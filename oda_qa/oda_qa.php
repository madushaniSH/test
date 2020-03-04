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
    <title>ODA QA</title>
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
            <v-toolbar-title>ODA Quality Assurance</v-toolbar-title>
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
                            hint="Select a Ticket"
                            persistent-hint
                            :items="ticketArray"
                            multiple
                    >
                        <template v-slot:selection="{ item, index }">
                            <v-chip v-if="index === 0">
                                <span>{{ item }}</span>
                            </v-chip>
                            <span
                                    v-if="index === 1"
                                    class="grey--text caption"
                            >(+{{ selectedTickets.length - 1 }} others)</span>
                        </template>

                        <template v-slot:prepend-item>
                            <v-list-item
                                    ripple
                                    @click="toggle"
                            >
                                <v-list-item-action>
                                    <v-icon :color="selectedTickets.length > 0 ? 'indigo darken-4' : ''">{{ icon }}</v-icon>
                                </v-list-item-action>
                                <v-list-item-content>
                                    <v-list-item-title>Select All</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-divider class="mt-2"></v-divider>
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
                            hint="Select a Ticket Status"
                            persistent-hint
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
                            multiple
                            small-chips
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
                            multiple
                            small-chips
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
                <v-col
                        cols="6"
                        md="2"
                >
                    <v-autocomplete
                            label="Client Category"
                            :items="productClientCategoryItems"
                            v-model="selectedClientCategory"
                            chips
                            deletable-chips
                    ></v-autocomplete>
                </v-col>
                <v-col
                        cols="6"
                        md="2"
                >
                    <v-autocomplete
                            label="Reference Status"
                            :items="referenceStatus"
                            v-model="selectedReferenceStatus"
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
                                        <v-btn color="indigo" dark >
                                            <v-icon dark @click="exportProducts(filteredProducts)">mdi-cloud-download</v-icon>
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
                                           :disabled="item.qa_being_handled === null
                                           || (item.qa_being_handled === '1' && item.assigned_user === '0')
                                           || (assigned === 1 && item.assigned_user !== '1')
                                           || (item.ticket_status === 'IN PROGRESS / SEND TO EAN' && item.product_ean_id === null && item.product_type === 'sku')"
                                           @click="qaProduct(item)"
                                    >{{ item.ticket_status === 'IN PROGRESS / SEND TO EAN' && item.product_ean_id === null && item.product_type === 'sku' ? "Pending EAN" : "QA" }}</v-btn>
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
                                    dark
                            >
                                <v-tabs-slider></v-tabs-slider>

                                <v-tab
                                        href="#qaForm"
                                >
                                    QA Form
                                </v-tab>
                                <v-tab
                                        href="#sourceInfo"
                                        v-if="selectedProductInfo.productHuntType === 'reference'"
                                >
                                    Source Information
                                </v-tab>

                                <v-tab
                                        href="#eanInfo"
                                        v-if="selectedProductInfo.eanInformation.eanProductId !== null"
                                >
                                    EAN Information
                                </v-tab>

                                <v-tab-item
                                        value="qaForm"
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
                                                    <v-radio label="Activate" color="success" value="active"></v-radio>
                                                    <v-radio label="Reject" color="red darken-3" value="rejected"></v-radio>
                                                </v-radio-group>
                                            </v-col>
                                        </v-row>

                                        <v-row>
                                            <v-col>
                                                <v-combobox
                                                        :items="productCommentItems"
                                                        label="Product Comment"
                                                        required
                                                        v-model.trim="selectedProductInfo.productComment"
                                                ></v-combobox>
                                            </v-col>
                                        </v-row>

                                    </v-form>
                                </v-tab-item>

                                <v-tab-item
                                        value="sourceInfo"
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

                                <v-tab-item
                                        value="eanInfo"
                                >
                                    <v-row>
                                        <v-col
                                                cols="12"
                                                md="12"
                                        >
                                            <v-text-field
                                                    label="Chain w/ Product Name"
                                                    outlined
                                                    readonly
                                                    :value="selectedProductInfo.eanInformation.productName"
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col
                                                cols="12"
                                                md="6"
                                        >
                                            <v-text-field
                                                    label="Item Code"
                                                    outlined
                                                    readonly
                                                    :value="selectedProductInfo.eanInformation.itemCode"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                                cols="12"
                                                md="6"
                                        >
                                            <v-text-field
                                                    label="EAN"
                                                    outlined
                                                    readonly
                                                    :value="selectedProductInfo.eanInformation.ean"
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col
                                                cols="12"
                                                md="6"
                                        >
                                            <v-text-field
                                                    label="Unmatch Reason"
                                                    outlined
                                                    readonly
                                                    :value="selectedProductInfo.eanInformation.unmatchReason"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                                cols="12"
                                                md="6"
                                        >
                                            <v-text-field
                                                    label="DVC, Substitute or Duplicated Product Name"
                                                    outlined
                                                    readonly
                                                    :value="selectedProductInfo.eanInformation.duplicateWith"
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col
                                                cols="12"
                                                md="6"
                                        >
                                            <v-text-field
                                                    label="Matched With"
                                                    outlined
                                                    readonly
                                                    :value="selectedProductInfo.eanInformation.matchWith"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                                cols="12"
                                                md="6"
                                        >
                                            <v-text-field
                                                    label="Additional Comment"
                                                    outlined
                                                    readonly
                                                    :value="selectedProductInfo.eanInformation.addComment"
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>
                                    <v-row v-for="weblink, index in selectedProductInfo.eanInformation.webLinkArray" :key="index">
                                        <v-col>
                                            <v-text-field
                                                    :label="`'Web Link ${index+1}` "
                                                    outlined
                                                    readonly
                                                    :value="weblink"
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>

                                </v-tab-item>

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
            <v-btn href="../oda_dashboard.php">
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
            darkThemeSelected: true,
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
                {text: 'Client Category', value: 'client_category_name', width: '10%', filterable: false},
                {text: 'Type', value: 'product_type', width: '5%', filterable: false},
                {text: 'Product Name', value: 'product_name', width: '25%'},
                {text: 'Product Alt Name', value: 'product_alt_design_name', width: '25%'},
                {text: 'Product QA Status', value: 'product_qa_status', width: '5%', filterable: false},
                {text: 'Hunt Type', value: 'product_hunt_type', width: '10%', filterable: false},
                {text: 'Product History', value: 'view', sortable: false, align: 'center', filterable: false},
                {text: 'EAN', value: 'product_ean', width: '10%'},
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
            selectedQaStatus: ['approved'],
            productHuntItems: ['probe', 'radar', 'reference'],
            selectedHuntType: [''],
            productTypeItems: ['brand', 'sku', 'dvc', 'facing'],
            selectedProductType: '',
            productBrandItems: [],
            productClientCategoryItems: [],
            selectedBrand: '',
            selectedClientCategory: '',
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
                productComment: '',
                eanInformation: {
                    eanProductId: '',
                }
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
            manuLinkRules: [
                v => !!v || 'Manu Link is required',
                v => (v && v.length > 10) || 'Link must be greater than 10 characters',
            ],
            referenceStatus: ['All', 'Already Matched', 'Pending'],
            selectedReferenceStatus: 'All',
            productCommentItems: ['Detected as a New SKU', 'Detected as a New Brand', 'Detected as a New DVC']
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
                            let index = 0;
                            let array = [];
                            response.data[0].ticket_info.forEach(item => {
                                array[index] = item.ticket_id;
                                index++;
                            });
                            this.ticketArray = array.slice();
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
                                this.productClientCategoryItems[count] = item.client_category_name;
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
                    })
                    .catch(() => {
                        location.reload();
                    });
            },
            qaProduct(item) {
                this.selectedProductInfo.projectName = this.selectedProject;
                this.selectedProductInfo.ticket_id = item.ticket_id;
                this.selectedProductInfo.product_hunt_type = item.product_hunt_type;
                this.selectedProductInfo.productId = item.product_id;
                this.selectedProductInfo.productType = item.product_type;
                this.selectedProductInfo.eanInformation.eanProductId = item.product_ean_id;
                this.selectedProductInfo.eanInformation.itemCode = item.product_item_code;
                this.selectedProductInfo.eanInformation.ean = item.product_ean;
                this.selectedProductInfo.eanInformation.unmatchReason = item.unmatchReason;
                this.selectedProductInfo.eanInformation.duplicateWith = item.duplicate_product_name;
                this.selectedProductInfo.eanInformation.matchWith = item.matched_method;
                this.selectedProductInfo.eanInformation.addComment = item.additional_comment;
                this.selectedProductInfo.eanInformation.productName = item.chain_name;
                this.selectedProductInfo.eanInformation.webLinkArray = [];
                if (item.weblink !== '') {
                    this.selectedProductInfo.eanInformation.webLinkArray = item.weblink.split(',');
                }
                this.selectedProductInfo.assignMessage = '';
                this.selectedProductInfo.refInfo = {};

                this.dialog = true;
                let formData = new FormData();
                formData.append('project_name', this.selectedProject);
                formData.append('product_id', this.selectedProductInfo.productId);
                axios.post('api/assign_product_oda.php', formData)
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
                    })
                    .catch(() => {
                        location.reload();
                    });
            },
            unassignProduct() {
                let formData = new FormData();
                formData.append('project_name', this.selectedProject);

                axios.post('api/unassign_product_oda.php', formData)
                    .then((response) => {
                        if (response.data[0].error === '') {
                            this.qaDialog = false;
                            this.getProductInfo();
                            this.$refs.form.reset();
                            this.selectedProductInfo.qaStatus = '';
                        }
                    })
                    .catch(() => {
                        location.reload();
                    });
            },
            saveQaProduct() {
                if (this.$refs.form.validate()) {
                    this.dialog = true;
                    let formData = new FormData();
                    formData.append("project_name", this.selectedProject);
                    formData.append("product_type", this.selectedProductInfo.productType);
                    formData.append("product_rename", this.selectedProductInfo.productName);
                    formData.append("product_alt_rename", this.selectedProductInfo.productAltName);
                    formData.append("error_qa", this.selectedQaErrors);
                    formData.append("product_comment", this.selectedProductInfo.productComment);
                    formData.append("num_facings", this.selectedProductInfo.productFacingCount);
                    formData.append("manu_link", this.selectedProductInfo.manuLink);
                    formData.append('status', this.selectedProductInfo.qaStatus);

                    axios.post('api/process_oda_qa.php', formData)
                        .then(() => {
                            this.getProductInfo();
                            this.dialog = false;
                            this.qaDialog = false;
                            this.$refs.form.reset();
                        })
                        .catch(() => {
                            location.reload();
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
                            "Hunt Creation Date": this.stringCheck(item.product_creation_time),
                            "Ticket ID": this.stringCheck(item.ticket_id),
                            "Product Type": this.stringCheck(item.product_type.toUpperCase()),
                            "Product Name": this.stringCheck(item.product_name),
                            "Product Previous Name": this.stringCheck(item.product_previous),
                            "Product Alt Name": this.stringCheck(item.product_alt_design_name),
                            "Product Previous Alt Name": this.stringCheck(item.product_alt_design_previous),
                            "Product QA Status": this.stringCheck(item.product_qa_status),
                            "Facing Count": this.stringCheck(item.product_facing_count),
                            "Hunt Type": this.stringCheck(item.product_hunt_type.toUpperCase()),
                            "Product Source Link": this.stringCheck(item.product_link),
                            "Product Hunt Source": this.stringCheck(source),
                            "SRT QA Errors": this.stringCheck(item.qa_error),
                            "SRT QA DateTime": this.stringCheck(item.product_qa_datetime),
                            "Product Item Code": this.stringCheck(item.product_item_code),
                            "Product EAN": this.stringCheck(item.product_ean),
                            "Matched With": this.stringCheck(item.matched_method),
                            "Unmatch Reason": this.stringCheck(item.unmatch_reason),
                            "DVC or Substitute or Duplicated Product name": this.stringCheck(item.duplicate_product_name),
                            "Product Chain with Product Name": this.stringCheck(item.chain_name),
                            "Web Links": this.stringCheck(item.weblink),
                            "EAN Additional Comment": this.stringCheck(item.additional_comment),
                            "EAN Creation QA GID":this.stringCheck(item.ean_gid),
                            "EAN Creation QA Name":this.stringCheck(item.ean_fname),
                            "EAN Creation QA DateTime": this.stringCheck(item.ean_creation_time),
                            "EAN Last Modified QA GID": this.stringCheck(item.mod_gid),
                            "EAN Last Modified QA Name": this.stringCheck(item.mod_name),
                            "EAN Last Modified QA DateTime": this.stringCheck(item.ean_last_mod_datetime),
                            "Product QA Previous Name": this.stringCheck(item.product_qa_previous),
                            "Product QA Previous Alt Name": this.stringCheck(item.product_alt_design_qa_previous),
                            "Product ODA Errors": this.stringCheck(item.oda_error),
                            "ODA Comment": this.stringCheck(item.product_oda_comment),
                            "ODA QA GID": this.stringCheck(item.account_gid),
                            "ODA QA Name": this.stringCheck(item.account_first_name),
                            "ODA QA DateTime": this.stringCheck(item.product_oda_datetime),
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
            toggle () {
                this.$nextTick(() => {
                    if (this.selectAllTickets) {
                        this.selectedTickets = []
                    } else {
                        this.selectedTickets = this.ticketArray.slice();
                        this.getProductInfo();
                    }
                })
            },
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
        },
        created() {
            this.getProjectList();
            this.$vuetify.theme.dark = this.darkThemeSelected;
        },
        computed: {
            filteredProducts() {
                let array = [];
                this.selectedQaStatus.forEach(status => {
                    array.push(this.productInfo.filter((i) => {
                        return !status || (i.product_qa_status === status);
                    }));
                });
                let a = [];
                for (row of array) for (e of row) a.push(e);

                this.selectedHuntType.forEach(type => {
                    a = a.filter((i) => {
                        return !type || (i.product_hunt_type === type);
                    });
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
                this.productClientCategoryItems = [];
                count = 0;
                a.forEach(item => {
                    this.productClientCategoryItems[count] = item.client_category_name;
                    count++;
                });
                a = a.filter((i) => {
                    return !this.selectedClientCategory || (i.client_category_name === this.selectedClientCategory);
                });
                let refStatus = this.selectedReferenceStatus;
                if (refStatus === 'Already Matched') {
                    a = a.filter((i) => {
                        return !refStatus || (i.product_ean_id !== null);
                    });
                } else if (refStatus === 'Pending') {
                    a = a.filter((i) => {
                        return !refStatus || (i.product_ean_id === null);
                    });
                }
                this.getPendingCount(a);
                return a;
            },
            errorTypeValidate() {
                if ((this.selectedQaErrors.length > 0 && this.selectedProductInfo.qaStatus === 'rejected')
                    || (this.selectedProductInfo.qaStatus === 'active')) {
                    return true;
                } else if (this.selectedQaErrors.length > 0 && this.selectedProductInfo.qaStatus === ''){
                    return "Status must be selected";
                } else {
                    return "Error Type must be selected";
                }
            },
            selectAllTickets() {
                return this.selectedTickets.length === this.ticketArray.length
            },
            selectSomeTickets() {
                return this.selectedTickets.length > 0 && !this.selectAllTickets
            },
            icon () {
                if (this.selectAllTickets) return 'mdi-close-box';
                if (this.selectSomeTickets) return 'mdi-minus-box';
                return 'mdi-checkbox-blank-outline';
            },
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
