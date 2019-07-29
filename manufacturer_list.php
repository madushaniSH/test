<?php
/*
    Filename: manufacture_list.php
    Author: Malika Liyanage
    Created: 29/07/2019
    Purpose: Updating the manufacturer drop down in "new_brand" form
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

$sql = 'SELECT manufacturer_id, manufacturer_name FROM manufacturer';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$manufacturer_rows = $stmt->fetchAll(PDO::FETCH_OBJ);

echo "<option value=\"\" selected disabled>Select</option>";
foreach($manufacturer_rows as $manufacturer_row){
    echo "<option value=\"$manufacturer_row->manufacturer_id\">$manufacturer_row->manufacturer_name</option>";
}
?>