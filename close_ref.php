<?php
/*
    Filename: close_ref.php
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
$comment = trim($_POST['comment']);
if ($comment === '') {
    $comment = NULL;
}

$remark = trim($_POST['remark']);
if ($remark === '') {
    $remark = NULL;
}

$sql = 'SELECT reference_queue_id,reference_info_key_id, assign_datetime FROM reference_queue WHERE account_id = :account_id AND reference_being_handled = 1';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$ref_info = $stmt->fetch(PDO::FETCH_OBJ);

$now = new DateTime();
$sql = 'UPDATE reference_info SET reference_processed_hunter_id = :ref_hunter_id, reference_hunter_processed_time = :reference_hunter_processed_time , reference_process_comment = :comment, reference_process_remark = :remark, reference_status_id = :status_id, ref_start_datetime = :datetime WHERE reference_info_id = :id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['ref_hunter_id'=>$_SESSION['id'], 'reference_hunter_processed_time'=>$now->format('Y-m-d H:i:s'), 'id'=>$ref_info->reference_info_key_id, 'comment'=>$comment, 'remark'=>$remark, 'status_id'=>$_POST['status'], 'datetime' => $ref_info->assign_datetime]);

$sql = 'DELETE FROM reference_queue WHERE reference_queue_id = :id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['id'=>$ref_info->reference_queue_id]);
?>