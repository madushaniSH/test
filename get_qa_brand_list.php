<?php
/*
    Filename: get_qa_brand_list.php
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

if ($_POST['type'] == 'probe') {
    $sql = 'SELECT SUBSTRING_INDEX(products.product_name, \' \', 1 ) as name FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN probe_product_info ON probe_product_info.probe_product_info_product_id = products.product_id INNER JOIN probe ON probe_product_info.probe_product_info_key_id = probe.probe_key_id   WHERE products.product_type = :product_type AND (probe_qa_queue.probe_being_handled = 0 OR probe_qa_queue.account_id = :account_id) AND probe.probe_ticket_id = :ticket AND products.product_hunt_type = :type';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['product_type'=>$_POST['product_type'], 'account_id' =>$_SESSION['id'], 'ticket'=>$_POST['ticket'], "type"=>$_POST['type']]);
    $brand_rows = $stmt->fetchAll(PDO::FETCH_OBJ);
} else if ($_POST['type'] == 'radar') {
    $sql = 'SELECT SUBSTRING_INDEX(products.product_name, \' \', 1 ) as name FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN radar_sources ON radar_sources.radar_product_id = products.product_id INNER JOIN radar_hunt ON radar_hunt.radar_hunt_id = radar_sources.radar_hunt_id   WHERE products.product_type = :product_type AND (probe_qa_queue.probe_being_handled = 0 OR probe_qa_queue.account_id = :account_id) AND radar_hunt.radar_ticket_id = :ticket AND products.product_hunt_type = :type';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['product_type'=>$_POST['product_type'], 'account_id' =>$_SESSION['id'], 'ticket'=>$_POST['ticket'], "type"=>$_POST['type']]);
    $brand_rows = $stmt->fetchAll(PDO::FETCH_OBJ);
}

$return_arr[] = array("brand_rows"=>$brand_rows);
echo json_encode($return_arr);
?>