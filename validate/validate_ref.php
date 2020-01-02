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
                    :align="'start'"
                    :justify="'start'"
                    class="filters"
            >
                <v-col
                        cols="6"
                        md="5"
                        v-if="searchObjectArray[0].col !== ''"
                >
                    <v-text-field
                            label="Product Name"
                            v-model.trim="searchObjectArray[0].value"
                    ></v-text-field>
                    <v-btn color="success" @click="matchData">Match</v-btn>
                    <v-btn
                            color="primary"
                            class="ma-2"
                            dark
                            :disabled="files === null"
                            @click="dialog = true"
                    >
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

        <v-dialog
                v-model="overlay"
                hide-overlay
                persistent
                width="300"
        >
            <v-card
                    color="primary"
                    dark
            >
                <v-card-text>
                    Please stand by
                    <v-progress-linear
                            indeterminate
                            color="white"
                            class="mb-0"
                    ></v-progress-linear>
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
                        <section v-for="item in searchObjectArray">
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
                                    ></v-text-field>
                                </v-col>
                            </v-row>
                        </section>
                    </v-container>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red darken-1" text @click="dialog = false">Close</v-btn>
                    <v-btn color="success darken-1" :disabled="checkSearchValues" @click="matchData()" text>Save</v-btn>
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
            refInfo: [],
            refInfoHeaders: [],
            overlay: false,
            searchObjectArray: [],
            key: '',
            search: '',
            productName: '',
            headers: [
                { text: 'Key', value: 'key' },
                { text: 'UPC', value: 'upc'},
                { text: 'Column', value: 'col' },
                { text: 'Match Percentage', value: 'per' },
            ],
            matchInfo: [],
            dialog: false,
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
                const refInfoLength = this.refInfo.length;
                const maxRows = 10000;
                let matchArray = [];
                let total = 0;
                let row = "";

                this.overlay = true;

                // init array
                for (let i = 0; i < refInfoLength; ++i) {
                    row = this.refInfo[i];
                    matchArray[i] = {
                        "key": row[this.key],
                        "upc": (row[this.key] + calcCheckDigit(row[this.key])).padStart(12, "0"),
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
                console.log(matchArray);

                matchArray.sort((a, b) => (a.per < b.per ? 1 : -1)); // sorts array based on match percentage
                let temp = matchArray;
                if (refInfoLength > maxRows) {
                    matchArray = temp.slice(0, maxRows);
                }

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
        },
        created() {
            this.addNewSearch();
        },
        computed: {
            checkSearchValues() {
                let valid = false;
                for (let i = 0; i < this.searchObjectArray.length; i++) {
                    this.searchObjectArray[i].col === '' || this.searchObjectArray[i].value === '' ?
                        valid = true : valid = false;
                }
                return valid;
            }
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

    function calcCheckDigit(code) {
        if (code !== undefined) {
            let check = 0;
            code = code.toString();

            while (code.length < 11) {
                code = "0" + code
            }

            for (let i = 0; i < code.length; i += 2) {
                check += parseInt(code.charAt(i));
            }

            check *= 3;

            for (let i = 1; i < code.length; i += 2) {
                check += parseInt(code.charAt(i));
            }

            check %= 10;
            check = (check === 0) ? check : 10 - check;

            return check
        }
    }
</script>
<style>
    .filters {
        margin-left: 0.5vw;
    }
</style>
</body>
</html>
