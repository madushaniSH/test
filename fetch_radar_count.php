<?php
/*
    Filename: fetch_radar_count.php
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

$sql = "SELECT count(*) FROM radar_queue b INNER JOIN radar_hunt a ON a.radar_hunt_id = b.radar_hunt_key_id WHERE radar_being_handled = 0 AND a.radar_ticket_id = :ticket"; 
$stmt = $pdo->prepare($sql);
$stmt->execute(['ticket'=>$_POST['ticket']]); 
$number_of_rows = $stmt->fetchColumn(); 

$sql = "SELECT count(*) FROM radar_queue b INNER JOIN radar_hunt a ON a.radar_hunt_id = b.radar_hunt_key_id WHERE radar_being_handled = 1 AND a.radar_ticket_id = :ticket"; 
$stmt = $pdo->prepare($sql);
$stmt->execute(['ticket'=>$_POST['ticket']]); 
$number_of_handled_rows = $stmt->fetchColumn(); 

$sql = 'SELECT radar_queue_id FROM radar_queue WHERE account_id = :account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$radar_queue_info = $stmt->fetch(PDO::FETCH_OBJ);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

$brand_name = '';

if ($row_count == 1) {
    $sql = 'SELECT DISTINCT(a.radar_category) AS name FROM radar_queue b INNER JOIN radar_hunt a ON a.radar_hunt_id = b.radar_hunt_key_id WHERE (b.radar_being_handled = 0 OR b.account_id = :account_id) AND a.radar_category IS NOT NULL AND b.radar_queue_id = :radar_queue_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['radar_queue_id' =>$radar_queue_info->radar_queue_id,'account_id'=>$_SESSION['id']]);
    $radar_info = $stmt->fetch(PDO::FETCH_OBJ);
    $radar_cat = $radar_info->name;
}

$search_item = $_POST['radar_cat'].'';
$sql = "SELECT count(*) FROM radar_queue b INNER JOIN radar_hunt a ON a.radar_hunt_id = b.radar_hunt_key_id  WHERE (b.radar_being_handled = 0 OR b.account_id = :account_id) AND a.radar_category = :search_item AND a.radar_ticket_id = :ticket";
$stmt = $pdo->prepare($sql);
$stmt->execute(['search_item'=>$search_item, 'account_id'=>$_SESSION['id'], "ticket"=>$_POST['ticket']]);
$radar_cat_count = $stmt->fetchColumn();

$return_arr[] = array("number_of_rows" => $number_of_rows, "processing_probe_row" => $row_count, "number_of_handled_rows"=>$number_of_handled_rows, "radar_cat_count"=>$radar_cat_count, "radar_cat"=>$radar_cat);
echo json_encode($return_arr);
?>