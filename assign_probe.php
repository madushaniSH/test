<?php
/*
    Filename: upload_probe.php
    Author: Malika Liyanage
*/
echo "<p>:D</p>";
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
$dbname = 'GMI_US';
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

$sql = 'SELECT probe_queue_id FROM probe_queue WHERE account_id = :account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

if ($row_count == 0) {
    $sql = 'SELECT probe_queue_id FROM probe_queue WHERE probe_being_handled = 0 FOR UPDATE';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $probe_info = $stmt->fetch(PDO::FETCH_OBJ);
    $last_id = $probe_info->probe_queue_id;

    $sql = 'UPDATE probe_queue SET account_id = :account_id, probe_being_handled = :probe_being_handled WHERE probe_queue_id = :probe_queue_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id'],'probe_queue_id'=>$last_id, 'probe_being_handled'=>1]);
}
?>