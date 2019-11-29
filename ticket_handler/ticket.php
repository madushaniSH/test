<?php
/*
    Filename: add_new_error.php
    Author: Malika Liyanage
*/
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
    header('Location: ../login_auth_one.php');
    exit();
} else {
    if (!($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor')) {
        header('Location: ../index.php');
        exit();
    }
}

// Current settings to connect to the user account database
require('../user_db_connection.php');
// Setting up the DSN
$dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
/*
    Attempts to connect to the databse, if no connection was estabishled
    kills the script
*/
try {
    // Creating a new PDO instance
    $pdo = new PDO($dsn, $user, $pwd);
    // setting the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // throws error message
    echo "<p>Connection to database failed<br>Reason: " . $e->getMessage() . '</p>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel='icon' href='../favicon.ico' type='image/x-icon'/>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Ticket Manager</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
</head>

<body>

<div id="app">
    <v-app>
        <v-app-bar
                app
                clipped-left
        >
            <v-toolbar-title>Ticket Manager</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-switch
                    v-model="darkThemeSelected"
                    :label="'Dark Mode'"
            ></v-switch>
        </v-app-bar>
        <v-content>
            <v-row
                    :align="'end'"
                    :justify="'start'"
                    id="filters"
            >
                <v-col
                        cols="6"
                        md="2"
                >
                    <v-autocomplete
                            v-model="selectedRegion"
                            label="Select"
                            chips
                            hint="Select a Region to get started!"
                            persistent-hint
                            :items="regionArray"
                    >
                    </v-autocomplete>
                </v-col>
                <v-col
                        cols="12"
                        md="3"
                >
                    <v-autocomplete
                            v-model="selectedProjects"
                            label="Select"
                            chips
                            hint="Select a Project"
                            persistent-hint
                            :items="projectArray"
                            item-text="name"
                            item-value="name"
                            multiple
                    >
                        <template v-slot:prepend-item>
                            <v-list-item
                                    ripple
                                    @click="toggleAllProjects"
                            >
                                <v-list-item-action>
                                    <v-icon :color="selectedProjects.length > 0 ? 'indigo darken-4' : ''">{{ icon }}
                                    </v-icon>
                                </v-list-item-action>
                                <v-list-item-content>
                                    <v-list-item-title>Select All</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-divider class="mt-2"></v-divider>
                        </template>
                        <template v-slot:selection="{ item, index }">
                            <v-chip v-if="index === 0">
                                <span>{{ item.name }}</span>
                            </v-chip>
                            <span
                                    v-if="index === 1"
                                    class="grey--text caption"
                            >(+{{ selectedProjects.length - 1 }} others)</span>
                        </template>
                    </v-autocomplete>
                </v-col>
                <v-col
                        cols="12"
                        md="3"
                >
                    <v-dialog
                            ref="dateDialog"
                            v-model="menu"
                            :return-value.sync="dates"
                            persistent
                            width="290px"
                    >
                        <template
                                v-slot:activator="{ on }"
                        >
                            <v-combobox
                                    v-model="dates"
                                    label="Ticket Creation Time"
                                    v-on="on"
                                    chips
                                    small-chips
                                    multiple
                                    :rules="[dateDifference <= 31 || 'Date Range has to be between within 31 Days']"
                            ></v-combobox>
                        </template>
                        <v-date-picker
                                v-model="dates"
                                range scrollable
                        >
                            <v-spacer></v-spacer>
                            <v-btn text color="primary" @click="menu = false">Cancel</v-btn>
                            <v-btn text color="primary" @click="$refs.dateDialog.save(dates); fetchTicketInfo();">OK
                            </v-btn>
                        </v-date-picker>
                    </v-dialog>
                </v-col>
                <v-col
                        cols="6"
                        md="3"
                >
                    <v-autocomplete
                            v-model="selectedTicketStatus"
                            label="Select"
                            chips
                            :items="ticketStatusOptions"
                            multiple
                    >
                        <template v-slot:selection="{ item, index }">
                            <v-chip v-if="index === 0">
                                <span>{{ item }}</span>
                            </v-chip>
                            <span
                                    v-if="index === 1"
                                    class="grey--text caption"
                            >(+{{ selectedTicketStatus.length - 1 }} others)</span>
                        </template>
                    </v-autocomplete>
                </v-col>
                <v-col
                        cols="6"
                        md="2"
                        v-if="ticketInfo.length > 0"
                >
                    <v-btn color="indigo" dark @click="exportTicketInfo">
                        <v-icon dark>mdi-cloud-download</v-icon>
                    </v-btn>
                </v-col>
            </v-row>
            <v-row>
                <v-slide-y-transition>
                    <v-col
                            v-if="ticketInfo.length > 0"
                    >
                        <v-data-table
                                :headers="computedHeaders"
                                :items="ticketInfo"
                                class="elevation-1"
                                show-select
                                v-model="selected"
                                item-key="index"
                        >
                            <template v-slot:top>
                                <v-toolbar flat>
                                    <v-toolbar-title>{{ selectedProjects.name }} Ticket(s)</v-toolbar-title>
                                    <v-col cols="12" sm="3">
                                        <v-btn text icon color="green" @click="fetchTicketInfo()">
                                            <v-icon>mdi-cached</v-icon>
                                        </v-btn>
                                    </v-col>
                                    <v-spacer></v-spacer>
                                    <v-dialog v-model="dialog" max-width="800px">
                                        <template v-slot:activator="{ on }">
                                            <v-btn
                                                    :color="selected.length === 0 ? 'purple' : 'info'"
                                                    dark class="mb-2"
                                                    v-on="on">
                                                {{ selected.length === 0 ? 'New Ticket' : 'Update' }}
                                            </v-btn>
                                        </template>
                                        <v-card>
                                            <v-card-title>
                                                <span class="headline">{{ formTitle }} {{ editedItem.ticket_id }}</span>
                                            </v-card-title>

                                            <v-card-text>
                                                <v-form
                                                        ref="form"
                                                        v-model="valid"
                                                        lazy-validation
                                                >
                                                    <v-container>
                                                        <section v-if="editedIndex === -1 && selected.length === 0">
                                                            <v-row>
                                                                <v-col
                                                                        cols="12"
                                                                        md="6"
                                                                >
                                                                    <v-autocomplete
                                                                            v-model="editedItem.project_name"
                                                                            label="Select"
                                                                            chips
                                                                            hint="Select a Project"
                                                                            persistent-hint
                                                                            :items="selectedProjects"
                                                                            :rules="projectRules"
                                                                    >
                                                                    </v-autocomplete>
                                                                </v-col>
                                                            </v-row>
                                                            <v-row>
                                                                <v-col
                                                                        cols="12"
                                                                        md="4"
                                                                >
                                                                    <v-text-field
                                                                            v-model.trim="editedItem.ticket_id"
                                                                            label="Ticket ID"
                                                                            :rules="ticketRules"
                                                                            required
                                                                    ></v-text-field>
                                                                </v-col>
                                                                <v-col
                                                                        cols="12"
                                                                        md="4"
                                                                >
                                                                    <v-select
                                                                            v-model="editedItem.ticket_type"
                                                                            label="Ticket Type"
                                                                            :items="ticketTypeOptions"
                                                                            :rules="ticketTypeRules"
                                                                            required
                                                                    ></v-select>
                                                                </v-col>
                                                            </v-row>
                                                        </section>
                                                        <v-row>
                                                            <v-col cols="12" sm="6" md="4">
                                                                <v-select
                                                                        v-model="editedItem.ticket_status"
                                                                        label="Ticket Status"
                                                                        :items="ticketStatusOptions"
                                                                        :rules="ticketStatusRules"
                                                                ></v-select>
                                                            </v-col>
                                                        </v-row>
                                                        <v-row v-if="editedIndex === -1 && selected.length === 0">
                                                            <v-col cols="12">
                                                                <v-text-field
                                                                        v-model.trim="editedItem.ticket_description"
                                                                        label="Ticket Description"
                                                                        :rules="ticketDescriptionRules"
                                                                        auto-grow
                                                                ></v-text-field>
                                                            </v-col>
                                                        </v-row>
                                                        <v-row>
                                                            <v-col cols="12"
                                                                   v-if="selected.length === 0 || editedIndex !== -1">
                                                                <v-text-field
                                                                        v-model.trim="editedItem.ticket_comment"
                                                                        label="Ticket Comment"
                                                                        auto-grow
                                                                ></v-text-field>
                                                            </v-col>
                                                        </v-row>
                                                        <v-row>
                                                            <v-col>
                                                                <v-btn
                                                                        :color="editedItem.ticket_escalate === '1' ? 'success': 'error'"
                                                                        @click="changeEscalateStatus()"
                                                                >
                                                                    {{ editedItem.ticket_escalate === '1' ?
                                                                    'De-escalate': 'Escalate' }}
                                                                </v-btn>
                                                            </v-col>
                                                        </v-row>
                                                    </v-container>
                                                </v-form>
                                            </v-card-text>

                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                                                <v-btn color="blue darken-1" text @click="save">Save</v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-dialog>
                                </v-toolbar>
                            </template>
                            <template v-slot:item.action="{ item }">
                                <v-icon
                                        small
                                        class="mr-2"
                                        @click="editItem(item)"
                                >
                                    mdi-account-edit
                                </v-icon>
                            </template>
                            <template v-slot:item.mod_info="{ item }">
                                <v-row justify="center">
                                    <v-btn
                                            color="info"
                                            @click.stop="showModInfo(item)"
                                    >
                                        View
                                    </v-btn>

                                </v-row>
                            </template>
                            <template v-slot:item.ticket_escalate="{ item }">
                                <v-chip dark :color="getColor(item.ticket_escalate)">{{ getStatus(item.ticket_escalate)
                                    }}
                                </v-chip>
                            </template>
                            <template v-slot:item.ticket_status="{ item }">
                                <v-chip dark :color="getColorStatus(item.ticket_status)" outlined>{{ item.ticket_status
                                    }}
                                </v-chip>
                            </template>
                        </v-data-table>
                </v-slide-y-transition>
                </v-col>
            </v-row>
            <v-dialog
                    v-model="modDialog"
                    max-width="400"
            >
                <v-card>
                    <v-card-title class="headline">Additional Information</v-card-title>

                    <v-card-text>
                        <v-list-item two-line>
                            <v-list-item-content>
                                <v-list-item-subtitle>Created By</v-list-item-subtitle>
                                <v-list-item-title>{{ modifyInfo.creator }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item two-line>
                            <v-list-item-content>
                                <v-list-item-subtitle>Last Modified By</v-list-item-subtitle>
                                <v-list-item-title>{{ modifyInfo.mod_gid }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item two-line>
                            <v-list-item-content>
                                <v-list-item-subtitle>Last Modified DateTime</v-list-item-subtitle>
                                <v-list-item-title>{{ modifyInfo.ticket_last_mod_date }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item two-line>
                            <v-list-item-content>
                                <v-list-item-subtitle>Completed DateTime</v-list-item-subtitle>
                                <v-list-item-title>{{ modifyInfo.ticket_completion_date }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item two-line>
                            <v-list-item-content>
                                <v-list-item-subtitle>Ticket Description</v-list-item-subtitle>
                                <v-list-item-title>{{ modifyInfo.description }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item two-line>
                            <v-list-item-content>
                                <v-list-item-subtitle>Ticket Comment</v-list-item-subtitle>
                                <v-list-item-title>{{ modifyInfo.comment }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>

                        <v-btn
                                color="green darken-1"
                                text
                                @click="modDialog = false"
                        >
                            Close
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-overlay
                    :value="overlay"
                    :z-index="220"
            >
                <v-progress-circular indeterminate size="64"></v-progress-circular>
            </v-overlay>
            <v-snackbar
                    v-model="snackbar"
                    :timeout="3000"
            >
                {{ displayMessage }}
                <v-btn
                        color="red"
                        text
                        @click="snackbar = false"
                >
                    Close
                </v-btn>
            </v-snackbar>
        </v-content>
        <v-bottom-navigation
                color="success"
        >
            <v-btn href="../dashboard.php">
                <span>Dashboard</span>
                <v-icon>mdi-home</v-icon>
            </v-btn>
        </v-bottom-navigation>
    </v-app>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
<script src="https://cdn.jsdelivr.net/npm/http-vue-loader@1.4.1/src/httpVueLoader.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js"></script>
<script>
    new Vue({
        el: "#app",
        vuetify: new Vuetify(),
        data: {
            selected: [],
            regionArray: ["AMER", "APAC", "DPG", "EMEA"],
            selectedRegion: '',
            selectedTicketStatus: ['IN PROGRESS', 'IN PROGRESS / SEND TO EAN'],
            menu: false,
            modDialog: false,
            darkThemeSelected: false,
            valid: false,
            projectArray: [],
            selectedProjects: [],
            ticketInfo: [],
            dates: [],
            headers: [
                {text: 'Project Name', value: 'project_name'},
                {text: 'Date', value: 'create_date'},
                {text: 'Ticket ID', value: 'ticket_id'},
                {text: 'Type', value: 'ticket_type'},
                {text: 'Status', value: 'ticket_status'},
                {text: 'Brands', value: 'brand_count', align: 'left'},
                {text: 'SKUs', value: 'sku_count', align: 'left'},
                {text: 'DVCs', value: 'dvc_count', align: 'left'},
                {text: 'Facings', value: 'facing_count', align: 'left'},
                {text: 'Additional Information', value: 'mod_info', width: '10%', align: 'center'},
                {text: 'Escalate Status', value: 'ticket_escalate', align: 'center'},
                {text: 'Actions', value: 'action', sortable: false, align: 'center'},
            ],
            headersToHide: ['Completion DateTime', 'Last Modified GID', 'Last Modified DateTime'],
            ticketStatusOptions: ['OPEN', 'CLOSED', 'DONE', 'IN PROGRESS', 'IN PROGRESS / SEND TO EAN'],
            ticketTypeOptions: ['APOC Radar', 'Radar', 'Data Health', 'Type E - SKU Hunt/Data Collection', 'NA'],
            editedIndex: -1,
            editedItem: {
                project_name: '',
                ticket_id: '',
                ticket_type: '',
                ticket_description: '',
                ticket_status: '',
                ticket_comment: '',
                ticket_escalate: '0',
                create_date: '',
                account_gid: '',
                ticket_completion_date: '',
                mod_gid: '',
                ticket_last_mod_date: '',
            },
            modifyInfo: {
                ticket_completion_date: '',
                mod_gid: '',
                ticket_last_mod_date: '',
                description: '',
                creator: '',
                comment: '',
            },
            dialog: false,
            overlay: false,
            displayMessage: '',
            snackbar: false,
            defaultItem: {
                project_name: '',
                ticket_id: '',
                ticket_type: '',
                ticket_description: '',
                ticket_status: '',
                ticket_comment: '',
                ticket_escalate: '0',
                create_date: '',
                account_gid: '',
                ticket_completion_date: '',
                mod_gid: '',
                ticket_last_mod_date: '',
            },
            dateDiff: 0,
            selectedIDArray: [],
            ticketRules: [
                v => !!v || 'Ticket ID is required',
                v => (v && v.length <= 255) || 'Ticket ID must be less than 255 characters'
            ],
            ticketTypeRules: [
                v => !!v || 'Ticket Type is required',
            ],
            projectRules: [
                v => !!v || 'Project is required',
            ],
            ticketStatusRules: [
                v => !!v || 'Ticket Status is required / Ignore for multi-update',
            ],
            ticketDescriptionRules: [
                v => !!v || 'Ticket Description is required',
                v => (v && v.length <= 500) || 'Ticket Description must be less than 500 characters'
            ],
        },
        methods: {
            getColor(status) {
                if (status === '1') {
                    return 'red'
                } else {
                    return 'green'
                }
            },
            getColorStatus(status) {
                if (status === 'IN PROGRESS' || status === 'IN PROGRESS / SEND TO EAN') {
                    return 'warning'
                } else if (status === 'DONE' || status === 'CLOSED') {
                    return 'success'
                } else {
                    return 'info'
                }
            },
            getStatus(status) {
                if (status === '1') {
                    return 'TRUE'
                } else {
                    return 'FALSE'
                }
            },
            getProjectList() {
                this.overlay = true;
                let formData = new FormData();
                formData.append('project_region', this.selectedRegion);
                axios.post('api/fetch_project_list.php', formData)
                    .then((response) => {
                        this.projectArray = response.data[0].project_info;
                        let count = 0;
                        this.selectedProjects = [];
                        this.projectArray.forEach(project => {
                            this.selectedProjects[count] = project.name;
                            count++;
                        });
                        this.overlay = false;
                    });
            },
            fetchTicketInfo() {
                if (this.selectedProjects.length !== 0 && this.dateDiff <= 31) {
                    this.overlay = true;
                    this.ticketInfo = [];
                    let formData = new FormData();
                    formData.append('project_array', this.selectedProjects);
                    formData.append('start_date', this.dates[0]);
                    formData.append('end_date', this.dates[1]);
                    formData.append('status_array', this.selectedTicketStatus);
                    axios.post('api/fetch_ticket_info.php', formData)
                        .then((response) => {
                            this.ticketInfo = response.data[0].ticket_info;
                            this.overlay = false;
                        });
                } else {
                    this.overlay = false;
                }
            },
            close() {
                this.dialog = false;
                setTimeout(() => {
                    this.$refs.form.resetValidation();
                    this.editedItem = Object.assign({}, this.defaultItem);
                    this.editedIndex = -1;
                }, 300);
            },
            editItem(item) {
                this.editedIndex = this.ticketInfo.indexOf(item);
                this.editedItem = Object.assign({}, item);
                this.dialog = true
            },
            showModInfo(item) {
                this.modifyInfo.mod_gid = item.mod_gid;
                this.modifyInfo.ticket_completion_date = item.ticket_completion_date;
                this.modifyInfo.ticket_last_mod_date = item.ticket_last_mod_date;
                this.modifyInfo.description = item.ticket_description;
                this.modifyInfo.creator = item.account_gid;
                this.modifyInfo.comment = item.ticket_comment;
                this.modDialog = true;
            },
            checkTicketInfoChanged(index) {
                return (this.ticketInfo[index].ticket_status !== this.editedItem.ticket_status)
                    || (this.ticketInfo[index].ticket_comment !== this.editedItem.ticket_comment)
                    || (this.ticketInfo[index].ticket_escalate !== this.editedItem.ticket_escalate);
            },
            changeEscalateStatus() {
                this.editedItem.ticket_escalate === '1' ?
                    this.editedItem.ticket_escalate = '0' :
                    this.editedItem.ticket_escalate = '1';
            },
            save() {
                this.overlay = true;
                if (this.selected.length !== 0 && this.editedIndex === -1) {
                    let formData = new FormData();
                    let projectArray = [];
                    for (let i = 0; i < this.selected.length; i++) {
                        projectArray[i] = this.selected[i].project_name;
                    }
                    formData.append('project_array', projectArray);
                    this.selectedIDArray = [];
                    for (let i = 0; i < this.selected.length; i++) {
                        this.selectedIDArray[i] = this.selected[i].project_ticket_system_id;
                    }
                    formData.append('selected', this.selectedIDArray);
                    let status = '';
                    this.editedItem.ticket_status === '' ? status = 'null' : status = this.editedItem.ticket_status;
                    formData.append('ticket_status', status);
                    formData.append('ticket_escalate', this.editedItem.ticket_escalate);
                    axios.post('api/multi_update_ticket_info.php', formData)
                        .then((response) => {
                            console.log(response);
                            if (response.data[0].error_message === '') {
                                this.fetchTicketInfo();
                                this.selected = [];
                                this.close();
                            } else {
                                this.displayMessage = response.data[0].error_message;
                                this.snackbar = true;
                            }
                            this.overlay = false;
                        });
                } else if (this.editedIndex > -1) {
                    // checking if changes were made that have to be commited to the db
                    if (this.checkTicketInfoChanged(this.editedIndex)) {
                        let formData = new FormData();
                        formData.append('project_name', this.editedItem.project_name);
                        formData.append('ticket_system_id', this.editedItem.project_ticket_system_id);
                        formData.append('ticket_status', this.editedItem.ticket_status);
                        formData.append('ticket_comment', this.editedItem.ticket_comment);
                        formData.append('ticket_escalate', this.editedItem.ticket_escalate);
                        axios.post('api/update_ticket_info.php', formData)
                            .then((response) => {
                                this.editedItem.mod_gid = response.data[0].update_info.gid;
                                this.editedItem.ticket_last_mod_date = response.data[0].update_info.date;
                                this.editedItem.ticket_completion_date = response.data[0].update_info.close_date;
                                Object.assign(this.ticketInfo[this.editedIndex], this.editedItem);
                                this.close();
                                this.overlay = false;
                            });

                    } else {
                        this.displayMessage = 'No changes were made';
                        this.snackbar = true;
                        this.overlay = false;
                    }
                } else {
                    if (!this.$refs.form.validate()) {
                        this.displayMessage = 'Errors in form';
                        this.snackbar = true;
                        this.overlay = false;
                    } else {
                        let formData = new FormData();
                        formData.append('project_name', this.editedItem.project_name);
                        formData.append('ticket_id', this.editedItem.ticket_id);
                        formData.append('ticket_type', this.editedItem.ticket_type);
                        formData.append('ticket_status', this.editedItem.ticket_status);
                        formData.append('ticket_description', this.editedItem.ticket_description);
                        formData.append('ticket_comment', this.editedItem.ticket_comment);
                        formData.append('ticket_escalate', this.editedItem.ticket_escalate);
                        axios.post('api/add_new_ticket.php', formData)
                            .then((response) => {
                                if (response.data[0].error_message === '') {
                                    this.editedItem.mod_gid = response.data[0].update_info.gid;
                                    this.editedItem.account_gid = response.data[0].update_info.gid;
                                    this.editedItem.ticket_last_mod_date = response.data[0].update_info.date;
                                    this.editedItem.ticket_completion_date = response.data[0].update_info.close_date;
                                    this.editedItem.create_date = response.data[0].update_info.create_date;
                                    this.ticketInfo.push(this.editedItem);
                                    this.close();
                                } else {
                                    this.displayMessage = response.data[0].error_message;
                                    this.snackbar = true;
                                }
                                this.overlay = false;
                            });
                    }
                }
            },
            initTicketDate() {
                this.dates[0] = moment().startOf('month').format('YYYY-MM-DD');
                this.dates[1] = moment().endOf('month').format('YYYY-MM-DD');
            },
            toggleAllProjects() {
                this.$nextTick(() => {
                    if (this.allProjects) {
                        this.selectedProjects = []
                    } else {
                        let count = 0;
                        this.selectedProjects = [];
                        this.projectArray.forEach(project => {
                            this.selectedProjects[count] = project.name;
                            count++;
                        });
                    }
                })
            },
            exportTicketInfo() {
                this.JSONToCSVConvertor(this.ticketInfo, "Ticket Export", true);
            },
            JSONToCSVConvertor(JSONData, ReportTitle, ShowLabel) {
                //If JSONData is not an object then JSON.parse will parse the JSON string in an Object
                var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;

                var CSV = '';
                //Set Report title in first row or line

                CSV += ReportTitle + '\r\n\n';

                //This condition will generate the Label/Header
                if (ShowLabel) {
                    var row = "";

                    //This loop will extract the label from 1st index of on array
                    for (var index in arrData[0]) {

                        //Now convert each value to string and comma-seprated
                        row += index + ',';
                    }

                    row = row.slice(0, -1);

                    //append Label row with line break
                    CSV += row + '\r\n';
                }

                //1st loop is to extract each row
                for (var i = 0; i < arrData.length; i++) {
                    var row = "";

                    //2nd loop will extract each column and convert it in string comma-seprated
                    for (var index in arrData[i]) {
                        row += '"' + arrData[i][index] + '",';
                    }

                    row.slice(0, row.length - 1);

                    //add a line break after each row
                    CSV += row + '\r\n';
                }

                if (CSV == '') {
                    alert("Invalid data");
                    return;
                }

                //Generate a file name
                var fileName = "MyReport_";
                //this will remove the blank-spaces from the title and replace it with an underscore
                fileName += ReportTitle.replace(/ /g, "_");

                //Initialize file format you want csv or xls
                var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);

                // Now the little tricky part.
                // you can use either>> window.open(uri);
                // but this will not work in some browsers
                // or you will not get the correct file extension

                //this trick will generate a temp <a /> tag
                var link = document.createElement("a");
                link.href = uri;

                //set the visibility hidden so it will not effect on your web-layout
                link.style = "visibility:hidden";
                link.download = fileName + ".csv";

                //this part will append the anchor tag and remove it after automatic click
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        },
        created() {
            this.initTicketDate();
        },
        watch: {
            darkThemeSelected: function (val) {
                this.$vuetify.theme.dark = val;
            },
            selectedProjects: function (val) {
                this.fetchTicketInfo();
            },
            dialog: function (val) {
                val || this.close();
            },
            selectedRegion: function () {
                this.projectArray = [];
                this.selected = [];
                this.selectedProjects = [];
                this.getProjectList();
            },
            selectedTicketStatus: function () {
                this.fetchTicketInfo();
            }
        },
        computed: {
            formTitle() {
                if (this.selected.length === 0) {
                    return this.editedIndex === -1 ? 'New Ticket' : 'Edit Ticket'
                } else {
                    return this.editedIndex === -1 ? 'Multi-Update' : 'Edit Ticket'
                }
            },
            computedHeaders() {
                return this.headers.filter(header =>
                    !header.hide
                );
            },
            dateDifference() {
                const date1 = new Date(this.dates[0]);
                const date2 = new Date(this.dates[1]);
                const timeDiff = Math.abs(date2.getTime() - date1.getTime());
                this.dateDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
                return Math.ceil(timeDiff / (1000 * 3600 * 24));
            },
            icon() {
                if (this.allProjects) return 'mdi-close-box';
                if (this.someProjects) return 'mdi-minus-box';
                return 'mdi-checkbox-blank-outline';
            },
            allProjects() {
                return this.selectedProjects.length === this.projectArray.length
            },
            someProjects() {
                return this.selectedProjects.length > 0 && !this.allProjects
            },
        },
    });
</script>
<style>
    #filters {
        margin-left: 0.5vw;
    }
</style>
</body>
</html>
