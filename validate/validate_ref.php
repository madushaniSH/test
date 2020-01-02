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
                        md="4"
                >
                    <v-file-input
                            v-model="files"
                            placeholder="Upload your documents"
                            label="Reference File input"
                            prepend-icon="mdi-paperclip"
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
                        cols="6"
                        md="2"
                >
                    <v-autocomplete
                            v-model="matchWith"
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
                            v-model="searchWith"
                            label="Search Column"
                            chips
                            :items="refInfoHeaders"
                            :disabled="!matchWith.length > 0"
                    >
                    </v-autocomplete>
                </v-col>
            </v-row>

            <v-row
                    :align="'start'"
                    :justify="'start'"
                    class="filters"
            >
                <v-col
                        cols="6"
                        md="5"
                        v-if="searchWith.length > 0"
                >
                    <v-text-field
                            label="Product Name"
                            v-model.trim="productName"
                    ></v-text-field>
                    <v-btn color="success" @click="matchData">Match</v-btn>
                </v-col>
            </v-row>

            <v-row>
                <v-col>
                    <v-data-table
                            :headers="headers"
                            :items="matchInfo"
                            :sort-by="['per']"
                            :sort-desc="[true]"
                            class="elevation-1"
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
                    </v-data-table>
                </v-col>
            </v-row>

        </v-content>
        <v-bottom-navigation
                color="success"
        >
            <v-btn href="../oda_dashboard.php">
                <span>Dashboard</span>
                <v-icon>mdi-home</v-icon>
            </v-btn>
        </v-bottom-navigation>

        <v-overlay :value="overlay">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
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
            refInfo: [],
            refInfoHeaders: [],
            overlay: false,
            matchWith: [],
            searchWith: [],
            search: '',
            productName: '',
            headers: [
                { text: 'Key', value: 'key' },
                { text: 'Column', value: 'col' },
                { text: 'Match Percentage', value: 'per' },
            ],
            matchInfo: [],
        },
        methods: {
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
                const productName = this.productName;
                const refInfoLength = this.refInfo.length;
                const maxRows = 10000;
                let matchArray = [];
                let row = "";

                this.overlay = true;
                for (let i = 0; i < refInfoLength; ++i) {
                    row = this.refInfo[i];
                    if (row[this.searchWith] !== undefined) {
                        matchArray[i] = {
                            "key": row[this.matchWith],
                            "col": row[this.searchWith],
                            "per": (similarity(productName, row[this.searchWith]) * 100).toFixed(2)
                        }
                    }
                    if (i > maxRows) {
                        break;
                    }
                }

                this.matchInfo = matchArray;
                this.overlay = false;
            },

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
        },
        computed: {
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
