<?php
/*
    Filename: upload_probe.php
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

$sql = 'SELECT probe_queue_id FROM probe_queue WHERE account_id = :account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$probe_info = $stmt->fetch(PDO::FETCH_OBJ);
$last_id = $probe_info->probe_queue_id;
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

if ($row_count == 0) {
    $this_count = 0;
    do {
        $sql = 'UPDATE probe_queue SET account_id = :account_id, probe_being_handled = 1 WHERE probe_being_handled = 0 AND account_id IS NULL LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id']]);

        $sql = 'SELECT probe_queue_id FROM probe_queue WHERE account_id = :account_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id']]);
        $probe_info = $stmt->fetch(PDO::FETCH_OBJ);
        $last_id = $probe_info->probe_queue_id;
        $this_count = $stmt->rowCount(PDO::FETCH_OBJ);

    } while ($this_count == 0);
}
$sql = 'SELECT brand.brand_name, client_category.client_category_name, probe.probe_id FROM  probe_queue LEFT JOIN probe ON probe_queue.probe_key_id = probe.probe_key_id LEFT JOIN brand ON probe.brand_id = brand.brand_id LEFT JOIN client_category ON probe.client_category_id  = client_category.client_category_id WHERE probe_queue_id = :probe_queue_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['probe_queue_id'=>$last_id]);
$probe_info = $stmt->fetch(PDO::FETCH_OBJ);
$return_arr[] = array("brand_name" => $probe_info->brand_name, "client_category_name" => $probe_info->client_category_name , "probe_id"=>$probe_info->probe_id);
echo json_encode($return_arr);
?>