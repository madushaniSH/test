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

$sql = 'SELECT SUBSTRING_INDEX(products.product_name, \' \', 1 ) as name FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id WHERE products.product_type = :product_type AND (probe_qa_queue.probe_being_handled = 0 OR probe_qa_queue.account_id = :account_id) ';
$stmt = $pdo->prepare($sql);
$stmt->execute(['product_type'=>$_POST['product_type'], 'account_id' =>$_SESSION['id']]);
$brand_rows = $stmt->fetchAll(PDO::FETCH_OBJ);

$return_arr[] = array("brand_rows"=>$brand_rows);
echo json_encode($return_arr);
?>