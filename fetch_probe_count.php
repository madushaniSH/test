<?php
/*
    Filename: fetcg_probe_count.php
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
$dbname = $_POST['project_name'];
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

$sql = "SELECT count(*) FROM probe_queue WHERE probe_being_handled = 0"; 
$stmt = $pdo->prepare($sql);
$stmt->execute(); 
$number_of_rows = $stmt->fetchColumn(); 


$sql = "SELECT count(*) FROM probe_queue WHERE probe_being_handled = 1"; 
$stmt = $pdo->prepare($sql);
$stmt->execute(); 
$number_of_handled_rows = $stmt->fetchColumn(); 



$sql = 'SELECT probe_queue_id FROM probe_queue WHERE account_id = :account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

$sql = 'SELECT account_latest_login_date_time FROM user_db.accounts WHERE account_id = :account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$login_info = $stmt->fetch(PDO::FETCH_OBJ);

$min_time = date("Y-m-d H:i:s", strtotime($login_info->account_latest_login_date_time . " -10 hours"));
$maxtime = date("Y-m-d H:i:s", strtotime($login_info->account_latest_login_date_time . " +10 hours"));

$sql = 'SELECT COUNT(*) FROM products WHERE product_type ="brand" AND account_id = :account_id AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
$brand_count = $stmt->fetchColumn();

$sql = 'SELECT COUNT(*) FROM products WHERE product_type ="sku" AND account_id = :account_id AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
$sku_count = $stmt->fetchColumn();

$sql = 'SELECT COUNT(*) FROM products WHERE product_type ="dvc" AND account_id = :account_id AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
$dvc_count = $stmt->fetchColumn();

$sql = 'SELECT COUNT(*) FROM probe WHERE probe.probe_processed_hunter_id = :account_id AND (probe.probe_hunter_processed_time >= :start_datetime AND probe.probe_hunter_processed_time <= :end_datetime)';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
$checked_count = $stmt->fetchColumn();

$sql = 'SELECT COUNT(*) FROM products WHERE products.account_id = :account_id AND product_qa_status = "disapproved" AND products.product_qa_account_id IS NOT NULL AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
$error_count = $stmt->fetchColumn();
    
$sql = 'SELECT COUNT(*) FROM products WHERE products.account_id = :account_id AND product_qa_status = "disapproved" AND products.product_qa_account_id IS NULL AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
$system_error_count = $stmt->fetchColumn();

$return_arr[] = array("number_of_rows" => $number_of_rows, "processing_probe_row" => $row_count, "number_of_handled_rows"=>$number_of_handled_rows, "brand_count"=>$brand_count, "sku_count"=>$sku_count, "dvc_count"=>$dvc_count, "checked_count"=>$checked_count, "error_count"=>$error_count, "system_error_count"=>$system_error_count);
echo json_encode($return_arr);
?>