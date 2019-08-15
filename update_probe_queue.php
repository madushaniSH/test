<?php
/*
    Filename: fetcg_probe_count.php
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

$success = '';
$error = '';
$sql = 'SELECT probe_key_id FROM probe_queue WHERE account_id = :account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$probe_info = $stmt->fetch(PDO::FETCH_OBJ);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

if ($row_count == 1) {
    if (isset($_POST['comment']) && $_POST['comment'] != '') {
        $comment = $_POST['comment'];
    } else {
        $comment = NULL;
    }

    if (isset($_POST['remark']) && $_POST['remark'] != '') {
        $remark = $_POST['remark'];
    } else {
        $remark = NULL;
    }
    try {
        $now = new DateTime();

        $sql = 'UPDATE probe SET probe_hunter_processed_time = :probe_hunter_proccessed_time, probe_process_comment = :probe_proccess_comment, probe_processed_hunter_id = :probe_processed_hunter_id, probe_process_remark = :probe_process_remark, probe_status_id = :probe_status_id WHERE probe_key_id = :probe_key_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['probe_hunter_proccessed_time'=> $now->format('Y-m-d H:i:s'), 'probe_proccess_comment'=>$comment, 'probe_processed_hunter_id'=>$_SESSION['id'], 'probe_process_remark'=>$remark, 'probe_status_id'=>$_POST['status'], 'probe_key_id'=>$probe_info->probe_key_id]);

        $sql = 'DELETE FROM probe_queue WHERE probe_key_id = :probe_key_id AND account_id = :account_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['probe_key_id'=>$probe_info->probe_key_id, 'account_id'=>$_SESSION['id']]);
        $success = 'Success';
    }
    catch(PDOException $e) {
        $error =$e->getMessage();
    }
}

$return_arr[] = array("error"=>$error, "success"=>$success);
echo json_encode($return_arr);

?>