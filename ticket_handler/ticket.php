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
            <v-row>
                <v-col cols="12" sm="6">
                    <v-col>
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
                </v-col>
            </v-row>
            <v-row>
                <v-col>
                    <v-slide-y-transition>
                    <v-col
                        v-if="ticketInfo.length > 0"
                    >
                        <v-data-table
                                :headers="headers"
                                :items="ticketInfo"
                                class="elevation-1"
                        >
                            <template v-slot:top>
                                <v-toolbar flat>
                                    <v-toolbar-title>{{ selectedProjects.name }} Ticket(s)</v-toolbar-title>
                                    <v-divider
                                            class="mx-4"
                                            inset
                                            vertical
                                    ></v-divider>
                                    <v-spacer></v-spacer>
                                    <v-dialog v-model="dialog" max-width="800px">
                                        <template v-slot:activator="{ on }">
                                            <v-btn color="success" dark class="mb-2" v-on="on">New Ticket</v-btn>
                                        </template>
                                        <v-card>
                                            <v-card-title>
                                                <span class="headline">{{ formTitle }}</span>
                                            </v-card-title>

                                            <v-card-text>
                                                <v-container>
                                                    <v-col cols="12" sm="6" md="4">
                                                        <v-text-field v-model="editedItem.ticket_status" label="Ticket Status"></v-text-field>
                                                    </v-col>
                                                    <v-col cols="12" sm="6" md="4">
                                                        <v-text-field v-model="editedItem.ticket_comment" label="Ticket Comment"></v-text-field>
                                                    </v-col>
                                                    <v-col cols=
                                                </v-container>
                                            </v-card-text>

                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                                                <v-btn color="blue darken-1" text>Save</v-btn>
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
                        </v-data-table>
                        </v-slide-y-transition>
                    </v-col>
                </v-col>
            </v-row>
        </v-content>

    </v-app>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
<script src="https://cdn.jsdelivr.net/npm/http-vue-loader@1.4.1/src/httpVueLoader.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
<script>
    new Vue({
        el: "#app",
        vuetify: new Vuetify(),
        data: {
            darkThemeSelected: false,
            projectArray: [],
            selectedProjects: [],
            ticketInfo: [],
            headers: [
                {text: 'Date', value: 'create_date'},
                {text: 'Ticket Type', value: 'ticket_type'},
                {text: 'Ticket Description', value: 'ticket_description'},
                {text: 'Ticket ID', value: 'ticket_id'},
                {text: 'Status', value: 'ticket_status'},
                {text: 'Ticket Creator GID', value: 'account_gid'},
                {text: 'Ticket Comment', value: 'ticket_comment'},
                {text: 'Actions', value: 'action', sortable: false, align: 'center'},
            ],
            editedIndex: -1,
            editedItem:{},
            dialog: false,
        },
        methods: {
            getProjectList() {
                axios.get('api/fetch_project_list.php')
                    .then((response) => {
                        this.projectArray = response.data[0].project_info;
                    })
            },
            fetchTicketInfo() {
                this.ticketInfo = [];
                let formData = new FormData ();
                formData.append('project_id', this.selectedProjects.project_id);
                formData.append('project_name', this.selectedProjects.name);
                axios.post('api/fetch_ticket_info.php', formData)
                    .then((response) => {
                        this.ticketInfo = response.data[0].ticket_info;
                    })
            },
            close() {
                this.dialog = false;
                setTimeout(() => {
                    this.editedItem = {};
                    this.editedIndex = -1;
                }, 300);
            },
            editItem (item) {
                this.editedIndex = this.ticketInfo.indexOf(item);
                this.editedItem = Object.assign({}, item);
                this.dialog = true
            },
        },
        created() {
            this.getProjectList();
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
                return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
            },
        },
    });
</script>
</body>
</html>
