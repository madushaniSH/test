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
    if ($_SESSION['role'] == 'SRT'){
        $role = 'SRT';
        $project_hunters[0][id] = $_SESSION['id'];
        $sql = 'SELECT a.account_gid FROM `user_db`.accounts a WHERE a.account_id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id'=>$project_hunters[0][id]]);
        $user_info = $stmt->fetch(PDO::FETCH_OBJ);
        $project_hunters[0][account_gid] = $user_info->account_gid;

    } else if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Supervisor') {
        $role = 'Super';
        $sql = 'SELECT DISTINCT probe_processed_hunter_id AS "id", account_gid FROM (SELECT DISTINCT b.probe_processed_hunter_id, a.account_gid FROM '.$dbname.'.probe b INNER JOIN user_db.accounts a ON b.probe_processed_hunter_id = a.account_id INNER JOIN user_db.account_designations g ON g.account_id = a.account_id WHERE g.designation_id = 3  AND ( b.probe_hunter_processed_time >= :start_datetime AND b.probe_hunter_processed_time <= :end_datetime)  OR DATE(b.probe_hunter_processed_time) = :start_datetime UNION ALL SELECT DISTINCT c.radar_hunter_id AS "probe_processed_hunter_id", a.account_gid FROM '.$dbname.'.radar_hunt c INNER JOIN user_db.accounts a ON c.radar_hunter_id = a.account_id INNER JOIN user_db.account_designations g ON g.account_id = a.account_id WHERE g.designation_id = 3 AND (c.radar_processed_time >= :start_datetime AND c.radar_processed_time <= :end_datetime OR DATE(c.radar_processed_time) = :start_datetime )UNION ALL SELECT DISTINCT d.reference_processed_hunter_id AS "probe_processed_hunter_id", a.account_gid FROM '.$dbname.'.reference_info d INNER JOIN user_db.accounts a ON d.reference_processed_hunter_id = a.account_id INNER JOIN user_db.account_designations g ON g.account_id = a.account_id WHERE g.designation_id = 3 AND (d.reference_hunter_processed_time >= :start_datetime AND d.reference_hunter_processed_time <= :end_datetime) OR DATE(d.reference_hunter_processed_time) = :start_datetime ) t3';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $project_hunters = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    for ($m = 0; $m < count($project_hunters); $m++) {
        foreach ($period as $dt) {
            $date = $dt->format("Y-m-d");
            $sql = 'SELECT COUNT(*) FROM '.$dbname.'.products a WHERE a.product_type ="sku" AND a.account_id = :account_id AND (DATE(a.product_creation_time) = :date) AND a.product_status = 2';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['account_id'=>$project_hunters[$m][id], 'date'=>$date]);
            $sku_count = $stmt->fetchColumn();
            if ($sku_count == NULL) {
                $sku_count = 0;
            }
            $sql = 'SELECT COUNT(*) FROM '.$dbname.'.products a WHERE a.product_type ="brand" AND a.account_id = :account_id AND (DATE(a.product_creation_time) = :date) AND a.product_status = 2';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['account_id'=>$project_hunters[$m][id], 'date'=>$date]);
            $brand_count = $stmt->fetchColumn();
            if ($brand_count == NULL) {
                $brand_count = 0;
            }
            $sql = 'SELECT COUNT(*) FROM '.$dbname.'.products a WHERE a.product_type ="dvc" AND a.account_id = :account_id AND (DATE(a.product_creation_time) = :date) AND a.product_status = 2';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['account_id'=>$project_hunters[$m][id], 'date'=>$date]);
            $dvc_count = $stmt->fetchColumn();
            if ($dvc_count == NULL) {
                $dvc_count = 0;
            }
            $sql = 'SELECT SUM(a.product_facing_count) FROM '.$dbname.'.products a WHERE a.account_id = :account_id AND (DATE(a.product_creation_time) = :date) AND a.product_status = 2';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['account_id'=>$project_hunters[$m][id], 'date'=>$date]);
            $facing_count = $stmt->fetchColumn();
            if ($facing_count == NULL) {
                $facing_count = 0;
            }
            $sql = "SELECT COUNT(a.product_id) FROM ".$dbname.".products a INNER JOIN ".$dbname.".product_qa_errors b ON a.product_id = b.product_id WHERE a.account_id = :account_id AND DATE(a.product_qa_datetime) = :date AND a.product_status = 2";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['account_id'=>$project_hunters[$m][id], 'date'=>$date]);
            $error_count = $stmt->fetchColumn();
            if ($error_count == NULL) {
                $error_count = 0;
            }
            if ($sku_count != 0 || $brand_count != 0 || $dvc_count != 0 || $facing_count != 0 || $error_count != 0){
                $sql = 'SELECT a.product_id, a.product_name, a.product_alt_design_name, a.product_type, a.product_creation_time, a.product_qa_datetime, a.product_qa_status FROM '.$dbname.'.products a WHERE (DATE(a.product_creation_time) = :date OR DATE(a.product_qa_datetime) = :date) AND a.account_id = :id';
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['id'=>$project_hunters[$m][id], 'date'=>$date]);
                $product_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                
                
                $summary[$count] = array_fill_keys(array('project_name','date','sku', 'brand', 'dvc', 'facing', 'errors'),'');
                $summary[$count][project_name] = $dbname;
                $summary[$count][user_gid] = $project_hunters[$m][account_gid];
                $summary[$count][date] = $date;
                $summary[$count]['sku'] = (int)$sku_count;
                $summary[$count]['brand'] = (int)$brand_count;
                $summary[$count]['dvc'] = (int)$dvc_count;
                $summary[$count]['facing'] = (int)$facing_count;
                $summary[$count]['errors'] = (int)$error_count;
                
                for ($k = 0; $k < count($product_info); $k++) {
                    $sql = 'SELECT a.project_error_image_location FROM '.$dbname.'.project_error_images a WHERE a.product_id = :product_id LIMIT 1';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['product_id'=>$product_info[$k][product_id]]);
                    $row_count = $stmt->rowCount(PDO::FETCH_OBJ);
                    $file_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $sql = 'SELECT a.project_error_name FROM '.$dbname.'.product_qa_errors b INNER JOIN '.$dbname.'.project_errors a ON b.error_id = a.project_error_id WHERE b.product_id = :product_id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['product_id'=>$product_info[$k][product_id]]);
                    $qa_errors = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $error_string = '';
                    for ($j = 0; $j < count($qa_errors); $j++) {
                        $error_string .= $qa_errors[$j][project_error_name].',';
                    }
                    if ($error_string != '') {
                        $error_string = rtrim($error_string, ",");
                    } 
                    $product_info[$k]["error_string"] = $error_string;
                    $product_info[$k]["error_url"] = $file_info;
                }
                
                $summary[$count]['product_info'] = array();
                array_push($summary[$count]['product_info'], $product_info);
                
                $count++;
            }
        }
    }
}

$return_arr[] = array("summary"=>$summary, "role"=>$role);
echo json_encode($return_arr);
?>