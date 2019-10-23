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


$hunter_summary = array();
$sql = 'SELECT project_name, project_db_name, project_region FROM projects WHERE project_region = "EMEA"';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$project_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
$max_size = 0;
for ($i = 0; $i < count($project_array); $i++) {
    $dbname = $project_array[$i]["project_db_name"];
    $dsn = 'mysql:host='.$host.';dbname='.$dbname;
    $pdo = new PDO($dsn, $user, $pwd);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = 'SELECT DISTINCT probe_processed_hunter_id, account_gid FROM (SELECT DISTINCT b.probe_processed_hunter_id, a.account_gid FROM '.$dbname.'.probe b INNER JOIN user_db.accounts a ON b.probe_processed_hunter_id = a.account_id INNER JOIN user_db.account_designations g ON g.account_id = a.account_id WHERE g.designation_id = 3  AND ( b.probe_hunter_processed_time >= :start_datetime AND b.probe_hunter_processed_time <= :end_datetime ) UNION ALL SELECT DISTINCT c.radar_hunter_id AS "probe_processed_hunter_id", a.account_gid FROM '.$dbname.'.radar_hunt c INNER JOIN user_db.accounts a ON c.radar_hunter_id = a.account_id INNER JOIN user_db.account_designations g ON g.account_id = a.account_id WHERE g.designation_id = 3 AND (c.radar_processed_time >= :start_datetime AND c.radar_processed_time <= :end_datetime )UNION ALL SELECT DISTINCT d.reference_processed_hunter_id AS "probe_processed_hunter_id", a.account_gid FROM '.$dbname.'.reference_info d INNER JOIN user_db.accounts a ON d.reference_processed_hunter_id = a.account_id INNER JOIN user_db.account_designations g ON g.account_id = a.account_id WHERE g.designation_id = 3 AND (d.reference_hunter_processed_time >= :start_datetime AND d.reference_hunter_processed_time <= :end_datetime) ) t3';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
    $this_project_hunters = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count_hunters = count($this_project_hunters);
    for ($j = 0; $j < $count_hunters; $j++) {
        $found = false;
        for ($k = 0; $k < count($hunter_summary); $k++) {
            if ($hunter_summary[$k]["probe_processed_hunter_id"] == $this_project_hunters[$j]["probe_processed_hunter_id"]) {
                $found = true;
                $project_count = $hunter_summary[$k]["project_count"];
                $hunter_summary[$k]["projects"][$project_count] = $dbname;
                $hunter_summary[$k]["project_region"][$project_count] = $project_array[$i]["project_region"];
                $hunter_summary[$k]["project_count"]++;
                break;
            }
        }
        if(!$found) {
            $hunter_summary[$max_size]["probe_processed_hunter_id"] = $this_project_hunters[$j]["probe_processed_hunter_id"];
            $sql = 'SELECT a.account_profile_picture_location, CONCAT (a.account_first_name," ", a.account_last_name) AS name FROM user_db.accounts a WHERE  a.account_id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id'=>$hunter_summary[$max_size]["probe_processed_hunter_id"]]);
            $user_information = $stmt->fetch(PDO::FETCH_OBJ);
            $hunter_summary[$max_size]["name"] = $user_information->name;
            $hunter_summary[$max_size]["account_gid"] = $this_project_hunters[$j]["account_gid"];
            $hunter_summary[$max_size]["project_count"] = 0;
            $hunter_summary[$max_size]["rename_error_count"] = 0;
            $project_count = $hunter_summary[$max_size]["project_count"];
            $hunter_summary[$max_size]["projects"][$project_count] = $dbname;
            $hunter_summary[$max_size]["project_region"][$project_count] = $project_array[$i]["project_region"];
            $hunter_summary[$max_size]["project_count"]++;
            $max_size++;
        }
    }
    $pdo = NULL;
}
for ($i = 0; $i < count($hunter_summary); $i++){
    $count_projects = $hunter_summary[$i]["project_count"];
    for ($j = 0; $j < $count_projects; $j++) {
        $dbname = $hunter_summary[$i]["projects"][$j];
        $dsn = 'mysql:host='.$host.';dbname='.$dbname;
        $pdo = new PDO($dsn, $user, $pwd);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = 'SELECT COUNT(a.product_id) 
        FROM '.$dbname.'.`product_qa_errors` a 
        INNER JOIN '.$dbname.'.project_errors b ON a.`error_id` = b.project_error_id 
        INNER JOIN '.$dbname.'.products c ON c.product_id = a.product_id 
        WHERE b.project_error_name LIKE "%Name%" OR b.project_error_name LIKE "%Naming%" AND c.account_id = :account_id
        AND (c.product_qa_datetime >= :start_datetime AND c.product_qa_datetime <= :end_datetime)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $error_count = $stmt->fetchColumn();
        $hunter_summary[$i]["rename_error_count"] += $error_count;
    }
    unset($hunter_summary[$i][probe_processed_hunter_id]);
    unset($hunter_summary[$i][project_count]);
    unset($hunter_summary[$i][projects]);
    unset($hunter_summary[$i][project_region]);
}
/*
$sql = 'SELECT project_name, project_db_name, project_region FROM projects';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$project_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
for ($i = 0; $i < count($project_info); $i++) {
    $dbname = $project_info[$i]["project_db_name"];
    $dsn = 'mysql:host='.$host.';dbname='.$dbname;
    $pdo = new PDO($dsn, $user, $pwd);

    $sql = 'SELECT COUNT(DISTINCT(probe_processed_hunter_id)) FROM probe WHERE probe_hunter_processed_time >= :start_datetime AND probe_hunter_processed_time <= :end_datetime';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>$_POST['start_datetime'], 'end_datetime'=>$_POST['end_datetime']]);
    $hunter_count = $stmt->fetchColumn();
    $project_info[$i]["Hunter Count"] = $hunter_count;

        $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="brand" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $brand_count = $stmt->fetchColumn();
        $project_info[$i]["Brand Hunted"] = $brand_count;
        $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="sku" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $sku_count = $stmt->fetchColumn();
        $project_info[$i]["SKU Hunted"] = $sku_count;
        $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="dvc" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $dvc_count = $stmt->fetchColumn();
        $project_info[$i]["DVC Hunted"] = $dvc_count;
        $sql = 'SELECT SUM(product_facing_count) FROM products WHERE (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $facing_count = $stmt->fetchColumn();
        if ($facing_count == null) {
            $facing_count = 0;
        }
        $project_info[$i]["Hunted Facing Count"] = $facing_count;
        $sql = 'SELECT COUNT(*) FROM probe WHERE (probe.probe_hunter_processed_time >= :start_datetime AND probe.probe_hunter_processed_time <= :end_datetime)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $checked_count = $stmt->fetchColumn();
        $project_info[$i]["Checked Probe Count"] = $checked_count;

        $sql = 'SELECT COUNT(*) FROM radar_sources WHERE (radar_sources.creation_time >= :start_datetime AND radar_sources.creation_time <= :end_datetime)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $checked_count = $stmt->fetchColumn();
        $project_info[$i]["Added Radar Link Count"] = $checked_count;
    
    $sql = 'SELECT COUNT(DISTINCT DATE(probe_hunter_processed_time)) FROM probe WHERE probe_hunter_processed_time >= :start_datetime AND probe_hunter_processed_time <= :end_datetime';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>$_POST['start_datetime'], 'end_datetime'=>$_POST['end_datetime']]);
    $date_count = $stmt->fetchColumn();
    $project_info[$i]["Days Worked"] = $date_count;

    $sql = 'SELECT COUNT(DISTINCT(probe_ticket_id)) FROM probe WHERE probe_hunter_processed_time >= :start_datetime AND probe_hunter_processed_time <= :end_datetime';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>$_POST['start_datetime'], 'end_datetime'=>$_POST['end_datetime']]);
    $date_count = $stmt->fetchColumn();
    $project_info[$i]["Tickets"] = $date_count;
    
    unset($project_info[$i]["project_db_name"]);
}
*/
$return_arr[] = array("hunter_summary"=>$hunter_summary);
echo json_encode($return_arr);
?>
