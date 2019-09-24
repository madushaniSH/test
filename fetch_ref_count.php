<?php
/*
    Filename: fetch_ref_count.php
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

$sql = "SELECT count(*) FROM reference_queue a INNER JOIN reference_info b ON a.reference_info_key_id = b.reference_info_id WHERE reference_being_handled = 0 AND b.reference_ticket_id = :ticket"; 
$stmt = $pdo->prepare($sql);
$stmt->execute(['ticket'=>$_POST['ticket']]);
$number_of_rows = $stmt->fetchColumn(); 

$sql = "SELECT count(*) FROM reference_queue a INNER JOIN reference_info b ON a.reference_info_key_id = b.reference_info_id WHERE reference_being_handled = 1 AND b.reference_ticket_id = :ticket"; 
$stmt = $pdo->prepare($sql);
$stmt->execute(['ticket'=>$_POST['ticket']]); 
$number_of_handled_rows = $stmt->fetchColumn(); 

$sql = 'SELECT reference_queue_id FROM reference_queue WHERE account_id = :account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$ref_queue_info = $stmt->fetch(PDO::FETCH_OBJ);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

$brand_name = '';

if ($row_count == 1) {
    $sql = 'SELECT a.reference_brand FROM reference_queue b INNER JOIN reference_info a ON a.reference_info_id = b.reference_info_key_id WHERE b.reference_queue_id = :reference_queue_id AND b.account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['reference_queue_id' =>$ref_queue_info->reference_queue_id,'account_id'=>$_SESSION['id']]);
    $brand_info = $stmt->fetch(PDO::FETCH_OBJ);
    $brand_name = $brand_info->reference_brand;
}

$search_item = $_POST['sku_brand_name'].'';
$sql = "SELECT count(*) FROM reference_queue b INNER JOIN reference_info a ON a.reference_info_id = b.reference_info_key_id WHERE (b.reference_being_handled = 0 OR b.account_id = :account_id) AND a.reference_brand = :search_item";
$stmt = $pdo->prepare($sql);
$stmt->execute(['search_item'=>$search_item, 'account_id'=>$_SESSION['id']]);
$ref_brand_count = $stmt->fetchColumn();

$return_arr[] = array("number_of_rows" => $number_of_rows, "processing_probe_row" => $row_count, "number_of_handled_rows"=>$number_of_handled_rows, "ref_brand_count"=>$ref_brand_count, "brand_name"=>$brand_name);
echo json_encode($return_arr);
?>