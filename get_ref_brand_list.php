<?php
/*
    Filename: get_ref_brand_list.php
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

$sql = 'SELECT DISTINCT(a.reference_brand) AS name FROM reference_queue b INNER JOIN reference_info a ON a.reference_info_id = b.reference_info_key_id WHERE (b.reference_being_handled = 0 OR b.account_id = :account_id) AND a.reference_brand IS NOT NULL AND a.reference_ticket_id = :ticket';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id' =>$_SESSION['id'], 'ticket'=>$_POST['ticket']]);
$brand_rows = $stmt->fetchAll(PDO::FETCH_OBJ);

$return_arr[] = array("brand_rows"=>$brand_rows);
echo json_encode($return_arr);
?>