<?php

/*
Function finds the hunters index in the passed array and increments it with passed error count
*/
function add_hunter_error_count (&$hunter_array, &$hunter_info, $error_count) {
    $i = 0;
    $found = false;
    for ($i = 0; $i < count($hunter_array); $i++) {
        if ($hunter_array[$i]["hunter_id"] == $hunter_info[id]) {
            $found = true;
            $hunter_array[$i]["error_count"] += (int)$error_count;
            break;
        }
    }
    if (!$found) {
        $hunter_array[$i]["hunter_id"] = $hunter_info[id];
        $hunter_array[$i]["hunter_gid"] = $hunter_info[account_gid];
        $hunter_array[$i]["error_count"] = (int)$error_count;
    }
}

/*
    Function adds the passed errors name and count to the region array
*/
function add_region_error_count (&$region_array, $error_id, $error_count, $error_name) {
    $i = 0;
    $found = false;
    for ($i = 0; $i < count($region_array["errors"]); $i++) {
        if ($region_array["errors"][$i]["error_id"] == $error_id) {
            $found = true;
            $region_array["errors"][$i]["error_count"] += $error_count;
            break;
        }
    }
    if (!$found) {
        $region_array["errors"][$i]["error_id"] = $error_id;
        $region_array["errors"][$i]["error_count"] = $error_count;
        $region_array["errors"][$i]["error_name"] = $error_name;

    }
}

?>
<?php
/*
Author: Malika Liyanage
*/
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
    header('Location: login_auth_one.php');
    exit();
}
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
    header('Location: login_auth_one.php');
    exit();
}
// Current settings to connect to the user account database
require('user_db_connection.php');
$dbname = 'project_db';
// Setting up the DSN
$dsn = 'mysql:host='.$host.';dbname='.$dbname;

