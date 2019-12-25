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
                        <v-row>
                            <v-col>
                        <span class="body-1 font-weight-regular">
                            {{ selectedProductInfo.projectName }} {{ selectedProductInfo.ticket_id }} {{ selectedProductInfo.product_hunt_type.toUpperCase() }} {{ selectedProductInfo.product_source }} {{ selectedProductInfo.titleString }}
                        </span>
                            </v-col>
                        </v-row>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-tabs
                                    v-model="tab"
                                    dark
                            >
                                <v-tabs-slider></v-tabs-slider>

                                <v-tab
                                    key="qaForm"
                                >
                                    QA Form
                                </v-tab>
                                <v-tab
                                    key="sourceInfo"
                                    v-if="selectedProductInfo.productHuntType === 'reference'"
                                >
                                    Source Information
                                </v-tab>

                                <v-tab-item
                                    key="qaForm"
                                >
                                    <v-form
                                            v-model="valid"
                                            ref="form"
                                    >
                                        <v-row>
                                            <v-col v-if="selectedProductInfo.productLink !== null" cols="12" md="4">
                                                <v-btn text outlined color="info" :href="selectedProductInfo.productLink" target="_blank">
                                                    <v-icon left>mdi-link</v-icon>
                                                    Product Source Link
                                                </v-btn>
                                            </v-col>
                                            <v-col v-if="selectedProductInfo.radarSource !== ''" cols="12" md="5">
                                                <v-btn text outlined color="info" :href="selectedProductInfo.radarSource" target="_blank">
                                                    <v-icon left>mdi-link</v-icon>
                                                    Suggestion Source Link
                                                </v-btn>
                                            </v-col>
                                        </v-row>
                                        <v-row v-if="selectedProductInfo.productName !== null">
                                            <v-col>
                                                <v-text-field
                                                        label="Product Name"
                                                        required
                                                        v-model.trim="selectedProductInfo.productName"
                                                        :rules="productNameRules"
                                                ></v-text-field>
                                                <v-alert
                                                        border="right"
                                                        colored-border
                                                        type="warning"
                                                        elevation="2"
                                                        v-if="selectedProductInfo.productName !== selectedProductInfo.productNameOrg"
                                                >
                                                    Warning, Product Name will be overwritten
                                                </v-alert>
                                            </v-col>
                                        </v-row>

                                        <v-row v-if="selectedProductInfo.productAltName !== null">
                                            <v-col>
                                                <v-text-field
                                                        label="Product Alt Name"
                                                        required
                                                        v-model.trim="selectedProductInfo.productAltName"
                                                        :rules="productAltNameRules"
                                                ></v-text-field>
                                                <v-alert
                                                        border="right"
                                                        colored-border
                                                        type="warning"
                                                        elevation="2"
                                                        v-if="selectedProductInfo.productAltName !== selectedProductInfo.productAltNameOrg"
                                                >
                                                    Warning, Product Alt Name will be overwritten
                                                </v-alert>
                                            </v-col>
                                        </v-row>

                                        <v-row v-if="selectedProductInfo.manuLink !== null">
                                            <v-col>
                                                <v-text-field
                                                        label="Manufacturer Source Link"
                                                        v-model.trim="selectedProductInfo.manuLink"
                                                        :rules="manuLinkRules"
                                                ></v-text-field>
                                            </v-col>
                                        </v-row>

                                        <v-row>
                                            <v-col>
                                                <v-autocomplete
                                                        :items="qaErrors"
                                                        item-text="project_error_name"
                                                        item-value="project_error_id"
                                                        v-model="selectedQaErrors"
                                                        label="Select Item"
                                                        multiple
                                                        clearable
                                                        :rules="[ errorTypeValidate ]"
                                                >
                                                    <template v-slot:selection="{ item, index }">
                                                        <v-chip v-if="index === 0">
                                                            <span>{{ item.project_error_name }}</span>
                                                        </v-chip>
                                                        <span
                                                                v-if="index === 1"
                                                                class="grey--text caption"
                                                        >(+{{ selectedQaErrors.length - 1 }} others)</span>
                                                    </template>
                                                </v-autocomplete>
                                            </v-col>
                                            <v-col>
                                                <v-btn
                                                        color="red"
                                                        dark
                                                        class="ma-2"
                                                        @click="newErrorDialog = true"
                                                >
                                                    <v-icon left>mdi-pencil</v-icon>Add Error
                                                </v-btn>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col cols="12" md="6">
                                                <v-slider
                                                        step="1"
                                                        ticks
                                                        label="Facing"
                                                        v-model="selectedProductInfo.productFacingCount"
                                                        min="0"
                                                        max="5"
                                                        tick-size="5"
                                                >
                                                    <template v-slot:append>
                                                        <span
                                                                class="mt-0 pt-0 info--text"
                                                        >
                                                            {{ selectedProductInfo.productFacingCount }}
                                                        </span>
                                                    </template>
                                                </v-slider>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col>
                                                <v-radio-group row label="Status"
                                                               v-model="selectedProductInfo.qaStatus"
                                                               :rules="qaStatusRules"
                                                >
                                                    <v-radio label="Approved" color="success" value="approved"></v-radio>
                                                    <v-radio label="Disapproved" color="red darken-3" value="disapproved"></v-radio>
                                                </v-radio-group>
                                            </v-col>
                                        </v-row>

                                        <v-row v-if="selectedQaErrors.length > 0">
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
                                                        :rules="[ fileTypeValidate ]"
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
                                </v-tab-item>

                                <v-tab-item
                                        key="sourceInfo"
                                >
                                    <v-row>
                                        <v-col
                                                cols="12"
                                                md="2"
                                        >
                                            <v-text-field
                                                    label="Level"
                                                    outlined
                                                    readonly
                                                    v-model="selectedProductInfo.refInfo.reference_recognition_level"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                                cols="12"
                                                md="10"
                                        >
                                            <v-text-field
                                                    label="Short Name"
                                                    outlined
                                                    readonly
                                                    v-model="selectedProductInfo.refInfo.reference_short_name"
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>

                                    <v-row>
                                        <v-col
                                                cols="12"
                                                md="6"
                                        >
                                            <v-text-field
                                                    label="Sub Brand"
                                                    outlined
                                                    readonly
                                                    v-model="selectedProductInfo.refInfo.reference_sub_brand"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                                cols="12"
                                                md="6"
                                        >
                                            <v-text-field
                                                    label="Manufacturer"
                                                    outlined
                                                    readonly
                                                    v-model="selectedProductInfo.refInfo.reference_manufacturer"
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>

                                    <v-row>
                                        <v-col
                                                cols="12"
                                                md="6"
                                        >
                                            <v-text-field
                                                    label="Category"
                                                    outlined
                                                    readonly
                                                    v-model="selectedProductInfo.refInfo.reference_category"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                                cols="12"
                                                md="6"
                                        >
                                            <v-text-field
                                                    label="Sub Category"
                                                    outlined
                                                    readonly
                                                    v-model="selectedProductInfo.refInfo.reference_sub_category"
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>

                                    <v-row>
                                        <v-col
                                                cols="12"
                                                md="3"
                                        >
                                            <v-text-field
                                                    label="Base Size"
                                                    outlined
                                                    readonly
                                                    v-model="selectedProductInfo.refInfo.reference_base_size"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                                cols="12"
                                                md="3"
                                        >
                                            <v-text-field
                                                    label="Size"
                                                    outlined
                                                    readonly
                                                    v-model="selectedProductInfo.refInfo.reference_size"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                                cols="12"
                                                md="2"
                                        >
                                            <v-text-field
                                                    label="Unit"
                                                    outlined
                                                    readonly
                                                    v-model="selectedProductInfo.refInfo.reference_measurement_unit"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                                cols="12"
                                                md="4"
                                        >
                                            <v-text-field
                                                    label="Container Type"
                                                    outlined
                                                    readonly
                                                    v-model="selectedProductInfo.refInfo.reference_container_type"
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>

                                    <v-row>
                                        <v-col
                                                cols="12"
                                                md="6"
                                        >
                                            <v-text-field
                                                    label="Agg Level"
                                                    outlined
                                                    readonly
                                                    v-model="selectedProductInfo.refInfo.reference_agg_level"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                                cols="12"
                                                md="6"
                                        >
                                            <v-text-field
                                                    label="Segment"
                                                    outlined
                                                    readonly
                                                    v-model="selectedProductInfo.refInfo.reference_segment"
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>

                                    <v-row>
                                        <v-col
                                                cols="12"
                                                md="6"
                                        >
                                            <v-text-field
                                                    label="UPC2 Count"
                                                    outlined
                                                    readonly
                                                    v-model="selectedProductInfo.refInfo.reference_count_upc2"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                                cols="12"
                                                md="6"
                                        >
                                            <v-text-field
                                                    label="Flavor Detail"
                                                    outlined
                                                    readonly
                                                    v-model="selectedProductInfo.refInfo.reference_flavor_detail"
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>

                                    <v-row>
                                        <v-col
                                                cols="12"
                                                md="6"
                                        >
                                            <v-text-field
                                                    label="Case Pack"
                                                    outlined
                                                    readonly
                                                    v-model="selectedProductInfo.refInfo.reference_case_pack"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                                cols="12"
                                                md="6"
                                        >
                                            <v-text-field
                                                    label="Multi Pack"
                                                    outlined
                                                    readonly
                                                    v-model="selectedProductInfo.refInfo.reference_multi_pack"
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>
                                </v-tab-item>

                                </v-tabs-items>
                            </v-tabs>
                        </v-container>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red darken-1" text @click="unassignProduct()">Close</v-btn>
                        <v-btn color="success darken-1" text @click="saveQaProduct()" :disabled="!valid">Save</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="newErrorDialog" max-width="500px">
                <v-card>
                    <v-card-title>
                        <v-row>
                            <v-col>
                        <span class="body-1 font-weight-regular">
                            Add New Error
                        </span>
                            </v-col>
                        </v-row>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col
                                >
                                    <v-text-field
                                            label="Error Name"
                                            outlined
                                            v-model.trim="newError"
                                    ></v-text-field>
                                    <v-alert
                                            border="right"
                                            colored-border
                                            type="error"
                                            elevation="2"
                                            v-if="newErrorDialogMessage !== ''"
                                    >
                                        {{ newErrorDialogMessage }}
                                    </v-alert>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red darken-1" text @click="newErrorDialog = false">Close</v-btn>
                        <v-btn color="success darken-1"  :disabled="newError === ''" text @click="addNewQaError()">Save</v-btn>
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
            tab: null,
            files: [],
            darkThemeSelected: false,
            dialog: false,
            qaDialog: false,
            newErrorDialog: false,
            newErrorDialogMessage: '',
            newError: '',
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
            productTypeItems: ['brand', 'sku', 'dvc', 'facing'],
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
                assignMessage: '',
                productNameOrg: '',
                productAltNameOrg: '',
                productName: '',
                productAltName: '',
                productFacingCount: 0,
                productLink: '',
                manuLink: '',
                manuLinkOrg: '',
                refInfo: {},
                productType: '',
                qaStatus: '',
            },
            assigned: 0,
            qaErrors: [],
            selectedQaErrors: [],
            valid: false,
            productNameRules: [
                v => !!v || 'Product Name is required'
            ],
            productAltNameRules: [
                v => !!v || 'Product Alt Name is required'
            ],
            qaStatusRules: [
                v => !!v || 'QA Status is required'
            ],
            acceptedImageFormats: ['png', 'jpg', 'jpeg'],
            maxFileCount: 4,
            maxFileSize: 2000000,
            manuLinkRules: [
                v => !!v || 'Manu Link is required',
                v => (v && v.length > 10) || 'Link must be greater than 10 characters',
            ],
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
            getQaErrors() {
                let formData = new FormData();
                formData.append('project_name', this.selectedProject);
                axios.post('api/fetch_qa_error_list.php', formData)
                    .then((response) => {
                        this.qaErrors = response.data[0].error_rows;
                    });

            },
            addNewQaError() {
                let formData = new FormData();
                formData.append('project_name', this.selectedProject);
                formData.append('error_new_name', this.newError);
                axios.post('api/add_new_error.php', formData)
                    .then((response) => {
                        this.newErrorDialogMessage = response.data[0].error;
                        if (this.newErrorDialogMessage === '') {
                            this.getQaErrors();
                            this.selectedQaErrors.unshift(response.data[0].error_id);
                            this.newErrorDialog = false;
                        }
                    });
            },
            qaProduct(item) {
                this.selectedProductInfo.projectName = this.selectedProject;
                this.selectedProductInfo.ticket_id = item.ticket_id;
                this.selectedProductInfo.product_hunt_type = item.product_hunt_type;
                this.selectedProductInfo.productId = item.product_id;
                this.selectedProductInfo.productType = item.product_type;
                this.selectedProductInfo.assignMessage = '';

                this.dialog = true;
                let formData = new FormData();
                formData.append('project_name', this.selectedProject);
                formData.append('product_id', this.selectedProductInfo.productId);
                axios.post('api/assign_product_qa.php', formData)
                    .then((response) => {
                        if (response.data[0].row_count === 1) {
                            this.getQaErrors();
                            this.dialog = false;
                            this.selectedProductInfo.productName = response.data[0].product_info[0].product_name;
                            this.selectedProductInfo.productAltName = response.data[0].product_info[0].product_alt_design_name;
                            this.selectedProductInfo.productNameOrg = response.data[0].product_info[0].product_name;
                            this.selectedProductInfo.productAltNameOrg = response.data[0].product_info[0].product_alt_design_name;
                            this.selectedProductInfo.productFacingCount = response.data[0].product_info[0].product_facing_count;
                            this.selectedProductInfo.productLink = response.data[0].product_info[0].product_link;
                            this.selectedProductInfo.manuLinkOrg = response.data[0].product_info[0].manufacturer_link;
                            this.selectedProductInfo.manuLink = response.data[0].product_info[0].manufacturer_link;
                            this.selectedProductInfo.productHuntType = response.data[0].product_info[0].product_hunt_type;
                            this.selectedProductInfo.titleString = response.data[0].title_string;
                            this.selectedProductInfo.radarSource = response.data[0].radar_source;

                            if (this.selectedProductInfo.productHuntType === "reference") {
                                this.selectedProductInfo.refInfo = response.data[0].ref_info;
                            }

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
                            this.$refs.form.reset();
                            this.selectedProductInfo.qaStatus = '';
                        }
                    });
            },
            saveQaProduct() {
                if (this.$refs.form.validate()) {
                    this.dialog = true;
                    let formData = new FormData();
                    formData.append('project_name', this.selectedProject);
                    formData.append("product_type", this.selectedProductInfo.productType);
                    formData.append("product_rename", this.selectedProductInfo.productName);
                    formData.append("error_image_count", this.files.length);
                    formData.append("product_alt_rename", this.selectedProductInfo.productAltName);
                    formData.append("error_qa", this.selectedQaErrors);
                    formData.append("num_facings", this.selectedProductInfo.productFacingCount);
                    formData.append("manu_link", this.selectedProductInfo.manuLink);
                    for (let i = 0; i < this.files.length; i++) {
                        formData.append("error_images" + i, this.files[i]);
                    }
                    formData.append('status', this.selectedProductInfo.qaStatus);

                    axios.post('api/process_qa.php', formData)
                        .then(() => {
                            this.getProductInfo();
                            this.dialog = false;
                            this.qaDialog = false;
                            this.$refs.form.reset();
                        });
                }
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
            newErrorDialog: function(val) {
                if (val === false) {
                    this.newError = '';
                    this.newErrorDialogMessage = '';
                }
            },
            files: function () {
                console.log(this.files[0])
            }
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
            },
            errorTypeValidate() {
                 if ((this.selectedQaErrors.length > 0 && this.selectedProductInfo.qaStatus === 'disapproved')
                    || (this.selectedProductInfo.qaStatus === 'approved')) {
                     return true;
                 } else if (this.selectedQaErrors.length > 0 && this.selectedProductInfo.qaStatus === ''){
                     return "QA Status must be selected";
                 } else {
                     return "Error Type must be selected";
                 }
            },
            fileTypeValidate() {
                if (this.files.length === 0 && this.selectedQaErrors > 0) {
                    return "At least one image must be selected for upload";
                } else if (!(this.files.reduce((size, file) => size + file.size, 0) < 8000000)) {
                    return "Images must be less than 8MB in size";
                } else if (this.files.length > 4) {
                    return "Only 4 Images can be selected for upload";
                }else {
                    return true;
                }
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
