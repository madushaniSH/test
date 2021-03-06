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
                        cols="12"
                        md="4"
                >
                    <v-file-input
                            v-model="files"
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
                            label="Product EAN File"
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
                <v-col
                        cols="12"
                        md="2"

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
                                    label="Product Creation Time"
                                    v-on="on"
                                    chips
                                    small-chips
                                    multiple
                                    :disabled="productInfo.length === 0"
                            ></v-combobox>
                        </template>
                        <v-date-picker
                                v-model="dates"
                                range scrollable
                        >
                            <v-spacer></v-spacer>
                            <v-btn text color="primary" @click="menu = false">Cancel</v-btn>
                            <v-btn text color="primary" :disabled="dates.length < 2" @click="$refs.dateDialog.save(dates);">OK
                            </v-btn>
                        </v-date-picker>
                    </v-dialog>
                </v-col>
                <v-col>
                    <v-layout
                            justify-end
                    >
                        <v-col
                                cols="6"
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
                    <v-btn
                            class="filters"
                            color="purple"
                            @click="historyDialog = true"
                    >View Product History</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn
                            @click="checkForDuplicates()"
                            class="filters"
                    >
                        Save
                    </v-btn>
                </v-toolbar>

                <v-tabs
                        v-model="tabs"
                        background-color="purple"
                        color="white"
                >
                    <v-tab
                            v-for="item, index in productTabs"
                            :key="index"
                    >
                        {{ item }}
                        <v-btn
                                class="filters"
                               fab dark x-small color="red"
                               v-if="index > 0"
                               @click="removeChainProduct(index)"
                        >
                            <v-icon dark>mdi-close</v-icon>
                        </v-btn>
                    </v-tab>
                </v-tabs>
                <v-card-text>
                    <v-tabs-items v-model="tabs">
                        <v-tab-item>
                            <v-row
                                    align="start"
                                    justify="start"
                                    class="filters"
                            >
                                <v-col
                                        cols="6"
                                        md="3"
                                >
                                    <v-text-field
                                            label="Main Product Name"
                                            v-model.trim="eanReferenceInformation.productName"
                                            disabled
                                    >
                                    </v-text-field>
                                </v-col>
                            </v-row>
                            <v-row
                                    align="start"
                                    justify="start"
                                    class="filters"
                            >
                                <v-col
                                        cols="6"
                                        md="3"
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
                                        md="3"
                                >
                                    <v-text-field
                                            label="EAN"
                                            v-model.trim="eanReferenceInformation.selectedEAN"
                                            counter="20"
                                    >
                                    </v-text-field>
                                </v-col>
                                <v-col
                                        cols="12"
                                        md="3"
                                >
                                    <v-autocomplete
                                            label="Matched With"
                                            :items="matchedWith"
                                            v-model="eanReferenceInformation.selectedMatchWith"
                                            clearable
                                    >
                                    </v-autocomplete>
                                </v-col>
                                <v-col
                                        cols="6"
                                        md="3"
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
                                            :disabled="eanReferenceInformation.selectedEAN !== ''
                                            && eanReferenceInformation.selectedUnmatchReason.unmatch_reason_id !== '18'"
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
                                        v-if="eanReferenceInformation.selectedUnmatchReason.unmatch_reason_id !== '18'"
                                >
                                    <v-text-field
                                            label="DVC, Substitute or Duplicated Product Name"
                                            :disabled="!(eanReferenceInformation.selectedUnmatchReason.unmatch_reason_id > 13 && eanReferenceInformation.selectedUnmatchReason.unmatch_reason_id < 18)"
                                            v-model.trim="eanReferenceInformation.duplicateProductName"
                                    >
                                    </v-text-field>
                                </v-col>
                                <v-col
                                        cols="6"
                                        md="2"
                                        v-else
                                >
                                    <v-btn
                                            outlined
                                            color="teal"
                                            @click="openChainDialog()"
                                            :disabled="currentChainIndex !== -1"
                                    >
                                        Chain Products
                                    </v-btn>
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
                                                    fab dark color="blue"
                                                    @click="addNewWebLink(eanReferenceInformation.weblinks)"
                                                    :disabled="item.link === ''"
                                            >
                                                mdi-plus
                                            </v-icon>
                                            <v-icon
                                                    slot="append"
                                                    fab dark color="red" @click="removeWebLink(eanReferenceInformation.weblinks, index)"
                                                    :disabled="index === 0"
                                            >
                                                mdi-minus
                                            </v-icon>
                                        </v-text-field>
                                    </v-col>
                                </v-row>
                            </section>
                        </v-tab-item>


                        <v-tab-item v-for="item, index in chainProductInfoArray" :key="index">
                            <v-row
                                    align="start"
                                    justify="start"
                                    class="filters"
                            >
                                <v-col
                                        cols="6"
                                        md="3"
                                >
                                    <v-text-field
                                            label="Chain Product Name"
                                            v-model.trim="item.productName"
                                    >
                                    </v-text-field>
                                </v-col>
                            </v-row>
                            <v-row
                                    align="start"
                                    justify="start"
                                    class="filters"
                            >
                                <v-col
                                        cols="6"
                                        md="3"
                                >
                                    <v-text-field
                                            label="Item Code"
                                            v-model.trim="item.itemCode"
                                            counter="20"
                                    >
                                    </v-text-field>
                                </v-col>
                                <v-col
                                        cols="6"
                                        md="3"
                                >
                                    <v-text-field
                                            label="EAN"
                                            v-model.trim="item.selectedEAN"
                                            counter="20"
                                    >
                                    </v-text-field>
                                </v-col>
                                <v-col
                                        cols="12"
                                        md="3"
                                >
                                    <v-autocomplete
                                            label="Matched With"
                                            :items="matchedWith"
                                            v-model="item.selectedMatchWith"
                                            clearable
                                    >
                                    </v-autocomplete>
                                </v-col>
                                <v-col
                                        cols="6"
                                        md="3"
                                >
                                    <v-text-field
                                            label="Additional Comment"
                                            v-model.trim="item.additonalComment"
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
                                            :disabled="item.selectedEAN !== '' && item.selectedUnmatchReason.unmatch_reason_id  !== '18'"
                                            :items="unmatchReasons"
                                            item-text="unmatch_reason"
                                            return-object
                                            v-model="item.selectedUnmatchReason"
                                            clearable
                                    >
                                    </v-autocomplete>
                                </v-col>
                                <v-col
                                        cols="6"
                                        md="8"
                                        v-if="item.selectedUnmatchReason.unmatch_reason_id !== '18'"
                                >
                                    <v-text-field
                                            label="DVC, Substitute or Duplicated Product Name"
                                            :disabled="!(item.selectedUnmatchReason.unmatch_reason_id > 13 && item.selectedUnmatchReason.unmatch_reason_id < 18)"
                                            v-model.trim="item.duplicateProductName"
                                    >
                                    </v-text-field>
                                </v-col>
                                <v-col
                                        cols="6"
                                        md="2"
                                        v-else
                                >
                                    <v-btn
                                            outlined
                                            color="teal"
                                            @click="openChainDialog()"
                                            :disabled="currentChainIndex !== index"
                                    >
                                        Chain Products
                                    </v-btn>
                                </v-col>
                            </v-row>
                            <section v-for="(links, index) in item.weblinks">
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
                                                v-model.trim="links.link"
                                        >
                                            <v-icon
                                                    slot="prepend-inner"
                                                    fab dark color="blue" @click="addNewWebLink(item.weblinks)"
                                                    :disabled="item.link === ''"
                                            >
                                                mdi-plus
                                            </v-icon>
                                            <v-icon
                                                    slot="append"
                                                    fab dark color="red" @click="removeWebLink(item.weblinks, index)"
                                                    :disabled="index === 0"
                                            >
                                                mdi-minus
                                            </v-icon>
                                        </v-text-field>
                                    </v-col>
                                </v-row>
                            </section>
                        </v-tab-item>
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
                        </v-row
                        >
                    </v-tabs-items>
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
                                            multiple
                                            small-chips
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
        <v-dialog v-model="duplicateWarningDialog" persistent max-width="590">
            <v-card>
                <v-card-title class="headline">Duplicate Check Results</v-card-title>
                <v-card-text>
                    {{ duplicateWarning }}
                    <v-data-table
                            :headers="duplicateItemHeaders"
                            :items="duplicateItems"
                            v-if="duplicateItems.length > 0"
                    >
                        <template v-slot:top>
                            <v-toolbar flat>
                                <v-toolbar-title>Result(s)</v-toolbar-title>
                            </v-toolbar>
                        </template>
                    </v-data-table>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                            color="green darken-1"
                            text
                            @click="duplicateWarningDialog = false;">
                        Take Me Back
                    </v-btn>
                    <v-btn
                            color="red darken-1"
                            text
                            @click="saveReference()">
                        I Have Been Warned
                    </v-btn>
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
            darkThemeSelected: true,
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
                {text: 'Client Category', value: 'client_category_name', width: '10%', filterable: false},
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
            productClientCategoryItems: [],
            selectedClientCategory: '',
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
                selectedMatchWith: '',
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
                selectedMatchWith: '',
            },
            unmatchReasons: [],
            matchedWith: ['Matched with Reference File', 'Matched with Attribute File', 'Matched with Item Code'],
            matchedProductEanInfo: [],
            duplicateWarningDialog: false,
            duplicateWarning: '',
            duplicateItems: [],
            duplicateItemHeaders: [
                {text: 'Product Name', value: 'English Product Name', filterable: false},
                {text: 'EAN', value: 'EAN Code',filterable: false},
                {text: 'Item Code', value: 'Item Code', filterable: false},
            ],
            pendingCount: {
                brand: 0,
                sku: 0,
                dvc: 0,
                facing: 0
            },
            referenceStatus: ['All', 'Already Matched', 'Pending'],
            selectedReferenceStatus: 'All',
            chainProductInfoArray: [],
            currentChainIndex: -1,
            productTabs: ['Main Product'],
            orgProductTabs: ['Main Product'],
            tabs: null,
            userPerm: false,
            dates: [],
            menu: false,
    },
        methods: {
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
                        if (index === 0) {
                            if (row[item.col] !== undefined) {
                                total = parseFloat((similarity(item.value, row[item.col]) * 100).toFixed(2));
                                matchArray[i].totalPer = parseFloat(total) + parseFloat(matchArray[i].totalPer);
                            }
                        } else {
                            item.value.forEach(val=> {
                                if (row[item.col] !== undefined) {
                                    total = parseFloat((similarity(val, row[item.col]) * 100).toFixed(2));
                                    matchArray[i].totalPer = parseFloat(total) + parseFloat(matchArray[i].totalPer);
                                }
                            })
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
                    value: []
                };
                this.searchObjectArray.push(newObject);
            },
            addNewWebLink(item) {
                if (item !== undefined) {
                    const webLinkObject = {
                        link: ''
                    };
                    item.push(webLinkObject);
                }
            },
            removeWebLink(item, index) {
                if (item !== undefined) {
                    item.splice(index, 1);
                }
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
                    let  a = this.refInfo.slice();
                    if (this.searchObjectArray.length > 2) {
                        let searchArray = [];
                        for (let i = 1; i < this.searchObjectArray.length; ++i) {
                            let item = this.searchObjectArray[i];
                            item.value.forEach(val => {
                                searchArray.push(a.filter((i) => {
                                    if (i[item.col] !== undefined && val !== '' && i[item.col] !== '') {
                                        return !val || (i[item.col].trim() === val.trim())
                                    }
                                }));
                            });
                            a = [];
                            for (row of searchArray) for (e of row) a.push(e);
                        }
                    }

                    a.forEach(item => {
                        let value = item[this.searchObjectArray[index].col];
                        if (value !== undefined || value !== '' || value !== 'undefined') {
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
                            "EAN Creation QA GID": this.stringCheck(item.account_gid),
                            "EAN Creation QA Name": this.stringCheck(item.account_first_name),
                            "EAN Creation QA DateTime": this.stringCheck(item.ean_creation_time),
                            "EAN Last Modified QA GID": this.stringCheck(item.mod_gid),
                            "EAN Last Modified QA Name": this.stringCheck(item.mod_name),
                            "EAN Last Modified QA DateTime": this.stringCheck(item.ean_last_mod_datetime),
                            "Product QA Previous Name": this.stringCheck(item.product_qa_previous),
                            "Product QA Previous Alt Name": this.stringCheck(item.product_alt_design_qa_previous),
                            "Product ODA Errors": this.stringCheck(item.oda_error),
                            "ODA Comment": this.stringCheck(item.product_oda_comment),
                            "ODA QA GID": this.stringCheck(item.ogid),
                            "ODA QA Name": this.stringCheck(item.ofname),
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
                this.eanReferenceInformation.weblinks = [{link: ''}];
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
                this.searchObjectArray[0].value = item.product_name;
                this.eanReferenceInformation.productName = item.product_name;
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
            checkChainProducts() {
                let flag = true;
                if (this.chainProductInfoArray.length > 0) {
                    this.chainProductInfoArray.forEach(item => {
                        let selectedEAN = item.selectedEAN.trim();
                        let unmatchReasonId= item.selectedUnmatchReason.unmatch_reason_id.trim();
                        if (item.productName.trim() === '' || (selectedEAN === '' && unmatchReasonId === '')) {
                            flag = false;
                        }
                    });
                }
                return flag;
            },
            saveReference() {
                this.duplicateWarningDialog = false;
                let chainCheck = this.checkChainProducts();
                const productId = this.eanReferenceInformation.productId.trim();
                const selectedEAN = this.eanReferenceInformation.selectedEAN.trim();
                let unmatchReasonId= this.eanReferenceInformation.selectedUnmatchReason.unmatch_reason_id.trim();
                const itemCode = this.eanReferenceInformation.itemCode.trim();
                const additionalComment = this.eanReferenceInformation.additonalComment.trim();
                let matchWith = this.eanReferenceInformation.selectedMatchWith;
                let duplicateProductName = this.eanReferenceInformation.duplicateProductName.trim();
                if (selectedEAN !== '' && unmatchReasonId !== '18') {
                    unmatchReasonId = '';
                    duplicateProductName = '';
                }
                if (matchWith === null || matchWith === undefined) {
                    matchWith = '';
                }
                let webLinks = [];
                let index = 0;
                this.eanReferenceInformation.weblinks.forEach(item => {
                    webLinks[index] = item.link;
                    index++;
                });
                if ((selectedEAN !== '' || unmatchReasonId !== '') && chainCheck) {
                    let formData = new FormData;
                    formData.append('project_name', this.selectedProject);
                    formData.append('productId', productId);
                    formData.append('selectedEAN', selectedEAN);
                    formData.append('unmatchReasonId', unmatchReasonId);
                    formData.append('duplicateProductName', duplicateProductName);
                    formData.append('itemCode', itemCode);
                    formData.append('additionalComment', additionalComment);
                    formData.append('webLinks', webLinks);
                    formData.append('matchWith', matchWith);
                    axios.post('api/save_reference_ean.php', formData)
                        .then((response) => {
                            if (this.chainProductInfoArray.length > 0) {
                                this.saveProductChain();
                            }
                            this.resetReferenceObject();
                            this.getProductInfo();
                            this.productMatchDialog = false;
                        })
                        .catch(() => {
                            location.reload();
                        });
                }
            },
            saveProductChain() {
                this.overlay = true;
                this.chainProductInfoArray.forEach(async (item, index) => {
                    const selectedEAN = item.selectedEAN.trim();
                    let unmatchReasonId= item.selectedUnmatchReason.unmatch_reason_id.trim();
                    const itemCode = item.itemCode.trim();
                    const additionalComment = item.additonalComment.trim();
                    let matchWith = item.selectedMatchWith;
                    let duplicateProductName = item.duplicateProductName.trim();
                    if (selectedEAN !== '' && unmatchReasonId !== '18') {
                        unmatchReasonId = '';
                        duplicateProductName = '';
                    }
                    if (matchWith === null || matchWith === undefined) {
                        matchWith = '';
                    }
                    let webLinks = [];
                    item.weblinks.forEach((item, count) => {
                        webLinks[count] = item.link;
                        count++;
                    });
                    let chainProductName = '';
                    if (index === 0) {
                        chainProductName = this.eanReferenceInformation.productName;
                    } else {
                        chainProductName = this.chainProductInfoArray[index - 1].productName;
                    }
                    if (selectedEAN !== '' || unmatchReasonId !== '') {
                        let formData = new FormData;
                        formData.append('project_name', this.selectedProject);
                        formData.append('productName', item.productName.trim());
                        formData.append('chainProductName', chainProductName);
                        formData.append('selectedEAN', selectedEAN);
                        formData.append('unmatchReasonId', unmatchReasonId);
                        formData.append('duplicateProductName', duplicateProductName);
                        formData.append('itemCode', itemCode);
                        formData.append('additionalComment', additionalComment);
                        formData.append('webLinks', webLinks);
                        formData.append('matchWith', matchWith);
                        let product = await axios.post('api/save_reference_chain.php', formData);
                       
                    }
                });
                this.overlay = false;
            },
            fetchUserLevel() {
                axios.get('api/fetch_user_perm.php')
                    .then((response) => {
                        let level = response.data;
                        this.userPerm = level === 'Admin' || level === 'ODA Supervisor';
                    })
                    .catch(() => {
                        location.reload();
                    });
            },
            resetReferenceObject() {
                Object.assign(this.eanReferenceInformation, {});
                Object.assign(this.eanReferenceInformation, this.refObject);
                this.resetChainInfo();
            },
            resetChainInfo() {
                this.productTabs = this.orgProductTabs.slice();
                this.chainProductInfoArray = [];
                this.currentChainIndex = -1;
            },
            checkForDuplicates() {
                if (this.eanReferenceInformation.selectedUnmatchReason.unmatch_reason_id !== '' && this.eanReferenceInformation.selectedUnmatchReason.unmatch_reason_id !== '18') {
                    this.saveReference();
                } else if (this.eanFiles !== null && this.matchedProductEanInfo.length !== 0 &&
                    (this.eanReferenceInformation.selectedEAN !== '' || this.eanReferenceInformation.itemCode !== '')) {
                    this.overlay = true;
                    this.duplicateItems = [];
                    let resultArray = [];
                    let message = "No Duplicates Found";
                    resultArray = this.matchedProductEanInfo.filter(item => {
                        return (item["EAN Code"] === this.eanReferenceInformation.selectedEAN.replace(/^0+/, '') && this.eanReferenceInformation.selectedEAN !== '')
                         || (item["Item Code"] === this.eanReferenceInformation.itemCode.replace(/^0+/, '') && this.eanReferenceInformation.itemCode !== '')
                    });
                    this.overlay = false;
                    if (resultArray.length > 0) {
                        message = resultArray.length + " Duplicate(s) Found";
                        this.duplicateItems = resultArray.slice();
                        this.duplicateWarning = message;
                        this.duplicateWarningDialog= true;
                    } else {
                        this.saveReference();
                    }
                }
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
            addChainProduct() {
                const refObj = {};
                Object.assign(refObj, this.refObject);
                refObj.productName = '';
                this.chainProductInfoArray.push(refObj);
                this.currentChainIndex === -1 ? this.currentChainIndex = 0 : this.currentChainIndex++;
                this.productTabs.push(`Product ${this.currentChainIndex+1}`);
                this.tabs = this.currentChainIndex + 1;
            },
            removeChainProduct(index) {
                this.chainProductInfoArray.splice(index - 1, 1);
                this.productTabs.splice(index, 1);
                this.currentChainIndex = this.chainProductInfoArray.length - 1;
            },
            openChainDialog() {
                this.addChainProduct();
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
            eanFiles: function (val) {
                if (val !== null) {
                    this.overlay = true;
                    Papa.parsePromise = function(val) {
                        return new Promise(function(complete, error) {
                            Papa.parse(val, {worker: true, header:true,complete, error});
                        });
                    };
                    Papa.parsePromise(val).then((results) => {
                        this.matchedProductEanInfo = results.data;
                        this.overlay = false;
                    });
                }
            },
        },
        created() {
            this.fetchUserLevel();
            this.addNewSearch();
            this.getProjectList();
            this.$vuetify.theme.dark = this.darkThemeSelected;
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

                if (this.dates.length === 2) {
                    a = a.filter((i) => {
                        return !this.dates || (i.product_creation_time >= this.dates[0] && i.product_creation_time <= this.dates[1]);
                    });
                }
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
