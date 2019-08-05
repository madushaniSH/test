<?php
/*
    Filename: container_type_list.php
    Author: Malika Liyanage
*/

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
	header('Location: login_auth_one.php');
	exit();
}

// Current settings to connect to the user account database
require('product_connection.php');

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

$sql = 'SELECT product_container_type_id, product_container_type_name FROM product_container_type';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$container_type_rows = $stmt->fetchAll(PDO::FETCH_OBJ);


echo "<option value=\"\" selected disabled>Select</option>";
foreach($container_type_rows as $container_type_row){
    echo "<option value=\"$container_type_row->product_container_type_id\">$container_type_row->product_container_type_name</option>";
}