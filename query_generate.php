<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
    header('Location: ../login_auth_one.php');
    exit();
} else {
    if (!($_SESSION['role'] === 'Admin')) {
        header('Location: ../index.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel='icon' href='favicon.ico' type='image/x-icon'/>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Query Generator</title>
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
            <v-toolbar-title>Query Generator</v-toolbar-title>
        </v-app-bar>
        <v-content>
            <v-row
                    :align="'center'"
                    :justify="'center'"
            >
                <v-col
                        cols="6"
                        md="2"
                >
                    <v-autocomplete
                            v-model="selectedRegion"
                            label="Select"
                            chips
                            hint="Select a Region to get started!"
                            persistent-hint
                            :items="regionArray"
                            multiple
                            chips
                    >
                    </v-autocomplete>
                </v-col>
                <v-col
                        cols="12"
                        md="3"
                >
                    <v-autocomplete
                            v-model="selectedProjects"
                            label="Select"
                            chips
                            hint="Select a Project"
                            persistent-hint
                            :items="projectArray"
                            item-text="name"
                            item-value="name"
                            multiple
                    >
                        <template v-slot:prepend-item>
                            <v-list-item
                                    ripple
                                    @click="toggleAllProjects"
                            >
                                <v-list-item-action>
                                    <v-icon :color="selectedProjects.length > 0 ? 'indigo darken-4' : ''">{{ icon }}
                                    </v-icon>
                                </v-list-item-action>
                                <v-list-item-content>
                                    <v-list-item-title>Select All</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-divider class="mt-2"></v-divider>
                        </template>
                        <template v-slot:selection="{ item, index }">
                            <v-chip v-if="index === 0">
                                <span>{{ item.name }}</span>
                            </v-chip>
                            <span
                                    v-if="index === 1"
                                    class="grey--text caption"
                            >(+{{ selectedProjects.length - 1 }} others)</span>
                        </template>
                    </v-autocomplete>
                </v-col>
                <v-col
                        cols="6"
                        md="2"
                >
                    <v-autocomplete
                            v-model="selectedHuntType"
                            label="Select"
                            chips
                            hint="Select a Hunt Type"
                            persistent-hint
                            :items="['probe', 'radar', 'reference']"
                    >
                    </v-autocomplete>
                </v-col>
                <v-col
                        cols="6"
                        md="2"
                >
                    <v-autocomplete
                            v-model="selectedQueryType"
                            label="Select"
                            chips
                            hint="Select a Query Type"
                            persistent-hint
                            :items="queryType"
                    >
                    </v-autocomplete>
                </v-col>
            </v-row>
            <v-row
                    :align="'center'"
                    :justify="'center'"
            >
                <v-col
                        cols="6"
                        md="8"
                >
                    <v-textarea
                            label="Query"
                            no-resize
                            rows="20"
                            v-model="sql"
                    ></v-textarea>
                </v-col>
            </v-row>
        </v-content>
        <v-bottom-navigation
                color="success"
        >
            <v-btn href="dashboard.php">
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
            regionArray: ['AMER', 'EMEA', 'DPG', 'APAC'],
            selectedRegion: [],
            projectArray: ['Test'],
            selectedProjects: [],
            sql: '',
            selectedHuntType: 'probe',
            queryType: ['Product Type Hunted Query', 'Probe Status Query'],
            selectedQueryType: '',
        },
        methods: {
            getProjectList() {
                this.overlay = true;
                let formData;
                let region = '';
                for (let i = 0; i < this.selectedRegion.length; i++) {
                    region = this.selectedRegion[i];
                    formData = new FormData();
                    formData.append('project_region', region);
                    axios.post('./ticket_handler/api/fetch_project_list.php', formData)
                        .then((response) => {
                            this.projectArray = this.projectArray.concat(response.data[0].project_info);
                            this.overlay = false;
                        });
                }
            },
            toggleAllProjects() {
                this.$nextTick(() => {
                    if (this.allProjects) {
                        this.selectedProjects = []
                    } else {
                        let count = 0;
                        this.selectedProjects = [];
                        this.projectArray.forEach(project => {
                            this.selectedProjects[count] = project.name;
                            count++;
                        });
                    }
                })
            },
            genQuery() {
                if (this.selectedProjects.length > 0 && this.selectedHuntType !== '') {
                    if (this.selectedQueryType === 'Product Type Hunted Query') {
                        this.sql = 'SELECT product_type, SUM(COUNT) AS "Count" FROM\n(';
                        for (let i = 0; i < this.selectedProjects.length; i++) {
                            let project = this.selectedProjects[i];
                            let projectQuery = '';
                            projectQuery = '  SELECT\n' +
                                '        COUNT(p.product_id) AS "Count",\n' +
                                '        p.product_type\n' +
                                '    FROM\n' +
                                `        ${project}.products p\n` +
                                '    WHERE\n' +
                                '       p.product_qa_status = "active" OR p.product_qa_status = "approved"\n' +
                                '    GROUP BY\n' +
                                '        p.product_type\n';
                            if (i + 1 !== this.selectedProjects.length) {
                                projectQuery += '    UNION ALL\n';
                            }
                            this.sql += projectQuery;
                        }
                        this.sql += ')t GROUP BY 1';
                    } else if (this.selectedQueryType === 'Probe Status Query'){
                        this.sql = 'SELECT SUM(COUNT) AS "Count", probe_status_name FROM\n(';
                        for (let i = 0; i < this.selectedProjects.length; i++) {
                            let project = this.selectedProjects[i];
                            let projectQuery = '';
                            let countString = '';
                            let joinString = '';
                            if (this.selectedHuntType === 'probe') {
                                countString = '        COUNT(p.probe_key_id) AS "Count",\n'
                                joinString = '    LEFT OUTER JOIN\n' +
                                    `        ${project}.probe p\n ` +
                                    '    ON\n' +
                                    '        p.probe_status_id = ps.probe_status_id\n';
                            } else if (this.selectedHuntType === 'radar') {
                                countString = '        COUNT(rs.radar_source_id) AS "Count",\n';
                                joinString = '    LEFT OUTER JOIN\n' +
                                    `        ${project}.radar_sources rs\n ` +
                                    '    ON\n' +
                                    '        rs.radar_status_id = ps.probe_status_id\n';
                            } else {
                                countString = '        COUNT(ri.reference_info_id) AS "Count",\n';
                                joinString = '    LEFT OUTER JOIN\n' +
                                    `        ${project}.reference_info ri\n ` +
                                    '    ON\n' +
                                    '        ri.reference_status_id = ps.probe_status_id\n';
                            }
                            projectQuery = '  SELECT\n' +
                                countString +
                                '        ps.probe_status_name\n' +
                                '    FROM\n' +
                                `        ${project}.probe_status ps\n` +
                                joinString +
                                '    GROUP BY\n' +
                                '        ps.probe_status_name\n';
                            if (i + 1 !== this.selectedProjects.length) {
                                projectQuery += '    UNION ALL\n';
                            }
                            this.sql += projectQuery;
                        }
                        this.sql += ')t GROUP BY 2';
                    }
                } else {
                    this.sql = '';
                }
            }
        },
        watch: {
            selectedRegion: function () {
                this.projectArray = [];
                this.selectedProjects = [];
                this.getProjectList();
            },
            selectedProjects: function () {
                this.genQuery();
            },
            selectedHuntType: function() {
                this.genQuery();
            },
            selectedQueryType: function() {
                this.genQuery();
            }
        },
        computed: {
            allProjects() {
                return this.selectedProjects.length === this.projectArray.length
            },
            someProjects() {
                return this.selectedProjects.length > 0 && !this.allProjects
            },
            icon() {
                if (this.allProjects) return 'mdi-close-box';
                if (this.someProjects) return 'mdi-minus-box';
                return 'mdi-checkbox-blank-outline';
            },
        },

    });
</script>
<style>
</style>
</body>
</html>
