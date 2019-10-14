
<?php
/*
    Filename: process_source.php
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
$_SESSION['current_database'] = $dbname; 
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

$sql = 'SELECT radar_hunt_key_id FROM radar_queue WHERE account_id = :account_id AND radar_being_handled = 1';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$radar_info = $stmt->fetch(PDO::FETCH_OBJ);

if ($_POST['comment'] === '') {
    $comment = NULL;
} else {
    $comment = $_POST['comment'];
}

$sql = 'INSERT INTO radar_sources (radar_hunt_id, radar_status_id, radar_source_link, radar_comment, account_id) VALUES (:radar_hunt_id, :radar_status_id, :radar_source_link, :radar_comment, :account_id)';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id'], 'radar_hunt_id'=>$radar_info->radar_hunt_key_id, "radar_status_id"=>$_POST['status'], "radar_source_link"=>$_POST['source'], "radar_comment"=>$comment]);

?>