<?php
/*
    Filename: fetch_probe_qa_count.php
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

$sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id WHERE products.product_type = 'brand' AND products.product_qa_status = 'pending'";
$stmt = $pdo->prepare($sql);
$stmt->execute(); 
$brand_count = $stmt->fetchColumn(); 

$sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id WHERE products.product_type = 'sku' AND products.product_qa_status = 'pending'"; 
$stmt = $pdo->prepare($sql);
$stmt->execute(); 
$sku_count = $stmt->fetchColumn(); 

$sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id WHERE products.product_type = 'dvc' AND products.product_qa_status = 'pending'"; 
$stmt = $pdo->prepare($sql);
$stmt->execute(); 
$dvc_count = $stmt->fetchColumn(); 

$sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id WHERE products.product_type = 'facing' AND products.product_qa_status = 'pending'"; 
$stmt = $pdo->prepare($sql);
$stmt->execute(); 
$facing_count = $stmt->fetchColumn(); 

$sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id WHERE products.product_type = 'brand' AND (probe_qa_queue.probe_being_handled = 0 OR probe_qa_queue.account_id = :account_id)";
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$brand_user_count = $stmt->fetchColumn();

$search_item = $_POST['sku_brand_name'].' %';
$sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id WHERE products.product_type = 'sku' AND (probe_qa_queue.probe_being_handled = 0 OR probe_qa_queue.account_id = :account_id) AND products.product_name LIKE :search_item";
$stmt = $pdo->prepare($sql);
$stmt->execute(['search_item'=>$search_item, 'account_id'=>$_SESSION['id']]);
$brand_sku_count = $stmt->fetchColumn();

$search_item = $_POST['sku_dvc_name'].' %';
$sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id WHERE products.product_type = 'dvc' AND (probe_qa_queue.probe_being_handled = 0 OR probe_qa_queue.account_id = :account_id)AND products.product_name LIKE :search_item";
$stmt = $pdo->prepare($sql);
$stmt->execute(['search_item'=>$search_item, 'account_id'=>$_SESSION['id']]);
$dvc_sku_count = $stmt->fetchColumn();

$search_item = $_POST['sku_facing_name'].' %';
$sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id WHERE products.product_type = 'facing' AND (probe_qa_queue.probe_being_handled = 0 OR probe_qa_queue.account_id = :account_id)AND products.product_name LIKE :search_item";
$stmt = $pdo->prepare($sql);
$stmt->execute(['search_item'=>$search_item, 'account_id'=>$_SESSION['id']]);
$facing_sku_count = $stmt->fetchColumn();


$sql = 'SELECT probe_qa_queue.probe_qa_queue_id, products.product_type FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id WHERE probe_qa_queue.account_id = :account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$probe_info = $stmt->fetch(PDO::FETCH_OBJ);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

$return_arr[] = array("brand_count" => $brand_count, "sku_count" => $sku_count, "dvc_count" => $dvc_count,"processing_probe_row" => $row_count, "product_type" => $probe_info->product_type, "brand_sku_count"=>$brand_sku_count, "brand_dvc_count"=>$dvc_sku_count, "brand_user_count"=>$brand_user_count, "facing_count"=>$facing_count, "facing_sku_count"=>$facing_sku_count);
echo json_encode($return_arr);
?>