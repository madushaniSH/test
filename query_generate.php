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
            queryType: ['Product Type Hunted Query', 'Probe Status Query', 'Product Type Hunted Chart Query',
                'Probe Processed Chart Query', 'Ticket Progress Query', 'Ticket Chart Query', 'Ticket Type Query',
                'Ticket Type Chart Query', 'Ticket Type Complete Query', 'Ticket Type Complete Chart Query',
                'Hunter Trend Chart', 'Hunter Error Chart', 'Hunter Probe Chart', 'Hunter Productivity',
                'Hunter Accuracy', 'Hunter Rename Accuracy'],
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
                                '       (p.product_qa_status = "active" OR p.product_qa_status = "approved") AND $__timeFilter(p.product_qa_datetime)\n' +
                                '    GROUP BY\n' +
                                '        p.product_type\n';
                            if (i + 1 !== this.selectedProjects.length) {
                                projectQuery += '    UNION ALL\n';
                            }
                            this.sql += projectQuery;
                        }
                        this.sql += ')t GROUP BY 1';
                    } else if (this.selectedQueryType === 'Hunter Rename Accuracy'){
                        this.sql = 'SELECT DISTINCT(account_gid) as "metric", ((SUM(prod) - SUM(re)) / SUM(prod)) * 100 AS "value" , now() as time_sec FROM(\n';
                        for (let i = 0; i < this.selectedProjects.length; i++) {
                            let project = this.selectedProjects[i];
                            let projectQuery = '';
                            projectQuery = 'SELECT\n' +
                                '    a.account_gid,\n' +
                                '    SUM(\n' +
                                '        CASE WHEN(p.product_type = "brand") THEN 1 ELSE 0\n' +
                                '    END\n' +
                                ') * 1.5 + SUM(\n' +
                                '    CASE WHEN(p.product_type = "sku") THEN 1 ELSE 0\n' +
                                'END\n' +
                                ') +(\n' +
                                '    SUM(\n' +
                                '        CASE WHEN(p.product_type = "dvc") THEN 1 ELSE 0\n' +
                                '    END\n' +
                                ')\n' +
                                ') * 0.5 +(SUM(p.product_facing_count)) * 0.5 as "prod",\n' +
                                'SUM(\n' +
                                '    CASE WHEN(\n' +
                                '        p.product_previous IS NOT NULL  OR p.product_alt_design_previous IS NOT NULL\n' +
                                '    ) THEN 1 ELSE 0\n' +
                                'END\n' +
                                ') AS "re"\n' +
                                'FROM\n' +
                                `    ${project}.products p\n` +
                                'INNER JOIN\n' +
                                '    user_db.accounts a\n' +
                                'ON\n' +
                                '    a.account_id = p.account_id\n' +
                                'LEFT JOIN\n' +
                                `    ${project}.product_qa_errors pqe\n` +
                                'ON\n' +
                                '    pqe.product_id = p.product_id\n' +
                                'WHERE\n' +
                                '\t$__timeFilter(p.product_creation_time)\n' +
                                'GROUP BY\n' +
                                '    1';
                            if (i + 1 !== this.selectedProjects.length) {
                                projectQuery += '    UNION ALL\n';
                            }
                            this.sql += projectQuery;
                        }
                        this.sql += '\nUNION ALL\n' +
                            'SELECT\n' +
                            '    a.account_gid,\n' +
                            '    0 as "prod",\n' +
                            '    0 as "rename",\n' +
                            'FROM    \n' +
                            '     user_db.accounts a\n' +
                            'INNER JOIN\n' +
                            '    user_db.account_designations ad \n' +
                            '    ON \n' +
                            '    ad.account_id = a.account_id\n' +
                            'WHERE\n' +
                            '    ad.designation_id = 3\n' +
                            '    GROUP BY\n' +
                            '    1\n' +
                            ')t GROUP BY 1 ORDER BY 1 ASC';
                    } else if (this.selectedQueryType === 'Hunter Accuracy'){
                        this.sql = 'SELECT DISTINCT(account_gid) as "metric", ((SUM(prod) - (SUM(qa_errors) + SUM(system_errors))) / SUM(prod)) * 100 AS "value" , now() as time_sec FROM(\n';
                        for (let i = 0; i < this.selectedProjects.length; i++) {
                            let project = this.selectedProjects[i];
                            let projectQuery = '';
                            projectQuery = 'SELECT\n' +
                                '    a.account_gid,\n' +
                                '    SUM(\n' +
                                '        CASE WHEN(p.product_type = "brand") THEN 1 ELSE 0\n' +
                                '    END\n' +
                                ') * 1.5 + SUM(\n' +
                                '    CASE WHEN(p.product_type = "sku") THEN 1 ELSE 0\n' +
                                'END\n' +
                                ') +(\n' +
                                '    SUM(\n' +
                                '        CASE WHEN(p.product_type = "dvc") THEN 1 ELSE 0\n' +
                                '    END\n' +
                                ')\n' +
                                ') * 0.5 +(SUM(p.product_facing_count)) * 0.5 as "prod",\n' +
                                'COUNT(pqe.product_id) AS "qa_errors",\n' +
                                'SUM(\n' +
                                '    CASE WHEN(\n' +
                                '        p.product_qa_status = "disapproved" AND p.product_qa_account_id IS NULL\n' +
                                '    ) THEN 1 ELSE 0\n' +
                                'END\n' +
                                ') AS "system_errors"\n' +
                                'FROM\n' +
                                `    ${project}.products p\n` +
                                'INNER JOIN\n' +
                                '    user_db.accounts a\n' +
                                'ON\n' +
                                '    a.account_id = p.account_id\n' +
                                'LEFT JOIN\n' +
                                `    ${project}.product_qa_errors pqe\n` +
                                'ON\n' +
                                '    pqe.product_id = p.product_id\n' +
                                'WHERE\n' +
                                '\t$__timeFilter(p.product_creation_time)\n' +
                                'GROUP BY\n' +
                                '    1';
                            if (i + 1 !== this.selectedProjects.length) {
                                projectQuery += '    UNION ALL\n';
                            }
                            this.sql += projectQuery;
                        }
                        this.sql += '\nUNION ALL\n' +
                            'SELECT\n' +
                            '    a.account_gid,\n' +
                            '    0 as "prod",\n' +
                            '    0 as "qa_errors",\n' +
                            '    0 as "system_errors"\n' +
                            'FROM    \n' +
                            '     user_db.accounts a\n' +
                            'INNER JOIN\n' +
                            '    user_db.account_designations ad \n' +
                            '    ON \n' +
                            '    ad.account_id = a.account_id\n' +
                            'WHERE\n' +
                            '    ad.designation_id = 3\n' +
                            '    GROUP BY\n' +
                            '    1\n' +
                            ')t GROUP BY 1 ORDER BY 1 ASC';
                    } else if (this.selectedQueryType === 'Hunter Productivity'){
                        this.sql = 'SELECT DISTINCT(account_gid) as "metric", (SUM(brand) * 1.5 + SUM(sku) + (SUM(dvc) + SUM(facing)) / 2) AS "value", now() as time_sec FROM(\n';
                        for (let i = 0; i < this.selectedProjects.length; i++) {
                            let project = this.selectedProjects[i];
                            let projectQuery = '';
                            projectQuery = 'SELECT\n' +
                                '    a.account_gid,\n' +
                                '    SUM(\n' +
                                '        CASE WHEN(p.product_type = "brand") THEN 1 ELSE 0\n' +
                                '    END\n' +
                                '\t) AS "brand",\n' +
                                '    SUM(\n' +
                                '        CASE WHEN(p.product_type = "sku") THEN 1 ELSE 0\n' +
                                '    END\n' +
                                '\t) AS "sku",\n' +
                                '    SUM(\n' +
                                '        CASE WHEN(p.product_type = "dvc") THEN 1 ELSE 0\n' +
                                '    END\n' +
                                '\t) AS "dvc",\n' +
                                '    SUM(p.product_facing_count) as "facing"\n' +
                                'FROM\n' +
                                `    ${project}.products p\n` +
                                'INNER JOIN\n' +
                                '    user_db.accounts a\n' +
                                'ON\n' +
                                '    a.account_id = p.account_id\n' +
                                'WHERE\n' +
                                '$__timeFilter(p.product_creation_time)\n' +
                                'GROUP BY\n' +
                                '    1';
                            if (i + 1 !== this.selectedProjects.length) {
                                projectQuery += '    UNION ALL\n';
                            }
                            this.sql += projectQuery;
                        }
                        this.sql += '\nUNION ALL\n' +
                            'SELECT\n' +
                            '    a.account_gid,\n' +
                            '    0 as "brand",\n' +
                            '    0 as "sku",\n' +
                            '    0 as "dvc",\n' +
                            '    0 as "facing"\n' +
                            'FROM    \n' +
                            '     user_db.accounts a\n' +
                            'INNER JOIN\n' +
                            '    user_db.account_designations ad \n' +
                            '    ON \n' +
                            '    ad.account_id = a.account_id\n' +
                            'WHERE\n' +
                            '    ad.designation_id = 3\n' +
                            '    GROUP BY\n' +
                            '    1\n' +
                            ')t GROUP BY 1 ORDER BY 1 ASC';
                    } else if (this.selectedQueryType === 'Hunter Probe Chart'){
                        this.sql = 'SELECT SUM(col1) AS "count", DATE(time) as "time" FROM\n(';
                        for (let i = 0; i < this.selectedProjects.length; i++) {
                            let project = this.selectedProjects[i];
                            let projectQuery = '';
                            if (this.selectedHuntType === 'probe') {
                                projectQuery = 'SELECT\n' +
                                    '\t\tCOUNT(p.probe_key_id) as "col1",\n' +
                                    ' \t\tDATE(p.probe_hunter_processed_time) AS "time"\n' +
                                    'FROM\n' +
                                    '    user_db.accounts a\n' +
                                    'LEFT JOIN\n' +
                                    `\t${project}.probe p \n` +
                                    '    ON\t\n' +
                                    '    \tp.probe_processed_hunter_id = a.account_id\n' +
                                    'WHERE\n' +
                                    '\ta.account_gid = $gid\n' +
                                    ' AND $__timeFilter(p.probe_hunter_processed_time)\n' +
                                    'GROUP BY time';
                                if (i + 1 !== this.selectedProjects.length) {
                                    projectQuery += '    UNION ALL\n';
                                }
                            }
                            if (this.selectedHuntType === 'radar') {
                                projectQuery = 'SELECT\n' +
                                    '\t\tCOUNT(rs.radar_source_id) as "col1",\n' +
                                    ' \t\tDATE(rs.creation_time) AS "time"\n' +
                                    'FROM\n' +
                                    '    user_db.accounts a\n' +
                                    'LEFT JOIN\n' +
                                    `\t${project}.radar_sources rs\n` +
                                    '    ON\t\n' +
                                    '    \trs.account_id = a.account_id\n' +
                                    'WHERE\n' +
                                    '\ta.account_gid = $gid\n' +
                                    ' AND $__timeFilter(rs.creation_time)\n' +
                                    'GROUP BY time';
                                if (i + 1 !== this.selectedProjects.length) {
                                    projectQuery += '    UNION ALL\n';
                                }
                            }
                            if (this.selectedHuntType === 'reference') {
                                projectQuery = 'SELECT\n' +
                                    '\t\tCOUNT(ri.reference_info_id) as "col1",\n' +
                                    ' \t\tDATE(ri.reference_hunter_processed_time) AS "time"\n' +
                                    'FROM\n' +
                                    '    user_db.accounts a\n' +
                                    'LEFT JOIN\n' +
                                    `\t${project}.reference_info ri\n` +
                                    '    ON\t\n' +
                                    '    \tri.reference_processed_hunter_id = a.account_id\n' +
                                    'WHERE\n' +
                                    '\ta.account_gid = $gid\n' +
                                    ' AND $__timeFilter(ri.reference_hunter_processed_time)\n' +
                                    'GROUP BY time';
                                if (i + 1 !== this.selectedProjects.length) {
                                    projectQuery += '    UNION ALL\n';
                                }
                            }
                            this.sql += projectQuery;
                        }
                        this.sql += ')t GROUP BY time ORDER BY time ASC';
                    } else if (this.selectedQueryType === 'Hunter Error Chart'){
                        this.sql = 'SELECT SUM(col1) AS "QA Errors", SUM(col2) AS "Rename Errors" ,DATE(time) as "time" FROM\n(';
                        for (let i = 0; i < this.selectedProjects.length; i++) {
                            let project = this.selectedProjects[i];
                            let projectQuery = '';
                            projectQuery = 'SELECT\n' +
                                '    \tCOUNT(pqe.product_id) as "col1",\n' +
                                ' \t\tSUM(\n' +
                                '        \tCASE WHEN (p.product_previous IS NOT NULL OR p.product_qa_previous IS NOT NULL)  THEN 1 ELSE 0\n' +
                                '        END) AS "col2",\n' +
                                ' \t\tDATE(p.product_creation_time) AS "time"\n' +
                                'FROM\n' +
                                `    ${project}.products p\n` +
                                'INNER JOIN\n' +
                                '\tuser_db.accounts a\n' +
                                '    ON\n' +
                                '    \ta.account_id = p.account_id\n' +
                                'LEFT OUTER JOIN\n' +
                                ` \t${project}.product_qa_errors pqe \n` +
                                ' \tON\n' +
                                ' \t\tpqe.product_id = p.product_id\n' +
                                'WHERE\n' +
                                '\ta.account_gid = $gid\n' +
                                ' AND $__timeFilter(p.product_creation_time)\n' +
                                'GROUP BY time';
                            if (i + 1 !== this.selectedProjects.length) {
                                projectQuery += '    UNION ALL\n';
                            }
                            this.sql += projectQuery;
                        }
                        this.sql += ')t GROUP BY time ORDER BY time ASC';
                    } else if (this.selectedQueryType === 'Hunter Trend Chart'){
                        this.sql = 'SELECT SUM(col1) AS "brand", SUM(col2) AS "sku",' +
                            'SUM(col3) AS "dvc", SUM(col4) AS "facing",' +
                            'DATE(time) as "time" FROM\n(';
                        for (let i = 0; i < this.selectedProjects.length; i++) {
                            let project = this.selectedProjects[i];
                            let projectQuery = '';
                            projectQuery = 'SELECT\n' +
                                '    \tSUM(\n' +
                                '        \tCASE WHEN p.product_type = "brand" THEN 1 ELSE 0\n' +
                                '        END) AS "col1",\n' +
                                '        SUM(\n' +
                                '        \tCASE WHEN p.product_type = "sku" THEN 1 ELSE 0\n' +
                                '        END) AS "col2",\n' +
                                '        SUM(\n' +
                                '        \tCASE WHEN p.product_type = "dvc" THEN 1 ELSE 0\n' +
                                '        END) AS "col3",\n' +
                                '        SUM(p.product_facing_count) as "col4",\n' +
                                '        DATE(p.product_creation_time) as "time"\n' +
                                'FROM\n' +
                                `    ${project}.products p\n` +
                                'INNER JOIN\n' +
                                '\tuser_db.accounts a\n' +
                                '    ON\n' +
                                '    \ta.account_id = p.account_id\n' +
                                'WHERE\n' +
                                '\ta.account_gid = $gid\n' +
                                ' AND $__timeFilter(p.product_creation_time)\n' +
                                'GROUP BY time\n';
                            if (i + 1 !== this.selectedProjects.length) {
                                projectQuery += '    UNION ALL\n';
                            }
                            this.sql += projectQuery;
                        }
                        this.sql += ')t GROUP BY time ORDER BY time ASC';
                    } else if (this.selectedQueryType === 'Ticket Chart Query') {
                        this.sql = 'SELECT "Region" ,SUM(COUNT) AS "Count", DATE(time) as "time" FROM\n(';
                        for (let i = 0; i < this.selectedProjects.length; i++) {
                            let project = this.selectedProjects[i];
                            let projectQuery = '';
                            projectQuery = 'SELECT\n' +
                                '    COUNT(pt.project_ticket_system_id) as "COUNT",\n' +
                                '    DATE(pt.ticket_creation_time) as "time"\n' +
                                'FROM\n' +
                                `    ${project}.project_tickets pt\n` +
                                'WHERE pt.ticket_type != "Internal"\n' +
                                'GROUP BY\n' +
                                '    2';
                            if (i + 1 !== this.selectedProjects.length) {
                                projectQuery += '    UNION ALL\n';
                            }
                            this.sql += projectQuery;
                        }
                        this.sql += ')t GROUP BY 3 ORDER BY 3 ASC';
                    } else if (this.selectedQueryType === 'Ticket Type Query') {
                        this.sql = 'SELECT "Region" , SUM(col1) AS "Radar",' +
                            'SUM(col2) AS "Data Health",' +
                            'SUM(col3) AS "Type E - SKU Hunt/Data Collection",' +
                            'SUM(col4) AS "APOC Radar",' +
                            'SUM(col5) AS "NA",' +
                            'SUM(col6) AS "Internal" FROM\n(';
                        for (let i = 0; i < this.selectedProjects.length; i++) {
                            let project = this.selectedProjects[i];
                            let projectQuery = '';
                            projectQuery = 'SELECT\n' +
                                '    SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "Radar" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col1",\n' +
                                '      SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "Data Health" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col2",\n' +
                                '      SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "Type E - SKU Hunt/Data Collection" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col3",\n' +
                                '      SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "APOC Radar" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col4",\n' +
                                '     SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "NA" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col5",\n' +
                                '      SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "Internal" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col6"\n' +
                                'FROM\n' +
                                `    ${project}.project_tickets pt\n` +
                                'WHERE\n' +
                                '    $__timeFilter(pt.ticket_creation_time)';
                            if (i + 1 !== this.selectedProjects.length) {
                                projectQuery += '    UNION ALL\n';
                            }
                            this.sql += projectQuery;
                        }
                        this.sql += ')t';
                    }else if (this.selectedQueryType === 'Ticket Type Complete Query') {
                        this.sql = 'SELECT "Region" , SUM(col1) AS "Radar",' +
                            'SUM(col2) AS "Data Health",' +
                            'SUM(col3) AS "Type E - SKU Hunt/Data Collection",' +
                            'SUM(col4) AS "APOC Radar",' +
                            'SUM(col5) AS "NA",' +
                            'SUM(col6) AS "Internal" FROM\n(';
                        for (let i = 0; i < this.selectedProjects.length; i++) {
                            let project = this.selectedProjects[i];
                            let projectQuery = '';
                            projectQuery = 'SELECT\n' +
                                '    SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "Radar" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col1",\n' +
                                '      SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "Data Health" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col2",\n' +
                                '      SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "Type E - SKU Hunt/Data Collection" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col3",\n' +
                                '      SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "APOC Radar" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col4",\n' +
                                '     SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "NA" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col5",\n' +
                                '      SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "Internal" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col6"\n' +
                                'FROM\n' +
                                `    ${project}.project_tickets pt\n` +
                                'WHERE\n' +
                                '    $__timeFilter(pt.ticket_completion_date)';
                            if (i + 1 !== this.selectedProjects.length) {
                                projectQuery += '    UNION ALL\n';
                            }
                            this.sql += projectQuery;
                        }
                        this.sql += ')t';
                    } else if (this.selectedQueryType === 'Ticket Type Complete Chart Query'){
                        this.sql = 'SELECT "Region" , SUM(col1) AS "Radar",' +
                            'SUM(col2) AS "Data Health",' +
                            'SUM(col3) AS "Type E - SKU Hunt/Data Collection",' +
                            'SUM(col4) AS "APOC Radar",' +
                            'SUM(col5) AS "NA",' +
                            'SUM(col6) AS "Internal",' +
                            'DATE(time) AS "time" FROM\n(';
                        for (let i = 0; i < this.selectedProjects.length; i++) {
                            let project = this.selectedProjects[i];
                            let projectQuery = '';
                            projectQuery = 'SELECT\n' +
                                '    SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "Radar" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col1",\n' +
                                '      SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "Data Health" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col2",\n' +
                                '      SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "Type E - SKU Hunt/Data Collection" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col3",\n' +
                                '      SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "APOC Radar" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col4",\n' +
                                '     SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "NA" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col5",\n' +
                                '      SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "Internal" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col6",\n' +
                                'DATE(pt.ticket_completion_date) as "time"\n' +
                                'FROM\n' +
                                `    ${project}.project_tickets pt\n` +
                                'WHERE\n' +
                                '    $__timeFilter(pt.ticket_completion_date)\n' +
                                'GROUP BY 7';
                            if (i + 1 !== this.selectedProjects.length) {
                                projectQuery += '    UNION ALL\n';
                            }
                            this.sql += projectQuery;
                        }
                        this.sql += ')t GROUP BY 8 ORDER BY 8 ASC';
                    }else if (this.selectedQueryType === 'Ticket Type Chart Query'){
                        this.sql = 'SELECT "Region" , SUM(col1) AS "Radar",' +
                            'SUM(col2) AS "Data Health",' +
                            'SUM(col3) AS "Type E - SKU Hunt/Data Collection",' +
                            'SUM(col4) AS "APOC Radar",' +
                            'SUM(col5) AS "NA",' +
                            'SUM(col6) AS "Internal",' +
                            'DATE(time) AS "time" FROM\n(';
                        for (let i = 0; i < this.selectedProjects.length; i++) {
                            let project = this.selectedProjects[i];
                            let projectQuery = '';
                            projectQuery = 'SELECT\n' +
                                '    SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "Radar" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col1",\n' +
                                '      SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "Data Health" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col2",\n' +
                                '      SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "Type E - SKU Hunt/Data Collection" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col3",\n' +
                                '      SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "APOC Radar" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col4",\n' +
                                '     SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "NA" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col5",\n' +
                                '      SUM(\n' +
                                '        CASE WHEN pt.ticket_type = "Internal" THEN 1 ELSE 0\n' +
                                '    \tEND\n' +
                                '\t) AS "col6",\n' +
                                'DATE(pt.ticket_creation_time) as "time"\n' +
                                'FROM\n' +
                                `    ${project}.project_tickets pt\n` +
                                'WHERE\n' +
                                '    $__timeFilter(pt.ticket_creation_time)\n' +
                                'GROUP BY 7';
                            if (i + 1 !== this.selectedProjects.length) {
                                projectQuery += '    UNION ALL\n';
                            }
                            this.sql += projectQuery;
                        }
                        this.sql += ')t GROUP BY 8 ORDER BY 8 ASC';
                    } else if (this.selectedQueryType === 'Ticket Progress Query') {
                        this.sql = 'SELECT "Region" as "", SUM(col1) AS "Current Week Inflow",' +
                            'SUM(col2) as "Closed Tickets for the Week",' +
                            'SUM(col3) as "In Progress",' +
                            'SUM(col4) as "Bought forward from last week" FROM\n(';
                        for (let i = 0; i < this.selectedProjects.length; i++) {
                            let project = this.selectedProjects[i];
                            let projectQuery = '';
                            projectQuery = 'SELECT\n' +
                                '    SUM(\n' +
                                '        CASE WHEN YEARWEEK(\n' +
                                '            DATE(pt.ticket_creation_time),\n' +
                                '            1\n' +
                                '        ) = YEARWEEK(CURDATE(), 1) THEN 1 ELSE 0\n' +
                                '        END) AS "col1",\n' +
                                '    SUM(\n' +
                                '        CASE WHEN YEARWEEK(\n' +
                                '            DATE(pt.ticket_completion_date),\n' +
                                '            1\n' +
                                '        ) = YEARWEEK(CURDATE(), 1) THEN 1 ELSE 0\n' +
                                '        END) AS "col2",\n' +
                                '    SUM(\n' +
                                '        CASE WHEN pt.ticket_status = "IN PROGRESS" THEN 1 ELSE 0\n' +
                                '    END\n' +
                                '\t) AS "col3",\n' +
                                '\tSUM(\n' +
                                '    \tCASE WHEN WEEK(CURDATE()) != WEEK(pt.ticket_creation_time) AND pt.ticket_status = "IN PROGRESS" THEN 1 ELSE 0\n' +
                                '    END) AS "col4"\n' +
                                'FROM\n' +
                                `    ${project}.project_tickets pt\n` +
                                'WHERE pt.ticket_type != "Internal"';
                            if (i + 1 !== this.selectedProjects.length) {
                                projectQuery += '    UNION ALL\n';
                            }
                            this.sql += projectQuery;
                        }
                        this.sql += ')t';
                    } else if (this.selectedQueryType === 'Probe Processed Chart Query') {
                        this.sql = 'SELECT "Region" ,SUM(COUNT) AS "Count", DATE(time) as "time" FROM\n(';
                        for (let i = 0; i < this.selectedProjects.length; i++) {
                            let project = this.selectedProjects[i];
                            let projectQuery = '';
                            let countString = '';
                            let joinString = '';
                            let whereString = '';
                            if (this.selectedHuntType === 'probe') {
                                countString = '        COUNT(p.probe_key_id) AS "Count", p.probe_hunter_processed_time as "time",\n';
                                joinString = '    LEFT OUTER JOIN\n' +
                                    `        ${project}.probe p\n ` +
                                    '    ON\n' +
                                    '        p.probe_status_id = ps.probe_status_id\n';
                                whereString = 'WHERE $__timeFilter(p.probe_hunter_processed_time)';
                            } else if (this.selectedHuntType === 'radar') {
                                countString = '        COUNT(rs.radar_source_id) AS "Count", rs.creation_time as "time", \n';
                                joinString = '    LEFT OUTER JOIN\n' +
                                    `        ${project}.radar_sources rs\n ` +
                                    '    ON\n' +
                                    '        rs.radar_status_id = ps.probe_status_id\n';
                                whereString = 'WHERE $__timeFilter(rs.creation_time)';
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
                                whereString +
                                '    GROUP BY\n' +
                                '        ps.probe_status_name, 2\n';
                            if (i + 1 !== this.selectedProjects.length) {
                                projectQuery += '    UNION ALL\n';
                            }
                            this.sql += projectQuery;
                        }
                        this.sql += ')t GROUP BY 3 ORDER BY 3 ASC';

                    } else if (this.selectedQueryType === 'Probe Status Query'){
                        this.sql = 'SELECT SUM(COUNT) AS "Count", probe_status_name FROM\n(';
                        for (let i = 0; i < this.selectedProjects.length; i++) {
                            let project = this.selectedProjects[i];
                            let projectQuery = '';
                            let countString = '';
                            let joinString = '';
                            let whereString = '';
                            if (this.selectedHuntType === 'probe') {
                                countString = '        COUNT(p.probe_key_id) AS "Count",\n'
                                joinString = '    LEFT OUTER JOIN\n' +
                                    `        ${project}.probe p\n ` +
                                    '    ON\n' +
                                    '        p.probe_status_id = ps.probe_status_id\n';
                                whereString = 'WHERE $__timeFilter(p.probe_hunter_processed_time)';
                            } else if (this.selectedHuntType === 'radar') {
                                countString = '        COUNT(rs.radar_source_id) AS "Count",\n';
                                joinString = '    LEFT OUTER JOIN\n' +
                                    `        ${project}.radar_sources rs\n ` +
                                    '    ON\n' +
                                    '        rs.radar_status_id = ps.probe_status_id\n';
                                whereString = 'WHERE $__timeFilter(rs.creation_time)';
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
                                whereString +
                                '    GROUP BY\n' +
                                '        ps.probe_status_name\n';
                            if (i + 1 !== this.selectedProjects.length) {
                                projectQuery += '    UNION ALL\n';
                            }
                            this.sql += projectQuery;
                        }
                        this.sql += ')t GROUP BY 2';
                    }else if (this.selectedQueryType === 'Product Type Hunted Chart Query') {
                        this.sql = 'SELECT product_type, SUM(COUNT) AS "Count", DATE(product_qa_datetime) as "time" FROM\n(';
                        for (let i = 0; i < this.selectedProjects.length; i++) {
                            let project = this.selectedProjects[i];
                            let projectQuery = '';
                            projectQuery = '  SELECT\n' +
                                '        COUNT(p.product_id) AS "Count",\n' +
                                '        p.product_type,\n' +
                                '        p.product_qa_datetime\n' +
                                '    FROM\n' +
                                `        ${project}.products p\n` +
                                '    WHERE\n' +
                                '       (p.product_qa_status = "active" OR p.product_qa_status = "approved") AND $__timeFilter(p.product_qa_datetime)\n' +
                                '    GROUP BY\n' +
                                '        p.product_type, p.product_qa_datetime\n';
                            if (i + 1 !== this.selectedProjects.length) {
                                projectQuery += '    UNION ALL\n';
                            }
                            this.sql += projectQuery;
                        }
                        this.sql += ')t GROUP BY 1, 3';
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
