<?php
/*
    Author: Malika Liyanage
*/
session_start();
// Current settings to connect to the user account database
require('../../user_db_connection.php');
$dbname = $_POST['project_name'];
// Setting up the DSN
$dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;

/*
    Attempts to connect to the databse, if no connection was estabishled
    kills the script
*/
try {
    // Creating a new PDO instance
    $pdo = new PDO($dsn, $user, $pwd);
    // setting the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // throws error message
    echo "<p>Connection to database failed<br>Reason: " . $e->getMessage() . '</p>';
    exit();
}

$sql = 'SELECT project_error_id, project_error_name FROM project_errors';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$error_rows = $stmt->fetchAll(PDO::FETCH_OBJ);

$return_arr[] = array("error_rows" => $error_rows);
echo json_encode($return_arr);

