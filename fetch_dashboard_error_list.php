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

$custom_project_list_flag = false;

if (trim($_POST['project_list']) != '') {
    // getting the list of projects into an array from the passes string.
    $project_list = explode(",",$_POST['project_list']);
}

// gets the users custom project list if it is not empty
if (count($project_list) > 0) {
    $project_array = $project_list;
    $custom_project_list_flag = true;
    
} else {
    $sql = 'SELECT project_db_name FROM `project_db`.projects WHERE project_region = :region';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['region'=>$_POST['project_region']]);
    $project_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$begin = new DateTime(strval($_POST['start_datetime']));
$end = new DateTime(strval($_POST['end_datetime']));
$end_date_for_loop = $end->modify('+1 day');

$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($begin, $interval, $end_date_for_loop);
$summary = array();
$count = 0;
$role = '';

for ($i = 0; $i < count($project_array); $i++) {
    if (!$custom_project_list_flag) {
        $dbname = $project_array[$i]["project_db_name"];
    } else {
        $dbname = $project_array[$i];
    }
    $dsn = 'mysql:host='.$host.';dbname='.$dbname;
    $pdo = new PDO($dsn, $user, $pwd);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $project_hunters = array();
    if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Supervisor') {
        $role = 'Super';
        $sql = 'SELECT DISTINCT probe_processed_hunter_id AS "id", account_gid FROM (SELECT DISTINCT b.probe_processed_hunter_id, a.account_gid FROM '.$dbname.'.probe b INNER JOIN user_db.accounts a ON b.probe_processed_hunter_id = a.account_id INNER JOIN user_db.account_designations g ON g.account_id = a.account_id WHERE g.designation_id = 3  AND ( b.probe_hunter_processed_time >= :start_datetime AND b.probe_hunter_processed_time <= :end_datetime)  OR DATE(b.probe_hunter_processed_time) = :start_datetime UNION ALL SELECT DISTINCT c.radar_hunter_id AS "probe_processed_hunter_id", a.account_gid FROM '.$dbname.'.radar_hunt c INNER JOIN user_db.accounts a ON c.radar_hunter_id = a.account_id INNER JOIN user_db.account_designations g ON g.account_id = a.account_id WHERE g.designation_id = 3 AND (c.radar_processed_time >= :start_datetime AND c.radar_processed_time <= :end_datetime OR DATE(c.radar_processed_time) = :start_datetime )UNION ALL SELECT DISTINCT d.reference_processed_hunter_id AS "probe_processed_hunter_id", a.account_gid FROM '.$dbname.'.reference_info d INNER JOIN user_db.accounts a ON d.reference_processed_hunter_id = a.account_id INNER JOIN user_db.account_designations g ON g.account_id = a.account_id WHERE g.designation_id = 3 AND (d.reference_hunter_processed_time >= :start_datetime AND d.reference_hunter_processed_time <= :end_datetime) OR DATE(d.reference_hunter_processed_time) = :start_datetime ) t3';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $project_hunters = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    for ($m = 0; $m < count($project_hunters); $m++) {
        $project_hunters[$m]["error_sum_index"] = 0;
        foreach ($period as $dt) {
            $date = $dt->format("Y-m-d");
            $sql = "SELECT COUNT(a.product_id) as 'count', b.error_id FROM ".$dbname.".products a INNER JOIN ".$dbname.".product_qa_errors b ON a.product_id = b.product_id WHERE a.account_id = :account_id AND (a.product_qa_datetime >= :start_datetime AND a.product_qa_datetime <= :end_datetime OR DATE(a.product_qa_datetime) = :start_datetime) AND a.product_status = 2 GROUP BY b.error_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['account_id'=>$project_hunters[$m][id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
            $error_summary = $stmt->fetchAll(PDO::FETCH_ASSOC);
            for($l = 0; $l < count($error_summary); $l++) {
                $current_index = $project_hunters[$m]["error_sum_index"];
                $found = false;
                for ($m = 0; $m < $current_index; $m++) {
                   if ($error_summary[$l]["error_id"] == $error_chart[$m]["error_id"]) {
                       $error_chart[$m]["count"] += $error_summary[$l]["count"];
                       $found = true;
                       break;
                   }
                }
                if(!$found) {
                    $sql = 'SELECT a.project_error_name FROM '.$dbname.'.project_errors a WHERE a.project_error_id = :error_id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['error_id'=>(int)$error_summary[$l]["error_id"]]);
                    $error_info = $stmt->fetch(PDO::FETCH_OBJ);
                    $error_chart[$current_index]["error_name"] = $error_info->project_error_name;
                    $error_chart[$current_index]["error_id"] = (int)$error_summary[$l]["error_id"];
                    $error_chart[$current_index]["count"] = (int)$error_summary[$l]["count"];
                    $error_chart[$current_index]["hunter_gid"] = $project_hunters[$m][account_gid];
                    $project_hunters[$m]["error_sum_index"]++;
                }
            }
        }
    }
}

$return_arr[] = array("summary"=>$error_chart);
echo json_encode($return_arr);
?>