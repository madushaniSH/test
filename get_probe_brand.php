<?php
/*
    Filename: get_probe_brand.php
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
if ($_POST['client_cat'] == 0) {
    $sql = 'SELECT DISTINCT(brand.brand_name) AS name, brand.brand_id AS id FROM probe INNER JOIN brand ON probe.brand_id = brand.brand_id WHERE probe.probe_ticket_id = :ticket AND probe.client_category_id IS NULL AND probe.probe_processed_hunter_id IS NULL';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket']]);
    $brand_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $sql = 'SELECT DISTINCT(brand.brand_name) AS name, brand.brand_id AS id FROM probe INNER JOIN brand ON probe.brand_id = brand.brand_id WHERE probe.probe_ticket_id = :ticket AND probe.client_category_id = :id AND probe.probe_processed_hunter_id IS NULL';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket'] ,'id'=>$_POST['client_cat']]);
    $brand_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$length = sizeof($brand_rows);
$temp = $brand_rows[0]["name"];
$temp_id = $brand_rows[0]["id"];
$brand_rows[0]["name"] = 'N / A';
$brand_rows[0]["id"] = 0;
$brand_rows[$length]["name"] = $temp;
$brand_rows[$length]["id"] = $temp_id;


$return_arr[] = array("brand_rows"=>$brand_rows);
echo json_encode($return_arr);
?>