<?php
/*
    Filename: assign_probe.php
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
$_SESSION['current_database'] = $dbname; 
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

$sql = "SELECT products.product_id, products.product_name FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id WHERE probe_qa_queue.account_id = :account_id AND probe_qa_queue.probe_being_handled = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$product_info = $stmt->fetch(PDO::FETCH_OBJ);
$row_count = $stmt->rowCount();

if ($row_count == 1 && $_POST['product_type'] == 'brand') {
    if (trim($product_info->product_name) != trim($_POST['product_rename'])) {
        $sql = "UPDATE products SET product_name = :product_name, product_previous = :product_previous WHERE product_id = :product_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['product_name'=>trim($_POST['product_rename']), 'product_previous'=>trim($product_info->product_name), 'product_id'=>$product_info->product_id]);
    }
}
$now = new DateTime();
$sql = "UPDATE products SET product_qa_account_id = :account_id, product_qa_datetime = :date_time, product_qa_status = :qa_status WHERE product_id = :product_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id'], 'date_time'=>$now->format('Y-m-d H:i:s'), 'qa_status'=>$_POST['status'], 'product_id'=>$product_info->product_id]);

?>