/*
Attempts to connect to the databse, if no connection was estabishled
kills the script
*/
try{
    // Creating a new PDO instance
    $pdo = new PDO($dsn, $user, $pwd);
    // setting the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

catch(PDOException $e){
    // throws error message
    echo "<p>Connection to database failed<br>Reason: ".$e->getMessage().'</p>';
    exit();
}

$error_chart = array();
$summary = array();
$count = 0;
$role = '';
$error_chart_index = 0;
$project_summary = array();
$region_error_count = 0;
if ($_POST['project_region'] != 'ALL') {
    if (trim($_POST['project_list']) != '') {
        // getting the list of projects into an array from the passes string.
        $project_list = explode(",",$_POST['project_list']);
    }
    
    // gets the users custom project list if it is not empty
    if (count($project_list) > 0) {
        for ($i = 0; $i < count($project_list); $i++) {
            $project_array[$i]["project_db_name"] = $project_list[$i];
        }
    } else {
        $sql = 'SELECT project_db_name FROM `project_db`.projects WHERE project_region = :region';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['region'=>$_POST['project_region']]);
        $project_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    for ($i = 0; $i < count($project_array); $i++) {
        $dbname = $project_array[$i]["project_db_name"];
        $dsn = 'mysql:host='.$host.';dbname='.$dbname;
        $pdo = new PDO($dsn, $user, $pwd);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Supervisor') {
            $role = 'Super';
            //$sql = 'SELECT DISTINCT a.account_id AS "id", b.account_gid FROM '.$dbname.'.products a INNER JOIN user_db.accounts b ON b.account_id = a.account_id INNER JOIN user_db.account_designations g ON g.account_id = b.account_id WHERE g.designation_id = 3 AND ((a.product_qa_datetime >= :start_datetime AND a.product_qa_datetime <= :end_datetime))';
            $sql = 'SELECT DISTINCT probe_processed_hunter_id AS "id", account_gid FROM (SELECT DISTINCT b.probe_processed_hunter_id, a.account_gid FROM '.$dbname.'.probe b INNER JOIN user_db.accounts a ON b.probe_processed_hunter_id = a.account_id INNER JOIN user_db.account_designations g ON g.account_id = a.account_id WHERE g.designation_id = 3  AND ( b.probe_hunter_processed_time >= :start_datetime AND b.probe_hunter_processed_time <= :end_datetime)  OR DATE(b.probe_hunter_processed_time) = :start_datetime UNION ALL SELECT DISTINCT c.radar_hunter_id AS "probe_processed_hunter_id", a.account_gid FROM '.$dbname.'.radar_hunt c INNER JOIN user_db.accounts a ON c.radar_hunter_id = a.account_id INNER JOIN user_db.account_designations g ON g.account_id = a.account_id WHERE g.designation_id = 3 AND (c.radar_processed_time >= :start_datetime AND c.radar_processed_time <= :end_datetime OR DATE(c.radar_processed_time) = :start_datetime )UNION ALL SELECT DISTINCT d.reference_processed_hunter_id AS "probe_processed_hunter_id", a.account_gid FROM '.$dbname.'.reference_info d INNER JOIN user_db.accounts a ON d.reference_processed_hunter_id = a.account_id INNER JOIN user_db.account_designations g ON g.account_id = a.account_id WHERE g.designation_id = 3 AND (d.reference_hunter_processed_time >= :start_datetime AND d.reference_hunter_processed_time <= :end_datetime) OR DATE(d.reference_hunter_processed_time) = :start_datetime ) t3';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
            $project_hunters = $stmt->fetchAll(PDO::FETCH_ASSOC);
            for ($m = 0; $m < count($project_hunters); $m++) {
                $sql = "SELECT COUNT(b.error_id) as 'count', b.error_id FROM ".$dbname.".products a INNER JOIN ".$dbname.".product_qa_errors b ON a.product_id = b.product_id WHERE a.account_id = :account_id AND ((a.product_qa_datetime >= :start_datetime AND a.product_qa_datetime <= :end_datetime)) AND a.product_status = 2 GROUP BY b.error_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['account_id'=>$project_hunters[$m][id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
                $error_summary = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                for($l = 0; $l < count($error_summary); $l++) {
                    $found = false;
                    for ($n = 0; $n < $error_chart_index; $n++) {
                        if ($error_summary[$l]["error_id"] == $error_chart[$n]["error_id"]) {
                            $found = true;
                            $error_chart[$n]["count"] += (int)$error_summary[$l]["count"];
                            add_hunter_error_count($error_chart[$n]["hunter_info"], $project_hunters[$m],(int)$error_summary[$l]["count"]);
                            break;
                        }
                    }
                    if(!$found) {
                        $sql = 'SELECT a.project_error_name FROM '.$dbname.'.project_errors a WHERE a.project_error_id = :error_id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(['error_id'=>(int)$error_summary[$l]["error_id"]]);
                        $error_info = $stmt->fetch(PDO::FETCH_OBJ);
                        $error_chart[$error_chart_index]["error_name"] = $error_info->project_error_name;
                        $error_chart[$error_chart_index]["error_id"] = (int)$error_summary[$l]["error_id"];
                        $error_chart[$error_chart_index]["count"] = (int)$error_summary[$l]["count"];
                        add_hunter_error_count($error_chart[$error_chart_index]["hunter_info"], $project_hunters[$m],(int)$error_summary[$l]["count"]);
                        $error_chart_index++;
                    }
                }
            }
        }
    }
} else {
    $sql = 'SELECT project_db_name, project_region FROM `project_db`.projects WHERE 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $project_array = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $project_summary[0]["region"] = "AMER";
    $project_summary[0]["errors"] = array();
    $project_summary[1]["region"] = "EMEA";
    $project_summary[1]["errors"] = array();
    $project_summary[2]["region"] = "APAC";
    $project_summary[2]["errors"] = array();
    $project_summary[3]["region"] = "DPG";
    $project_summary[3]["errors"] = array();

    for ($i = 0; $i < count($project_array); $i++) {
        $dbname = $project_array[$i]["project_db_name"];
        $dsn = 'mysql:host='.$host.';dbname='.$dbname;
        $pdo = new PDO($dsn, $user, $pwd);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = 'SELECT DISTINCT probe_processed_hunter_id AS "id", account_gid FROM (SELECT DISTINCT b.probe_processed_hunter_id, a.account_gid FROM '.$dbname.'.probe b INNER JOIN user_db.accounts a ON b.probe_processed_hunter_id = a.account_id INNER JOIN user_db.account_designations g ON g.account_id = a.account_id WHERE g.designation_id = 3  AND ( b.probe_hunter_processed_time >= :start_datetime AND b.probe_hunter_processed_time <= :end_datetime)  OR DATE(b.probe_hunter_processed_time) = :start_datetime UNION ALL SELECT DISTINCT c.radar_hunter_id AS "probe_processed_hunter_id", a.account_gid FROM '.$dbname.'.radar_hunt c INNER JOIN user_db.accounts a ON c.radar_hunter_id = a.account_id INNER JOIN user_db.account_designations g ON g.account_id = a.account_id WHERE g.designation_id = 3 AND (c.radar_processed_time >= :start_datetime AND c.radar_processed_time <= :end_datetime OR DATE(c.radar_processed_time) = :start_datetime )UNION ALL SELECT DISTINCT d.reference_processed_hunter_id AS "probe_processed_hunter_id", a.account_gid FROM '.$dbname.'.reference_info d INNER JOIN user_db.accounts a ON d.reference_processed_hunter_id = a.account_id INNER JOIN user_db.account_designations g ON g.account_id = a.account_id WHERE g.designation_id = 3 AND (d.reference_hunter_processed_time >= :start_datetime AND d.reference_hunter_processed_time <= :end_datetime) OR DATE(d.reference_hunter_processed_time) = :start_datetime ) t3';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $project_hunters = $stmt->fetchAll(PDO::FETCH_ASSOC);

        for ($m = 0; $m < count($project_hunters); $m++) {
            $sql = "SELECT COUNT(b.error_id) as 'count', b.error_id FROM ".$dbname.".products a INNER JOIN ".$dbname.".product_qa_errors b ON a.product_id = b.product_id WHERE a.account_id = :account_id AND ((a.product_qa_datetime >= :start_datetime AND a.product_qa_datetime <= :end_datetime)) AND a.product_status = 2 GROUP BY b.error_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['account_id'=>$project_hunters[$m][id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
            $error_summary = $stmt->fetchAll(PDO::FETCH_ASSOC);

            for($l = 0; $l < count($error_summary); $l++) {
                $found = false;
                if(!$found) {
                    $sql = 'SELECT a.project_error_name FROM '.$dbname.'.project_errors a WHERE a.project_error_id = :error_id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['error_id'=>(int)$error_summary[$l]["error_id"]]);
                    $error_info = $stmt->fetch(PDO::FETCH_OBJ);
                    switch ($project_array[$i][project_region]) {
                        case "AMER" : 
                            add_region_error_count($project_summary[0], (int)$error_summary[$l]["error_id"], (int)$error_summary[$l]["count"], $error_info->project_error_name);
                            $region_error_count += (int)$error_summary[$l]["count"];
                            break;
                        case "EMEA" : 
                            add_region_error_count($project_summary[1], (int)$error_summary[$l]["error_id"], (int)$error_summary[$l]["count"], $error_info->project_error_name);
                            $region_error_count++;
                            break;
                        case "APAC" : 
                            add_region_error_count($project_summary[2], (int)$error_summary[$l]["error_id"], (int)$error_summary[$l]["count"], $error_info->project_error_name);
                            $region_error_count++;
                            break;
                        case "DPG" : 
                            add_region_error_count($project_summary[3], (int)$error_summary[$l]["error_id"], (int)$error_summary[$l]["count"], $error_info->project_error_name);
                            $region_error_count++;
                            break;
                    }
                }
            }
        }
    }
}

$return_arr[] = array("summary"=>$error_chart, "project_summary"=>$project_summary, "region_error_count"=>$region_error_count);
echo json_encode($return_arr);
?>