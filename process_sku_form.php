<?php
/*
    Filename: new_sku_form.php
    Author: Malika Liyanage
    Created: 23/07/2019
    Purpose: Used for entering a new sku to the system
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

$sql = 'SELECT product_name FROM product WHERE product_name = :product_name';
$stmt = $pdo->prepare($sql);
$stmt->execute(['product_name'=>$_POST['name']]);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);
?>
