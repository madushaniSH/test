<?php
/*
    Filename: assign_qa_product.php
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
$sql = 'SELECT probe_qa_queue_id FROM probe_qa_queue WHERE account_id = :account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

if ($row_count == 1) {
    $sql = 'UPDATE probe_qa_queue SET account_id = NULL, probe_being_handled = 0 WHERE account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id']]);
}
?>