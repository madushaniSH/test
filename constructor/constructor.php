<?php
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
    <title>Product Constructor</title>
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
            <v-toolbar-title>Product Name Constructor</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-switch
                    v-model="darkThemeSelected"
                    :label="'Dark Mode'"
            ></v-switch>
        </v-app-bar>

        <v-content
        >
            <v-row
                    align="center"
                    justify="center"
                    style="margin-top: 1vh"
            >
                <v-col>
                    <p style="text-align: center">
                        <span v-for="item, index in productNameArrayProper" :key="index">
                            <v-chip
                                :color="item.color"
                                v-if="item.att !== ''"
                                text-color="white"
                                class="title"
                            >
                                {{ item.att }}
                            </v-chip>
                        </span>
                    </p>
                    <p style="text-align: center" class="display-1">
                        {{ genProductName }}
                    </p>
                </v-col>
            </v-row>
            <v-card
                style="margin-top: 4vh;"
                class="mx-auto"
            >
            <v-row style="padding: 10px">
                <v-col md="4">
                    <v-text-field
                            label="Brand"
                            v-model.trim="productNameArray[0].att"
                    ></v-text-field>
                </v-col>
                <v-col md="4">
                    <v-text-field
                            label="Sub Brand"
                            v-model.trim="productNameArray[1].att"
                    ></v-text-field>
                </v-col>
                <v-col md="4">
                    <v-text-field
                            label="Item"
                            v-model.trim="productNameArray[2].att"
                    ></v-text-field>
                </v-col>
            </v-row>
            <v-row style="padding: 10px">
                <v-col md="4">
                    <v-text-field
                            label="Flavor"
                            v-model.trim="productNameArray[3].att"
                    ></v-text-field>
                </v-col>
                <v-col md="8">
                    <v-text-field
                            label="Additional Information"
                            v-model.trim="productNameArray[4].att"
                    ></v-text-field>
                </v-col>
            </v-row>
            <v-row style="padding: 10px">
                <v-col md="4">
                    <v-text-field
                            label="Container Type"
                            v-model.trim="productNameArray[5].att"
                    ></v-text-field>
                </v-col>
                <v-col md="3">
                    <v-text-field
                            label="Sub Packages"
                            v-model.trim="productNameArray[6].att"
                            :disabled="productNameArray[7].att !== ''"
                    ></v-text-field>
                </v-col>
                <v-col md="3">
                    <v-text-field
                            label="Unit"
                            v-model.trim="productNameArray[7].att"
                            :disabled="productNameArray[6].att !== ''"
                    ></v-text-field>
                </v-col>
                <v-col md="2">
                    <v-text-field
                            label="Descriptor"
                            v-model.trim="productNameArray[8].att"
                            disabled
                    ></v-text-field>
                </v-col>
            </v-row>
            <v-row style="padding: 10px">
                <v-col md="4">
                    <v-text-field
                            label="Size"
                            v-model.trim="productNameArray[9].att"
                    ></v-text-field>
                </v-col>
                <v-col md="4">
                    <v-text-field
                            label="Measurement Unit"
                            v-model.trim="productNameArray[10].att"
                    ></v-text-field>
                </v-col>
            </v-row>
            <v-row style="padding: 10px">
                <v-col md="12">
                    <v-text-field
                            label="Value Pack Description"
                            v-model.trim="productNameArray[11].att"
                    ></v-text-field>
                </v-col>
            </v-row>
            </v-card>

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
<script src="https://unpkg.com/papaparse@5.1.1/papaparse.min.js"></script>
<script>
    new Vue({
        el: "#app",
        vuetify: new Vuetify(),
        data: {
            darkThemeSelected: true,
            brand: '',
            subBrand: '',
            item: '',
            flavor: '',
            addInfo: '',
            container: '',
            subPackages: '',
            size: '',
            measurementUnit: '',
            valuePackDescription: '',
            productNameArray: [
                {
                    color: 'teal',
                    att: ''
                },
                {
                    color: 'teal',
                    att: ''
                },
                {
                    color: 'teal',
                    att: ''
                },
                {
                    color: 'amber',
                    att: ''
                },
                {
                    color: 'amber',
                    att: ''
                },
                {
                    color: 'lime',
                    att: ''
                },
                {
                    color: 'lime',
                    att: ''
                },
                {
                    color: 'lime',
                    att: ''
                },
                {
                    color: 'lime',
                    att: ''
                },
                {
                    color: 'green',
                    att: ''
                },
                {
                    color: 'green',
                    att: ''
                },
                {
                    color: 'orange',
                    att: ''
                },
            ]
        },
        method: {
        },
        watch: {
            darkThemeSelected: function (val) {
                this.$vuetify.theme.dark = val;
            },
        },
        created() {
            this.$vuetify.theme.dark = true;
        },
        computed: {
            productNameArrayProper() {
                let array = this.productNameArray;
                let stringArray = [];
                for (let i = 0; i < array.length; ++i) {
                    if (i === 8) {
                        if (array[6].att !== '') {
                            array[i].att = "Packs x";
                        } else if (array[7].att !== '') {
                            if (parseInt(array[7].att) === 1) {
                                array[i].att = "Unit"
                            } else {
                                array[i].att = "Units"
                            }
                        }
                    } else if (i !== 10) {
                        stringArray = array[i].att.toString().split(" ");
                        for (let j = 0; j < stringArray.length; ++j) {
                            stringArray[j] = stringArray[j].charAt(0).toUpperCase() + stringArray[j].slice(1);
                        }
                        array[i].att = stringArray.join(" ");
                    }
                }
                return array;
            },
            genProductName() {
                let array = this.productNameArray;
                let productName = '';
                for (let i = 0; i < array.length; ++i) {
                    productName += array[i].att + ' ';
                }
                return productName.trim();
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
