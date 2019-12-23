<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
    header('Location: ../login_auth_one.php');
    exit();
} else {
    if (!($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor' || $_SESSION['role'] === 'SRT Analyst')) {
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
    <title>Quality Assurance</title>
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
            <img
                    src="./api/logo.png"
                    class="mr-3"
                    height="40"
            >
            <v-toolbar-title>Quality Assurance</v-toolbar-title>
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
                    class="filters"
            >
                <v-col
                        cols="12"
                        md="2"
                >
                    <v-autocomplete
                            v-model="selectedProject"
                            label="Select"
                            chips
                            hint="Select a Project"
                            persistent-hint
                            :items="projectArray"
                            item-text="name"
                            item-value="name"
                    >
                    </v-autocomplete>
                </v-col>
                <v-col
                        cols="6"
                        md="2"
                >
                    <v-autocomplete
                            v-model="selectedTickets"
                            label="Select"
                            chips
                            :items="ticketArray"
                            item-text="ticket_id"
                            item-value="ticket_id"
                            multiple
                    >
                        <template v-slot:selection="{ item, index }">
                            <v-chip v-if="index === 0">
                                <span>{{ item.ticket_id }}</span>
                            </v-chip>
                            <span
                                    v-if="index === 1"
                                    class="grey--text caption"
                            >(+{{ selectedTickets.length - 1 }} others)</span>
                        </template>
                    </v-autocomplete>
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
                <v-layout
                    justify-end
                >
                    <v-col
                            cols="6"
                            md="6"
                    >
                        <v-card
                                class="mx-auto"
                                outlined
                        >
                            <v-list-item>
                                <v-list-item-content>
                                    <div class="overline mb-4">Product Count</div>
                                    <v-row>
                                        <v-col>
                                            <v-list-item-title class="text-center">Ticket</v-list-item-title>
                                            <v-list-item-subtitle class="text-center">{{ selectedTickets.length }}</v-list-item-subtitle>
                                        </v-col>
                                        <v-col>
                                            <v-list-item-title class="text-center">Brand</v-list-item-title>
                                            <v-list-item-subtitle class="text-center">{{ pendingCount.brand }}</v-list-item-subtitle>
                                        </v-col>
                                        <v-col>
                                            <v-list-item-title class="text-center">SKU</v-list-item-title>
                                            <v-list-item-subtitle class="text-center">{{ pendingCount.sku }}</v-list-item-subtitle>
                                        </v-col>
                                        <v-col>
                                            <v-list-item-title class="text-center">DVC</v-list-item-title>
                                            <v-list-item-subtitle class="text-center">{{ pendingCount.dvc }}</v-list-item-subtitle>
                                        </v-col>
                                    <v-col>
                                            <v-list-item-title class="text-center">Facing</v-list-item-title>
                                            <v-list-item-subtitle class="text-center">{{ pendingCount.facing }}</v-list-item-subtitle>
                                        </v-col>
                                    </v-row>
                                </v-list-item-content>

                            </v-list-item>

                        </v-card>
                    </v-col>
                </v-layout>
            </v-row>
            <v-row
                    :align="'end'"
                    :justify="'start'"
                    class="filters"
                    v-if="productInfo.length > 0"
            >
                <v-col
                        cols="6"
                        md="2"
                >
                    <v-autocomplete
                            label="QA Status"
                            :items="qaStatusItems"
                            v-model="selectedQaStatus"
                            chips
                            deletable-chips
                    ></v-autocomplete>
                </v-col>
                <v-col
                        cols="6"
                        md="2"
                >
                    <v-autocomplete
                            label="Hunt Type"
                            :items="productHuntItems"
                            v-model="selectedHuntType"
                            chips
                            deletable-chips
                    ></v-autocomplete>
                </v-col>
                <v-col
                        cols="6"
                        md="2"
                >
                    <v-autocomplete
                            label="Product Type"
                            :items="productTypeItems"
                            v-model="selectedProductType"
                            chips
                            deletable-chips
                    ></v-autocomplete>
                </v-col>
                <v-col
                        cols="6"
                        md="2"
                >
                    <v-autocomplete
                            label="Brand"
                            :items="productBrandItems"
                            v-model="selectedBrand"
                            chips
                            deletable-chips
                    ></v-autocomplete>
                </v-col>
            </v-row>
            <v-row>
                <v-slide-y-transition>
                    <v-col
                            v-if="productInfo.length > 0"
                    >
                        <v-data-table
                                :headers="headers"
                                :items="filteredProducts"
                                class="elevation-1"
                                item-key="product_id"
                                :footer-props="{
                                    'items-per-page-options': [10]
                                }"
                                multi-sort
                                :search="search"
                        >
                            <template v-slot:top>
                                <v-toolbar flat>
                                    <v-toolbar-title>{{ selectedProject }} Product(s)</v-toolbar-title>
                                    <v-col cols="12" sm="3">
                                        <v-btn text icon color="green" @click="getProductInfo()">
                                            <v-icon>mdi-cached</v-icon>
                                        </v-btn>
                                    </v-col>
                                    <v-spacer></v-spacer>
                                    <v-text-field
                                            v-model="search"
                                            label="Product Name"
                                            single-line
                                            hide-details
                                    ></v-text-field>
                                </v-toolbar>
                            </template>
                            <template v-slot:item.action="{ item }">
                                <div class="my-2">
                                    <v-btn color="primary"
                                           :disabled="item.probe_being_handled === null
                                           || (item.probe_being_handled === '1' && item.assigned_user === '0')
                                           || (assigned === 1 && item.assigned_user !== '1')"
                                           @click="qaProduct(item)"
                                    >QA</v-btn>
                                </div>
                            </template>
                            <template v-slot:item.view="{ item }">
                                <div class="my-2">
                                    <v-btn
                                            color="dark"
                                            @click.stop="showProductHistory(item)"
                                    >Explore</v-btn>
                                </div>
                            </template>
                            <template v-slot:item.product_qa_status="{ item }">
                                <v-chip
                                        class="ma-2"
                                        :color="getStatusColor(item.product_qa_status)"
                                        label
                                        text-color="white"
                                >
                                    <v-icon left>mdi-label</v-icon>
                                    {{ item.product_qa_status }}
                                </v-chip>
                            </template>
                            <template v-slot:item.product_type="{ item }">
                                <v-chip dark>{{ item.product_type }}
                                </v-chip>
                            </template>
                            <template v-slot:item.product_hunt_type="{ item }">
                                <v-chip dark :color="getHuntTypeColor(item.product_hunt_type)">{{ item.product_hunt_type }}
                                </v-chip>
                            </template>
                        </v-data-table>
                </v-slide-y-transition>
                </v-col>
            </v-row>
            <v-dialog
                    v-model="historyDialog"
                    max-width="600"
            >
                <v-card>
                    <v-card-title class="headline">Product History</v-card-title>

                    <v-card-text>
                        <v-list-item two-line>
                            <v-list-item-content>
                                <v-list-item-subtitle>Product Source Link</v-list-item-subtitle>
                                <v-list-item-title><a :href="productHistory.product_link" target="_blank">{{ productHistory.product_link}}</a></v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item two-line>
                            <v-list-item-content>
                                <v-list-item-subtitle>Product Hunt Source</v-list-item-subtitle>
                                <v-list-item-title>{{ productHistory.hunt_source }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item two-line>
                            <v-list-item-content>
                                <v-list-item-subtitle>SRT QA DateTime</v-list-item-subtitle>
                                <v-list-item-title>{{ productHistory.product_qa_datetime}}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item two-line>
                            <v-list-item-content>
                                <v-list-item-subtitle>Product Previous Name</v-list-item-subtitle>
                                <v-list-item-title>{{ productHistory.product_previous}}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item two-line>
                            <v-list-item-content>
                                <v-list-item-subtitle>Product Previous Alt Name</v-list-item-subtitle>
                                <v-list-item-title>{{ productHistory.alt_design_previous}}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item two-line>
                            <v-list-item-content>
                                <v-list-item-subtitle>Product QA Errors</v-list-item-subtitle>
                                <v-list-item-title>{{ productHistory.qa_error}}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item two-line>
                            <v-list-item-content>
                                <v-list-item-subtitle>ODA QA DateTime</v-list-item-subtitle>
                                <v-list-item-title>{{ productHistory.product_oda_datetime}}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item two-line>
                            <v-list-item-content>
                                <v-list-item-subtitle>Product QA Previous Name</v-list-item-subtitle>
                                <v-list-item-title>{{ productHistory.product_qa_previous}}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item two-line>
                            <v-list-item-content>
                                <v-list-item-subtitle>Product QA Previous Alt Name</v-list-item-subtitle>
                                <v-list-item-title>{{ productHistory.product_alt_design_qa_previous}}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item two-line>
                            <v-list-item-content>
                                <v-list-item-subtitle>Product ODA Errors</v-list-item-subtitle>
                                <v-list-item-title>{{ productHistory.oda_error}}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item two-line>
                            <v-list-item-content>
                                <v-list-item-subtitle>Product ODA Comment</v-list-item-subtitle>
                                <v-list-item-title>{{ productHistory.product_oda_comment}}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>

                        <v-btn
                                color="green darken-1"
                                text
                                @click="historyDialog = false"
                        >
                            Close
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="qaDialog" max-width="800px">
                <v-card>
                    <v-card-title>
                        <span class="headline">{{ selectedProductInfo.projectName }} {{ selectedProductInfo.ticket_id }} {{ selectedProductInfo.product_hunt_type }} {{ selectedProductInfo.product_source }}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-form
                            >
                                <v-form
                                >
                                    <v-row>
                                        <v-col>
                                            <v-text-field
                                                    label="Product Name"
                                                    required
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>

                                    <v-row>
                                        <v-col>
                                            <v-text-field
                                                    label="Product Alt Name"
                                                    required
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>


                                    <v-row>
                                        <v-col>
                                            <v-select
                                                    label="Error Type"
                                                    required
                                            ></v-select>
                                        </v-col>
                                        <v-col>
                                            <v-slider
                                                    step="5"
                                                    ticks
                                                    thumb-label="always"
                                                    label="Facing"
                                            ></v-slider>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col>
                                            <v-radio-group row label="Status">
                                                <v-radio label="Approved" color="success"></v-radio>
                                                <v-radio label="Disapproved" color="red darken-3"></v-radio>
                                            </v-radio-group>
                                        </v-col>
                                    </v-row>

                                    <v-row>
                                        <v-col>
                                            <v-file-input
                                                    v-model="files"
                                                    color="deep-purple accent-4"
                                                    counter
                                                    label="Image Attachments"
                                                    multiple
                                                    placeholder="Select your files"
                                                    prepend-icon="mdi-paperclip"
                                                    outlined
                                                    :show-size="1000"
                                            >
                                                <template v-slot:selection="{ index, text }">
                                                    <v-chip
                                                            v-if="index < 2"
                                                            color="deep-purple accent-4"
                                                            dark
                                                            label
                                                            small
                                                    >
                                                        {{ text }}
                                                    </v-chip>

                                                    <span
                                                            v-else-if="index === 2"
                                                            class="overline grey--text text--darken-3 mx-2"
                                                    >
                                                        +{{ files.length - 2 }} File(s)
                                                    </span>
                                                </template>
                                            </v-file-input>
                                        </v-col>
                                    </v-row>

                                </v-form>
                        </v-container>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red darken-1" text @click="unassignProduct()">Close</v-btn>
                        <v-btn color="success darken-1" text @click="qaDialog = false">Save</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog
                    v-model="dialog"
                    hide-overlay
                    persistent
                    width="300"
            >
                <v-card
                        :color="selectedProductInfo.assignMessage === '' ? 'primary' : 'red'"
                        dark
                >
                    <v-card-text v-if="selectedProductInfo.assignMessage === ''">
                        Please stand by
                        <v-progress-linear
                                indeterminate
                                color="white"
                                class="mb-0"
                        ></v-progress-linear>
                    </v-card-text>
                    <section v-else>
                        <v-card-text>
                            {{ selectedProductInfo.assignMessage }}
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="white darken-1" text @click="dialog = false">Close</v-btn>
                        </v-card-actions>
                    </section>
                </v-card>
            </v-dialog>

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
            files: [],
            darkThemeSelected: false,
            dialog: false,
            qaDialog: false,
            projectArray: [],
            ticketArray: [],
            selectedProject: '',
            selectedTickets: [],
            productInfo: [],
            headers: [
                {text: 'Creation Date', value: 'product_creation_time', width: '10%', filterable: false},
                {text: 'Ticket ID', value: 'ticket_id', width: '10%', filterable: false},
                {text: 'Product Type', value: 'product_type', width: '10%', filterable: false},
                {text: 'Product Name', value: 'product_name', width: '25%'},
                {text: 'Product Alt Name', value: 'product_alt_design_name', width: '25%'},
                {text: 'Product QA Status', value: 'product_qa_status', width: '10%', filterable: false},
                {text: 'Hunt Type', value: 'product_hunt_type', width: '10%', filterable: false},
                {text: 'Product History', value: 'view', sortable: false, align: 'center', filterable: false},
                {text: 'Actions', value: 'action', sortable: false, align: 'center', filterable: false},
            ],
            ticketStatusOptions: ['OPEN', 'CLOSED', 'DONE', 'IN PROGRESS', 'IN PROGRESS / SEND TO EAN'],
            selectedTicketStatus: ['IN PROGRESS', 'IN PROGRESS / SEND TO EAN'],
            pendingCount: {
                brand: 0,
                sku: 0,
                dvc: 0,
                facing: 0
            },
            qaStatusItems: ['pending', 'approved', 'disapproved', 'active', 'rejected'],
            selectedQaStatus: 'pending',
            productHuntItems: ['probe', 'radar', 'reference'],
            selectedHuntType: '',
            productTypeItems:['brand', 'sku', 'dvc', 'facing'],
            selectedProductType: '',
            productBrandItems: [],
            selectedBrand: '',
            search: '',
            productHistory: {},
            historyDialog: false,
            selectedProductInfo: {
                projectName: '',
                ticket_id: '',
                product_hunt_type: '',
                product_source: '',
                productId: '',
                assignMessage: ''
            },
            assigned: 0,
        },
        methods: {
            getHuntTypeColor(hunt_type) {
                let color = '';
                if (hunt_type === 'probe') {
                    color = 'purple';
                } else if (hunt_type === 'radar') {
                    color = 'orange';
                } else {
                    color =  'blue';
                }
                return color;
            },
            getStatusColor(status) {
                let color = '';
                if (status === 'pending') {
                    color = 'orange';
                } else if (status === 'approved' || status === 'active') {
                    color = 'green';
                } else {
                    color =  'red';
                }
                return color;
            },
            getProjectList() {
                this.overlay = true;
                axios.get('api/fetch_project_list.php')
                    .then((response) => {
                        this.selectedTickets = [];
                        this.productInfo = [];
                        this.projectArray = response.data[0].project_info;
                        this.overlay = false;
                    });
            },
            getTicketList() {
                if (this.selectedProject !== '' && this.selectedTicketStatus.length > 0) {
                    this.overlay = true;
                    let formData = new FormData;
                    formData.append('db_name', this.selectedProject);
                    formData.append('status_array', this.selectedTicketStatus);
                    axios.post('api/fetch_project_ticket_list.php', formData)
                        .then((response) => {
                            this.selectedTickets = [];
                            this.productInfo = [];
                            this.ticketArray = response.data[0].ticket_info;
                            this.overlay = false;
                        });
                }
            },
            getProductInfo() {
                if (this.selectedProject !== '' && this.selectedTickets.length !== 0) {
                    this.overlay = true;
                    let formData = new FormData;
                    formData.append('db_name', this.selectedProject);
                    formData.append('ticket', this.selectedTickets);
                    axios.post('api/fetch_product_info.php', formData)
                        .then((response) => {
                            this.productInfo = [];
                            this.productBrandItems = [];
                            let count = 0;
                            response.data[0].product_info.forEach(item => {
                                this.productBrandItems[count] = item.brand_name;
                                count++;
                            });
                            this.assigned = response.data[0].row_count;
                            this.productInfo = response.data[0].product_info;
                            this.overlay = false;
                        });
                }
            },
            getPendingCount(data) {
                if (data.length === 0) {
                    this.pendingCount.brand = 0;
                    this.pendingCount.sku = 0;
                    this.pendingCount.dvc = 0;
                    this.pendingCount.facing = 0;
                } else {
                    let brand_count = 0;
                    let sku_count = 0;
                    let dvc_count = 0;
                    let facing_count = 0;

                    data.forEach(function(item) {
                        if (item.product_type === 'brand') {
                            brand_count++;
                        }
                        if (item.product_type === 'sku') {
                            sku_count++;
                        }
                        if (item.product_type === 'dvc') {
                            dvc_count++;
                        }
                        if (item.product_type === 'facing') {
                            facing_count++;
                        }
                    });

                    this.pendingCount.brand = brand_count;
                    this.pendingCount.sku = sku_count;
                    this.pendingCount.dvc = dvc_count;
                    this.pendingCount.facing = facing_count;

                }
            },
            showProductHistory(item) {
                this.productHistory.product_qa_datetime = item.product_qa_datetime;
                this.productHistory.product_previous = item.product_previous;
                this.productHistory.alt_design_previous = item.alt_design_previous;
                this.productHistory.qa_error = item.qa_error;
                this.productHistory.product_oda_datetime = item.product_oda_datetime;
                this.productHistory.product_qa_previous = item.product_qa_previous;
                this.productHistory.product_alt_design_qa_previous = item.product_alt_design_qa_previous;
                this.productHistory.oda_error = item.oda_error;
                this.productHistory.product_oda_comment = item.product_oda_comment;
                this.productHistory.product_link = item.product_link;
                if (item.probe_id !== null) {
                    this.productHistory.hunt_source = "PROBE " + item.probe_id;
                } else if (item.radar_source_link !== null) {
                    this.productHistory.hunt_source = item.radar_source_link;
                } else {
                    this.productHistory.hunt_source = "REF EAN " + item.reference_ean;
                }
                this.historyDialog = true;
            },
            qaProduct(item) {
                this.selectedProductInfo.projectName = this.selectedProject;
                this.selectedProductInfo.ticket_id = item.ticket_id;
                this.selectedProductInfo.product_hunt_type = item.product_hunt_type;
                this.selectedProductInfo.productId = item.product_id;
                this.selectedProductInfo.assignMessage = '';

                this.dialog = true;
                let formData = new FormData();
                formData.append('project_name', this.selectedProject);
                formData.append('product_id', this.selectedProductInfo.productId);
                axios.post('api/assign_product_qa.php', formData)
                    .then((response) => {
                        if (response.data[0].row_count === 1) {
                            this.dialog = false;
                            this.qaDialog = true;
                        } else if (response.data[0].row_count === 0 && response.data[0].already_assigned === 0){
                            this.selectedProductInfo.assignMessage = 'Product was taken by another QA';
                            this.getProductInfo();
                        }
                    });
            },
            unassignProduct() {
                let formData = new FormData();
                formData.append('project_name', this.selectedProject);

                axios.post('api/unassign_product_qa.php', formData)
                    .then((response) => {
                        if (response.data[0].error === '') {
                            this.qaDialog = false;
                            this.getProductInfo();
                        }
                    });
            }
        },
        watch: {
            darkThemeSelected: function (val) {
                this.$vuetify.theme.dark = val;
            },
            selectedProject: function () {
                this.getTicketList();
            },
            selectedTickets: function() {
                this.getProductInfo();
            },
            selectedTicketStatus: function() {
                this.productInfo = [];
                this.getTicketList();
            },
            productInfo: function() {
                this.getPendingCount(this.productInfo);
            },
        },
        created() {
            this.getProjectList();
        },
        computed: {
            filteredProducts() {
                let a = this.productInfo.filter((i) => {
                    return !this.selectedQaStatus || (i.product_qa_status === this.selectedQaStatus);
                });
                a = a.filter((i) => {
                    return !this.selectedHuntType || (i.product_hunt_type === this.selectedHuntType);
                });
                a = a.filter((i) => {
                    return !this.selectedProductType || (i.product_type === this.selectedProductType);
                });
                let count = 0;
                this.productBrandItems = [];
                a.forEach(item => {
                    this.productBrandItems[count] = item.brand_name;
                    count++;
                });
                a = a.filter((i) => {
                    return !this.selectedBrand ||
                        (i.product_name.substr(0, i.product_name.indexOf(" ")) === this.selectedBrand);
                });
                this.getPendingCount(a);
                return a;
            }

        }
    });
</script>
<style>
    .filters {
        margin-left: 0.5vw;
    }
</style>
</body>
</html>
