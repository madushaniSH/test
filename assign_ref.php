<?php
/*
    Filename: assign_ref.php
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

$sql = 'SELECT reference_queue_id FROM reference_queue WHERE account_id = :account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$reference_info = $stmt->fetch(PDO::FETCH_OBJ);
$last_id = $reference_info->reference_queue_id;
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

if ($row_count == 0) {
    $this_count = 0;
    $iterations = 0;
    do {
        $sql = 'UPDATE reference_queue SET account_id = :account_id, reference_being_handled = 1 WHERE reference_being_handled = 0 AND account_id IS NULL LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id']]);

        $sql = 'SELECT reference_queue_id FROM reference_queue WHERE account_id = :account_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id']]);
        $reference_info = $stmt->fetch(PDO::FETCH_OBJ);
        $last_id = $reference_info->reference_queue_id;
        $this_count = $stmt->rowCount(PDO::FETCH_OBJ);
        $iterations++;
    } while ($this_count == 0  && $iterations < 10);
}
$sql = 'SELECT a.reference_ean, a.reference_brand FROM reference_queue b INNER JOIN reference_info a ON a.reference_info_id = b.reference_info_key_id WHERE b.reference_info_key_id = :last_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['last_id'=>$last_id]);
$ref_info = $stmt->fetch(PDO::FETCH_OBJ);
$return_arr[] = array("ean" => $ref_info->reference_ean, "brand" => $ref_info->reference_brand);
echo json_encode($return_arr);
?>