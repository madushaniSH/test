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
                    :justify="'space-between'"
            >
                <v-col
                        cols="12"
                        md="4"
                >
                    <v-autocomplete
                        v-if="projectArray.length > 0"
                        v-model="selectedProjects"
                        label="Select"
                        chips
                        hint="Select a Project to get started!"
                        persistent-hint
                        :items="projectArray"
                        item-text="name"
                        item-value="project_id"
                        return-object
                >
                </v-autocomplete>
                </v-col>
                <v-col
                        cols="12"
                        md="4"
                >
                    <v-dialog
                            ref="dateDialog"
                            v-model="menu"
                            :return-value.sync="dates"
                            persistent
                            width="290px"
                    >
                        <template v-slot:activator="{ on }">
                            <v-combobox
                                    v-model="dates"
                                    label="Ticket Creation Time"
                                    v-on="on"
                                    chips
                                    small-chips
                                    multiple
                            ></v-combobox>
                        </template>
                        <v-date-picker v-model="dates" range scrollable>
                            <v-spacer></v-spacer>
                            <v-btn text color="primary" @click="menu = false">Cancel</v-btn>
                            <v-btn text color="primary" @click="$refs.dateDialog.save(dates); fetchTicketInfo();">OK</v-btn>
                        </v-date-picker>
                    </v-dialog>
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
                                item-key="project_ticket_system_id"
                                v-model="selected"
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
                                                    <section v-if="editedIndex === -1 && selected.length === 0" >
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
                                                                        label = "Ticket Type"
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
                                                                    label = "Ticket Status"
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
                                                        <v-col cols="12" v-if="selected.length === 0 || editedIndex !== -1">
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
                                                                {{ editedItem.ticket_escalate === '1' ? 'De-escalate': 'Escalate' }}
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
                                <v-chip dark :color="getColor(item.ticket_escalate)">{{ getStatus(item.ticket_escalate) }}</v-chip>
                            </template>
                            <template v-slot:item.ticket_status="{ item }">
                                <v-chip dark :color="getColorStatus(item.ticket_status)" outlined>{{ item.ticket_status }}</v-chip>
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
                                <v-list-item-subtitle>Created Byy</v-list-item-subtitle>
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
                    :z-index="206"
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
            menu: false,
            modDialog: false,
            darkThemeSelected: false,
            valid: false,
            projectArray: [],
            selectedProjects: [],
            ticketInfo: [],
            dates: [],
            headers: [
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
            editedItem:{
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
            selectedIDArray: [],
            ticketRules: [
                v => !!v || 'Ticket ID is required',
                v => (v && v.length <= 255) || 'Ticket ID must be less than 255 characters'
            ],
            ticketTypeRules: [
                v => !!v || 'Ticket Type is required',
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
                axios.get('api/fetch_project_list.php')
                    .then((response) => {
                        this.projectArray = response.data[0].project_info;
                    });
            },
            fetchTicketInfo() {
                this.overlay = true;
                if (this.selectedProjects.length !== 0) {
                    this.ticketInfo = [];
                    let formData = new FormData();
                    formData.append('project_id', this.selectedProjects.project_id);
                    formData.append('project_name', this.selectedProjects.name);
                    formData.append('start_date', this.dates[0]);
                    formData.append('end_date', this.dates[1]);
                    axios.post('api/fetch_ticket_info.php', formData)
                        .then((response) => {
                            this.ticketInfo = response.data[0].ticket_info;
                            this.overlay = false;
                        });
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
            editItem (item) {
                this.editedIndex = this.ticketInfo.indexOf(item);
                this.editedItem = Object.assign({}, item);
                this.dialog = true
            },
            showModInfo (item) {
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
                    this.editedItem.ticket_escalate = '0':
                    this.editedItem.ticket_escalate = '1';
            },
            save() {
                this.overlay = true;
                if (this.selected.length !== 0 && this.editedIndex === -1) {
                    let formData = new FormData ();
                    formData.append('project_name', this.selectedProjects.name);
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
                            if (response.data[0].error_message === '') {
                                this.fetchTicketInfo();
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
                        let formData = new FormData ();
                        formData.append('project_name', this.selectedProjects.name);
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
                        let formData = new FormData ();
                        formData.append('project_name', this.selectedProjects.name);
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
            }
        },
        created() {
            this.getProjectList();
            this.initTicketDate();
        },
        watch: {
            darkThemeSelected: function (val) {
                this.$vuetify.theme.dark = val;
            },
            selectedProjects: function () {
                this.fetchTicketInfo();
            },
            dialog: function (val) {
                val || this.close();
            },
        },
        computed: {
            formTitle () {
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
        },
    });
</script>
</body>
</html>
