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

$sql = 'SELECT project_name, project_db_name FROM projects WHERE project_region = "AMER"';
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
$return_arr[] = array("hunter_summary"=>$project_info);
echo json_encode($return_arr);
?>