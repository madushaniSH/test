<?php
/*
    Filename: add_new_error.php
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

$error = '';
$sql = 'SELECT project_error_name FROM project_errors WHERE project_error_name = :project_error_name';
$stmt = $pdo->prepare($sql);
$stmt->execute(['project_error_name'=>trim($_POST['error_new_name'])]);
$row_count = $stmt->rowCount();

if ($row_count == 0) {
    $sql = 'INSERT INTO project_errors (project_error_name) VALUES (:project_error_name)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['project_error_name'=>trim($_POST['error_new_name'])]);
} else {
    $error = 'Error type already exists';
}

$return_arr[] = array("error"=>$error, "added_name"=>trim($_POST['error_new_name']));
echo json_encode($return_arr);
?>