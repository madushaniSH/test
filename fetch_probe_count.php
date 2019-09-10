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

if ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor' ) {
    $sql = "SELECT count(*) FROM probe_queue a INNER JOIN probe b ON a.probe_key_id = b.probe_key_id WHERE a.probe_being_handled = 0 AND b.probe_ticket_id =:ticket";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket']]);
    $number_of_rows = $stmt->fetchColumn();


    $sql = "SELECT count(*) FROM probe_queue a INNER JOIN probe b ON a.probe_key_id = b.probe_key_id WHERE a.probe_being_handled = 1 AND b.probe_ticket_id =:ticket";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket']]);
    $number_of_handled_rows = $stmt->fetchColumn();

    $sql = 'SELECT probe_queue_id, probe_key_id FROM probe_queue WHERE account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id']]);
    $probe_info = $stmt->fetch(PDO::FETCH_OBJ);
    $row_count = $stmt->rowCount(PDO::FETCH_OBJ);

    $number_of_products_added = 0;
    if ($row_count == 1) {
        $sql = "SELECT COUNT(*) FROM probe_product_info WHERE probe_product_info_key_id = :probe_key";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['probe_key'=>$probe_info->probe_key_id]);
        $number_of_products_added = $stmt->fetchColumn();
    }
 
    $sql = 'SELECT account_latest_login_date_time FROM user_db.accounts WHERE account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id']]);
    $login_info = $stmt->fetch(PDO::FETCH_OBJ);

    $min_time = date("Y-m-d H:i:s", strtotime($login_info->account_latest_login_date_time . " -10 hours"));
    $maxtime = date("Y-m-d H:i:s", strtotime($login_info->account_latest_login_date_time . " +10 hours"));

    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="brand" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
    $brand_count = $stmt->fetchColumn();

    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="sku" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
    $sku_count = $stmt->fetchColumn();

    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="dvc" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
    $dvc_count = $stmt->fetchColumn();

    $sql = 'SELECT COUNT(*) FROM probe WHERE probe.probe_hunter_processed_time >= :start_datetime AND probe.probe_hunter_processed_time <= :end_datetime';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
    $checked_count = $stmt->fetchColumn();

    $sql = 'SELECT COUNT(*) FROM products WHERE product_qa_status = "disapproved" AND products.product_qa_account_id IS NOT NULL AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
    $error_count = $stmt->fetchColumn();
    
    $sql = 'SELECT COUNT(*) FROM products WHERE product_qa_status = "disapproved" AND products.product_qa_account_id IS NULL AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
    $system_error_count = $stmt->fetchColumn();

    $sql = 'SELECT SUM(product_facing_count) FROM products WHERE products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
    $facing_count = $stmt->fetchColumn();
    if ($facing_count == null) {
        $facing_count = 0;
    }
} else {
    $sql = "SELECT count(*) FROM probe_queue a INNER JOIN probe b ON a.probe_key_id = b.probe_key_id WHERE a.probe_being_handled = 0 AND b.probe_ticket_id =:ticket";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket']]);
    $number_of_rows = $stmt->fetchColumn();


    $sql = "SELECT count(*) FROM probe_queue a INNER JOIN probe b ON a.probe_key_id = b.probe_key_id WHERE a.probe_being_handled = 1 AND b.probe_ticket_id =:ticket";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket']]);
    $number_of_handled_rows = $stmt->fetchColumn();

    $sql = 'SELECT probe_queue_id, probe_key_id FROM probe_queue WHERE account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id']]);
    $probe_info = $stmt->fetch(PDO::FETCH_OBJ);
    $row_count = $stmt->rowCount(PDO::FETCH_OBJ);

    $number_of_products_added = 0;
    if ($row_count == 1) {
        $sql = "SELECT COUNT(*) FROM probe_product_info WHERE probe_product_info_key_id = :probe_key";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['probe_key'=>$probe_info->probe_key_id]);
        $number_of_products_added = $stmt->fetchColumn();
    }
 
    $sql = 'SELECT account_latest_login_date_time FROM user_db.accounts WHERE account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id']]);
    $login_info = $stmt->fetch(PDO::FETCH_OBJ);

    $min_time = date("Y-m-d H:i:s", strtotime($login_info->account_latest_login_date_time . " -10 hours"));
    $maxtime = date("Y-m-d H:i:s", strtotime($login_info->account_latest_login_date_time . " +10 hours"));

    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="brand" AND account_id = :account_id AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
    $brand_count = $stmt->fetchColumn();

    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="sku" AND account_id = :account_id AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
    $sku_count = $stmt->fetchColumn();

    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="dvc" AND account_id = :account_id AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
    $dvc_count = $stmt->fetchColumn();

    $sql = 'SELECT COUNT(*) FROM probe WHERE probe.probe_processed_hunter_id = :account_id AND (probe.probe_hunter_processed_time >= :start_datetime AND probe.probe_hunter_processed_time <= :end_datetime)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
    $checked_count = $stmt->fetchColumn();

    $sql = 'SELECT COUNT(*) FROM products WHERE products.account_id = :account_id AND product_qa_status = "disapproved" AND products.product_qa_account_id IS NOT NULL AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
    $error_count = $stmt->fetchColumn();
    
    $sql = 'SELECT COUNT(*) FROM products WHERE products.account_id = :account_id AND product_qa_status = "disapproved" AND products.product_qa_account_id IS NULL AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
    $system_error_count = $stmt->fetchColumn();

    $sql = 'SELECT SUM(product_facing_count) FROM products WHERE account_id = :account_id AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
    $facing_count = $stmt->fetchColumn();
    if ($facing_count == null) {
        $facing_count = 0;
    }
}


$return_arr[] = array("number_of_rows" => $number_of_rows, "processing_probe_row" => $row_count, "number_of_handled_rows"=>$number_of_handled_rows, "brand_count"=>$brand_count, "sku_count"=>$sku_count, "dvc_count"=>$dvc_count, "checked_count"=>$checked_count, "error_count"=>$error_count, "system_error_count"=>$system_error_count, "facing_count"=>$facing_count, "number_of_products_added"=>$number_of_products_added);
echo json_encode($return_arr);
?>