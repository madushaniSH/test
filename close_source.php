
<?php
/*
    Filename: close_source.php
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

$sql = 'SELECT radar_queue_id,radar_hunt_key_id FROM radar_queue WHERE account_id = :account_id AND radar_being_handled = 1';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$radar_info = $stmt->fetch(PDO::FETCH_OBJ);

$now = new DateTime();
$sql = 'UPDATE radar_hunt SET radar_hunter_id = :radar_hunter_id, radar_processed_time = :radar_processed_time WHERE radar_hunt_id = :id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['radar_hunter_id'=>$_SESSION['id'], 'radar_processed_time'=>$now->format('Y-m-d H:i:s'), 'id'=>$radar_info->radar_hunt_key_id]);

$sql = 'DELETE FROM radar_queue WHERE radar_queue_id = :id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['id'=>$radar_info->radar_queue_id]);
?>