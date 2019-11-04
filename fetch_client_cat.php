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
$dbname = $_POST['project_name'];
// Setting up the DSN
$dsn = 'mysql:host='.$host.';dbname='.$dbname;

/*
    Attempts to connect to the database, if no connection was established
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
$sql = 'SELECT client_category_name FROM client_category WHERE 1';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$client_cat_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
 * Array containing N/A value. This will then be added to the beginning of the client_cat_info array.
 * N/A value is used to represent products which do not have a client category
 * */
$na_client_cat = array("client_category_name"=>'N/A');
array_unshift($client_cat_info, $na_client_cat);

$return_arr[] = array("client_cat_info"=>$client_cat_info);
echo json_encode($return_arr);
