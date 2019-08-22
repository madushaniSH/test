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

$return_arr[] = array("number_of_rows" => $number_of_rows, "processing_probe_row" => $row_count, "number_of_handled_rows"=>$number_of_handled_rows);
echo json_encode($return_arr);
?>