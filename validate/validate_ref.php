<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
    header('Location: ../login_auth_one.php');
    exit();
} else {
    if(!($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'ODA Supervisor' || $_SESSION['role'] === 'ODA')){
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
    <title>Reference Validation</title>
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
            <v-toolbar-title>Reference Validation</v-toolbar-title>
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
                        cols="12"
                        md="4"
                >
                    <v-file-input
                            v-model="files"
                            placeholder="Upload your documents"
                            label="Reference File input"
                    >
                        <template v-slot:selection="{ text }">
                            <v-chip
                                    small
                                    label
                                    color="primary"
                            >
                                {{ text }}
                            </v-chip>
                        </template>
                    </v-file-input>
                </v-col>
                <v-col
                        cols="12"
                        md="4"
                >
                    <v-file-input
                            v-model="eanFiles"
                            placeholder="Coming soon"
                            label="Product EAN File"
                            disabled
                    >
                        <template v-slot:selection="{ text }">
                            <v-chip
                                    small
                                    label
                                    color="primary"
                            >
                                {{ text }}
                            </v-chip>
                        </template>
                    </v-file-input>
                </v-col>
            </v-row>

            <v-row
                    :align="'end'"
                    :justify="'start'"
                    class="filters"
            >
                <v-col
                        cols="6"
                        md="2"
                >
                    <v-autocomplete
                            v-model="key"
                            label="Key"
                            chips
                            :items="refInfoHeaders"
                            :disabled="!refInfoHeaders.length > 0"
                    >
                    </v-autocomplete>
                </v-col>
                <v-col
                        cols="6"
                        md="2"
                >
                    <v-autocomplete
                            v-model="searchObjectArray[0].col"
                            label="Search Column"
                            chips
                            :items="refInfoHeaders"
                            :disabled="key === ''"
                    >
                    </v-autocomplete>
                </v-col>
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
                                :headers="productInfoHeaders"
                                :items="filteredProducts"
                                class="elevation-1"
                                item-key="product_id"
                                :footer-props="{
                                    'items-per-page-options': [10]
                                }"
                                multi-sort
                                :search="productInfoSearch"
                        >
                            <template v-slot:top>
                                <v-toolbar flat>
                                    <v-toolbar-title>{{ selectedProject }} Product(s)</v-toolbar-title>
                                    <v-col cols="12" sm="3">
                                        <v-btn text icon color="green" @click="getProductInfo()">
                                            <v-icon>mdi-cached</v-icon>
                                        </v-btn>
                                        <v-btn color="indigo" dark >
                                            <v-icon dark @click="exportProducts(filteredProducts)">mdi-cloud-download</v-icon>
                                        </v-btn>
                                    </v-col>
                                    <v-spacer></v-spacer>
                                    <v-text-field
                                            v-model="productInfoSearch"
                                            label="Product Name"
                                            single-line
                                            hide-details
                                    ></v-text-field>
                                </v-toolbar>
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
                            <template v-slot:item.view="{ item }">
                                <div class="my-2">
                                    <v-btn
                                            color="dark"
                                            @click.stop="showProductHistory(item)"
                                    >Explore</v-btn>
                                </div>
                            </template>
                            <template v-slot:item.product_hunt_type="{ item }">
                                <v-chip dark :color="getHuntTypeColor(item.product_hunt_type)">{{ item.product_hunt_type }}
                                </v-chip>
                            </template>
                            <template v-slot:item.action="{ item }">
                                <div class="my-2">
                                    <v-btn color="primary"
                                           :disabled=
                                           "
                                                item.product_ean_id !== null ||
                                                item.product_qa_status !== 'approved' ||
                                                item.product_type !== 'sku' ||
                                                (item.product_being_handled === '1' && item.assigned_user === '0') ||
                                                (assigned === 1 && item.assigned_user !== '1')
                                           "
                                           @click="openMatchDialog(item)"
                                    >Reference</v-btn>
                                </div>
                            </template>
                        </v-data-table>
                </v-slide-y-transition>
                </v-col>
            </v-row>

            <!--

            -->

        </v-content>

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

        <v-bottom-navigation
                color="success"
        >
            <v-btn href="../oda_dashboard.php">
                <span>Dashboard</span>
                <v-icon>mdi-home</v-icon>
            </v-btn>
        </v-bottom-navigation>

        <v-dialog
                v-model="overlay"
                hide-overlay
                persistent
                width="300"
        >
            <v-card
                    :color="assignMessage === '' ? 'primary' : 'red'"
                    dark
            >
                <v-card-text v-if="assignMessage === ''">
                    Please stand by
                    <v-progress-linear
                            indeterminate
                            color="white"
                            class="mb-0"
                    ></v-progress-linear>
                </v-card-text>
                <section v-else>
                    <v-card-text>
                        {{ assignMessage }}
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="white darken-1" text @click="overlay = false">Close</v-btn>
                    </v-card-actions>
                </section>
            </v-card>
        </v-dialog>


        <v-dialog
                v-model="productMatchDialog"
                fullscreen
                hide-overlay
                transition="dialog-bottom-transition"
                scrollable
        >
            <v-card tile>
                <v-toolbar
                        flat
                        dark
                        color="primary"
                >
                    <v-btn
                            icon
                            dark
                            @click="unassignProduct()"
                    >
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Product Reference Match</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn
                            @click="saveReference()"
                    >
                        Save
                    </v-btn>
                </v-toolbar>

                <v-card-text>
                    <v-row
                            align="start"
                            justify="start"
                            class="filters"
                    >
                        <v-col
                                cols="6"
                                md="4"
                        >
                            <v-text-field
                                    label="Item Code"
                                    v-model.trim="eanReferenceInformation.itemCode"
                                    counter="20"
                            >
                            </v-text-field>
                        </v-col>
                        <v-col
                                cols="6"
                                md="4"
                        >
                            <v-text-field
                                    label="EAN"
                                    v-model.trim="eanReferenceInformation.selectedEAN"
                                    counter="20"
                            >
                            </v-text-field>
                        </v-col>
                        <v-col
                                cols="6"
                                md="4"
                        >
                            <v-text-field
                                    label="Additional Comment"
                                    v-model.trim="eanReferenceInformation.additonalComment"
                            >
                            </v-text-field>
                        </v-col>
                    <!--- Web Link with ability to add links -->
                    </v-row>
                    <v-row
                            align="start"
                            justify="start"
                            class="filters"
                    >
                        <v-col
                                cols="12"
                                md="4"
                        >
                            <v-autocomplete
                                    label="Unmatch Reason"
                                    :disabled="eanReferenceInformation.selectedEAN !== ''"
                                    :items="unmatchReasons"
                                    item-text="unmatch_reason"
                                    return-object
                                    v-model="eanReferenceInformation.selectedUnmatchReason"
                                    clearable
                            >
                            </v-autocomplete>
                        </v-col>
                        <v-col
                                cols="6"
                                md="8"
                        >
                            <v-text-field
                                    label="Duplicate with Product Name"
                                    :disabled="!(eanReferenceInformation.selectedUnmatchReason.unmatch_reason_id > 13 && eanReferenceInformation.selectedUnmatchReason.unmatch_reason_id < 18)"
                                    v-model.trim="eanReferenceInformation.duplicateProductName"
                            >
                            </v-text-field>
                        </v-col>
                    </v-row>
                    <section v-for="(item, index) in eanReferenceInformation.weblinks">
                        <v-row
                                align="start"
                                justify="start"
                                class="filters"
                        >
                            <v-col
                                md="12"
                            >
                                <v-text-field
                                        :label="`Weblink ${index+1}`"
                                        v-model.trim="item.link"
                                >
                                    <v-icon
                                            slot="prepend-inner"
                                            fab dark color="blue" @click="addNewWebLink()"
                                            :disabled="item.link === ''"
                                    >
                                        mdi-plus
                                    </v-icon>
                                    <v-icon
                                            slot="append"
                                            fab dark color="red" @click="removeWebLink(index)"
                                            :disabled="index === 0"
                                    >
                                        mdi-minus
                                    </v-icon>
                                </v-text-field>
                            </v-col>
                        </v-row>
                    </section>
                    <v-row
                            align="start"
                            justify="start"
                            class="filters"
                    >
                        <v-col cols="6" md="5" v-if="searchObjectArray[0].col !== ''">
                            <v-text-field label="Product Name" v-model.trim="searchObjectArray[0].value"></v-text-field>
                            <v-btn color="success" @click="matchData">Search</v-btn>
                            <v-btn color="primary" class="ma-2" dark
                                :disabled="files === null || searchObjectArray[0].value === ''" @click="dialog = true">
                                Search Options
                            </v-btn>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col>
                            <v-data-table
                                    :headers="headers"
                                    :items="matchInfo"
                                    :sort-by="['per']"
                                    :sort-desc="[true]"
                                    v-if="matchInfo.length > 0"
                                    :search="search"
                            >
                                <template v-slot:top>
                                    <v-toolbar flat>
                                        <v-toolbar-title>Result(s)</v-toolbar-title>
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
                                        <v-btn color="success"
                                               @click="eanReferenceInformation.selectedEAN = item.key"
                                        >Match</v-btn>
                                    </div>
                                </template>


                                <template v-slot:item.key="{ item }">
                                    <v-btn text outlined color="info" :href="upcLink(item.key)" target="_blank">
                                        <v-icon left>mdi-link</v-icon>
                                        {{ item.key }}
                                    </v-btn>
                                </template>
                            </v-data-table>
                        </v-col>
                    </v-row>
                </v-card-text>

            </v-card>
        </v-dialog>



        <v-dialog
                v-model="dialog"
                max-width="1000px"
        >
            <v-card>
                <v-card-title>
                    Search Options
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col
                                    cols="11"
                            >
                                <v-autocomplete
                                        :items="refInfoHeaders"
                                        v-model="key"
                                        label="Select Key"
                                >
                                </v-autocomplete>
                            </v-col>
                            <v-col
                                    cols="1"
                            >
                                <v-btn fab dark small color="indigo" @click="addNewSearch()">
                                    <v-icon dark>mdi-plus</v-icon>
                                </v-btn>
                            </v-col>
                        </v-row>
                        <section v-for="(item, index) in searchObjectArray">
                            <v-row>
                                <v-col>
                                    <v-autocomplete
                                            :items="refInfoHeaders"
                                            v-model="item.col"
                                            label="Search Column"
                                    >
                                    </v-autocomplete>
                                </v-col>
                                <v-col>
                                    <v-text-field
                                            label="Value"
                                            v-model.trim="item.value"
                                            v-if="index === 0"
                                    >
                                    </v-text-field>
                                    <v-autocomplete
                                            :items="refDropDown(index)"
                                            v-model="item.value"
                                            label="Value"
                                            v-else
                                    >
                                        <template slot="append-outer">
                                           <v-btn class="mx-2" fab dark small color="red" :disabled="index === 0" @click="removeSearch(index)">
                                                <v-icon dark>mdi-minus</v-icon>
                                            </v-btn>
                                        </template>
                                    </v-autocomplete>
                                </v-col>
                            </v-row>
                        </section>
                    </v-container>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red darken-1" text @click="dialog = false">Close</v-btn>
                    <v-btn color="success darken-1" :disabled="!checkSearchValues" @click="matchData()" text>Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-app>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
<script src="https://cdn.jsdelivr.net/npm/http-vue-loader@1.4.1/src/httpVueLoader.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js"></script>
<script src="https://unpkg.com/papaparse@5.1.1/papaparse.min.js"></script>
<script>
    new Vue({
        el: "#app",
        vuetify: new Vuetify(),
        data: {
            darkThemeSelected: false,
            files: null,
            eanFiles: null,
            refInfo: [],
            refInfoHeaders: [],
            overlay: false,
            searchObjectArray: [],
            key: '',
            search: '',
            productName: '',
            orgProductName: '',
            headers: [
                { text: 'Key', value: 'key' , width: '10%'},
                { text: 'Column', value: 'col' },
                { text: 'Match Percentage', value: 'per' },
                {text: 'Actions', value: 'action', sortable: false, align: 'center', filterable: false},
            ],
            productInfo: [],
            productInfoHeaders: [
                {text: 'Creation Date', value: 'product_creation_time', width: '10%', filterable: false},
                {text: 'Ticket ID', value: 'ticket_id', width: '10%', filterable: false},
                {text: 'Type', value: 'product_type', width: '5%', filterable: false},
                {text: 'Product Name', value: 'product_name', width: '25%'},
                {text: 'Product Alt Name', value: 'product_alt_design_name', width: '25%'},
                {text: 'Product QA Status', value: 'product_qa_status', width: '5%', filterable: false},
                {text: 'Hunt Type', value: 'product_hunt_type', width: '10%', filterable: false},
                {text: 'EAN', value: 'product_ean', width: '10%'},
                {text: 'Product History', value: 'view', sortable: false, align: 'center', filterable: false},
                {text: 'Actions', value: 'action', sortable: false, align: 'center', filterable: false},
            ],
            matchInfo: [],
            dialog: false,
            projectArray: [],
            ticketArray: [],
            selectedProject: '',
            selectedTickets: [],
            selectedTicketStatus: ['IN PROGRESS / SEND TO EAN'],
            qaStatusItems: ['pending', 'approved', 'disapproved', 'active', 'rejected'],
            selectedQaStatus: 'approved',
            productHuntItems: ['probe', 'radar', 'reference'],
            selectedHuntType: '',
            productTypeItems: ['brand', 'sku', 'dvc', 'facing'],
            selectedProductType: 'sku',
            productBrandItems: [],
            selectedBrand: '',
            productInfoSearch: '',
            productHistory: {},
            historyDialog: false,
            productMatchDialog: false,
            assigned: 0,
            assignMessage: '',
            eanReferenceInformation: {
                selectedEAN: '',
                productName: '',
                selectedUnmatchReason: {
                    unmatch_reason: '',
                    unmatch_reason_id: ''
                },
                weblinks: [],
                productId: '',
                additonalComment: '',
                itemCode: '',
                duplicateProductName: '',
            },
            refObject: {
                selectedEAN: '',
                productName: '',
                selectedUnmatchReason: {
                    unmatch_reason: '',
                    unmatch_reason_id: ''
                },
                weblinks: [{
                    link: ''
                }],
                productId: '',
                additonalComment: '',
                duplicateProductName: '',
                itemCode: '',
            },
            unmatchReasons: [],
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
            processData(file) {
                this.overlay = true;
                Papa.parsePromise = function(file) {
                    return new Promise(function(complete, error) {
                        Papa.parse(file, {worker: true, header:true,complete, error});
                    });
                };
                Papa.parsePromise(file).then((results) => {
                    this.refInfo = results.data;
                    this.refInfoHeaders = results.meta['fields'];
                    this.overlay = false;
                });
            },

            matchData() {
                const refInfoLength = this.refInfo.length;
                const maxRows = 10000;
                let matchArray = [];
                let total = 0;
                let row = "";
                let upc = '';

                this.overlay = true;
                this.eanReferenceInformation.productName = this.searchObjectArray[0].value;

                // init array
                for (let i = 0; i < refInfoLength; ++i) {
                    row = this.refInfo[i];
                    matchArray[i] = {
                        "key": row[this.key],
                        "col": row[this.searchObjectArray[0].col],
                        "totalPer": 0,
                        "per": 0
                    }
                }

                this.searchObjectArray.forEach((item, index) => {
                    for (let i = 0; i < refInfoLength; ++i) {
                        row = this.refInfo[i];
                        if (row[item.col] !== undefined) {
                            total = parseFloat((similarity(item.value, row[item.col]) * 100).toFixed(2));
                            matchArray[i].totalPer = parseFloat(total) + parseFloat(matchArray[i].totalPer);
                        }
                    }
                });

                for (let i = 0; i < refInfoLength; ++i) {
                    matchArray[i].per = (matchArray[i].totalPer / this.searchObjectArray.length).toFixed(2);
                }

                matchArray.sort((a, b) => (a.per < b.per ? 1 : -1)); // sorts array based on match percentage
                let temp = matchArray;
                if (refInfoLength > maxRows) {
                    matchArray = temp.slice(0, maxRows);
                }

                this.searchObjectArray.splice(1);
                this.matchInfo = matchArray;
                this.dialog = false;
                this.overlay = false;
            },
            addNewSearch() {
                const newObject = {
                    col: '',
                    value: ''
                };
                this.searchObjectArray.push(newObject);
            },
            addNewWebLink() {
                const webLinkObject = {
                    link: ''
                };
                this.eanReferenceInformation.weblinks.push(webLinkObject);
            },
            removeWebLink(index) {
                this.eanReferenceInformation.weblinks.splice(index, 1);
            },
            removeSearch(index) {
                this.searchObjectArray.splice(index, 1);
            },
            upcLink(upc) {
                return `https://www.upcitemdb.com/query?upc=${upc}&type=4`;
            },
            refDropDown(index) {
                let returnArray = [];
                if (this.searchObjectArray[index] !== undefined) {
                    let count = 0;
                    this.refInfo.forEach(item => {
                        let value = item[this.searchObjectArray[index].col];
                        if (value !== undefined || value !== '') {
                            returnArray[count] = value;
                            count++;
                        }
                    });
                }
                return returnArray;
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
                        });
                }
            },
            stringCheck(string) {
                if (string === null) {
                    return '';
                } else {
                    return string;
                }
            },
            exportProducts(data) {
                if (data.length > 0) {
                    let exportData = [];
                    data.forEach((item, index) => {
                        let source = '';
                        if (item.probe_id !== null) {
                            source = "PROBE " + item.probe_id;
                        } else if (item.radar_source_link !== null) {
                            source = item.radar_source_link;
                        } else {
                            source = "REF EAN " + item.reference_ean;
                        }
                        exportData[index] = {
                            "Creation Data": this.stringCheck(item.product_creation_time),
                            "Product EAN": this.stringCheck(item.product_ean),
                            "Ticket ID": this.stringCheck(item.ticket_id),
                            "Product Type": this.stringCheck(item.product_type.toUpperCase()),
                            "Product Name": this.stringCheck(item.product_name),
                            "Product Alt Name": this.stringCheck(item.product_alt_design_name),
                            "Product QA Status": this.stringCheck(item.product_qa_status),
                            "Product Item Code": this.stringCheck(item.product_item_code),
                            "Product Additional Comment": this.stringCheck(item.additional_comment),
                            "Product Duplicate with": this.stringCheck(item.duplicate_product_name),
                            "Unmatch Reason": this.stringCheck(item.unmatch_reason),
                            "Web Links": this.stringCheck(item.weblink),
                            "Facing Count": this.stringCheck(item.product_facing_count),
                            "Hunt Type": this.stringCheck(item.product_hunt_type.toUpperCase()),
                            "Product Source Link": this.stringCheck(item.product_link),
                            "Product Hunt Source": this.stringCheck(source),
                            "SRT QA DateTime": this.stringCheck(item.product_qa_datetime),
                            "Product Previous Name": this.stringCheck(item.product_previous),
                            "Product Previous Alt Name": this.stringCheck(item.product_alt_design_previous),
                            "Product QA Errors": this.stringCheck(item.qa_error),
                            "Product ODA DateTime": this.stringCheck(item.product_oda_datetime),
                            "Product QA Previous Name": this.stringCheck(item.product_qa_previous),
                            "Product QA Previous Alt Name": this.stringCheck(item.product_alt_design_qa_previous),
                            "Product ODA Errors": this.stringCheck(item.oda_error),
                            "Product ODA Comment": this.stringCheck(item.product_oda_comment),
                        }
                    });
                    this.JSONToCSVConvertor(exportData, "Product Export", true);
                }
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
            openMatchDialog(item) {
                this.searchObjectArray[0].value = item.product_name;
                this.eanReferenceInformation.productId = item.product_id;
                this.assignMessage = '';
                let formData = new FormData();
                formData.append('project_name', this.selectedProject);
                formData.append('product_id', item.product_id);
                axios.post('api/assign_product_oda_ref.php', formData)
                    .then((response) => {
                        if (response.data[0].row_count === 1) {
                            this.matchData();
                            this.productMatchDialog = true;
                        } else if (response.data[0].row_count === 0 && response.data[0].already_assigned === 1){
                            this.assignMessage = 'Product was taken by another QA';
                            this.getProductInfo();
                            this.overlay = true;
                        }
                    })
                    .catch(() => {
                        location.reload();
                    });
            },
            unassignProduct() {
                let formData = new FormData();
                formData.append('project_name', this.selectedProject);

                axios.post('api/unassign_product_oda_ref.php', formData)
                    .then((response) => {
                        if (response.data[0].error === '') {
                            this.productMatchDialog = false;
                            this.resetReferenceObject();
                            this.getProductInfo();
                        }
                    })
                    .catch(() => {
                        location.reload();
                    });
            },
            getUnmatchReasons() {
                if (this.selectedProject !== '') {
                    let formData = new FormData;
                    formData.append('project_name', this.selectedProject);
                    axios.post('api/fetch_unmatch_reason.php', formData)
                        .then((response) => {
                            this.unmatchReasons = response.data[0].error_rows;
                        });
                }
            },
            saveReference() {
                const productId = this.eanReferenceInformation.productId.trim();
                const selectedEAN = this.eanReferenceInformation.selectedEAN.trim();
                let unmatchReasonId= this.eanReferenceInformation.selectedUnmatchReason.unmatch_reason_id.trim();
                const itemCode = this.eanReferenceInformation.itemCode.trim();
                const additionalComment = this.eanReferenceInformation.additonalComment.trim();
                let duplicateProductName = this.eanReferenceInformation.duplicateProductName.trim();
                if (selectedEAN !== '') {
                    unmatchReasonId = '';
                    duplicateProductName = '';
                }
                let webLinks = [];
                let index = 0;
                this.eanReferenceInformation.weblinks.forEach(item => {
                    webLinks[index] = item.link;
                    index++;
                });
                 if (selectedEAN !== '' || unmatchReasonId !== '') {
                     let formData = new FormData;
                     formData.append('project_name', this.selectedProject);
                     formData.append('productId', productId);
                     formData.append('selectedEAN', selectedEAN);
                     formData.append('unmatchReasonId', unmatchReasonId);
                     formData.append('duplicateProductName', duplicateProductName);
                     formData.append('itemCode', itemCode);
                     formData.append('additionalComment', additionalComment);
                     formData.append('webLinks', webLinks);
                     axios.post('api/save_reference_ean.php', formData)
                         .then((response) => {
                             console.log(response)
                             this.resetReferenceObject();
                             this.getProductInfo();
                             this.productMatchDialog = false;
                         })
                         .catch(() => {
                             location.reload();
                         });
                 }
            },
            resetReferenceObject() {
                Object.assign(this.eanReferenceInformation, {});
                Object.assign(this.eanReferenceInformation, this.refObject);
            }
        },
        watch: {
            darkThemeSelected: function (val) {
                this.$vuetify.theme.dark = val;
            },
            files: function(val) {
                if (val !== null) {
                    this.refInfo = this.processData(val);
                }
            },
            selectedProject: function () {
                this.getTicketList();
                this.getUnmatchReasons();
                this.addNewWebLink();
            },
            selectedTickets: function() {
                this.getProductInfo();
            },
        },
        created() {
            this.addNewSearch();
            this.getProjectList();
        },
        computed: {
            checkSearchValues() {
                let valid = false;
                for (let i = 0; i < this.searchObjectArray.length; i++) {
                    this.searchObjectArray[i].col === '' || this.searchObjectArray[i].value === '' ?
                        valid = false : valid = true;
                }
                return valid;
            },
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
                return a;
            },
        }
    });
    // FROM STACK OVER FLOW https://stackoverflow.com/questions/10473745/compare-strings-javascript-return-of-likely
    function similarity(s1, s2) {
        var longer = s1;
        var shorter = s2;
        if (s1.length < s2.length) {
            longer = s2;
            shorter = s1;
        }
        var longerLength = longer.length;
        if (longerLength == 0) {
            return 1.0;
        }
        return (longerLength - editDistance(longer, shorter)) / parseFloat(longerLength);
    }

    function editDistance(s1, s2) {
        s1 = s1.toLowerCase();
        s2 = s2.toLowerCase();

        var costs = new Array();
        for (var i = 0; i <= s1.length; i++) {
            var lastValue = i;
            for (var j = 0; j <= s2.length; j++) {
                if (i == 0)
                    costs[j] = j;
                else {
                    if (j > 0) {
                        var newValue = costs[j - 1];
                        if (s1.charAt(i - 1) != s2.charAt(j - 1))
                            newValue = Math.min(Math.min(newValue, lastValue),
                                costs[j]) + 1;
                        costs[j - 1] = lastValue;
                        lastValue = newValue;
                    }
                }
            }
            if (i > 0)
                costs[s2.length] = lastValue;
        }
        return costs[s2.length];
    }

</script>
<style>
    .filters {
        margin-left: 0.5vw;
    }
</style>
</body>
</html>
