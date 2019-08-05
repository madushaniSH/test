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

$sql = 'SELECT product_measurement_unit_id, product_measurement_unit_name FROM product_measurement_unit';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$measurement_unit_rows = $stmt->fetchAll(PDO::FETCH_OBJ);

echo "<option value=\"\" selected disabled>Select</option>";
foreach($measurement_unit_rows as $measurement_unit_row){
    echo "<option value=\"$measurement_unit_row->product_measurement_unit_id\">$measurement_unit_row->product_measurement_unit_name</option>";
}