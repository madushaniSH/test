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

$weight = 0;
$sql = 'SELECT project_language FROM `project_db`.projects WHERE project_name = :project_name';
$stmt = $pdo->prepare($sql);
$stmt->execute(["project_name"=>$dbname]);
$project_lang = $stmt->fetchColumn();
$weight = 1;
if ($project_lang == "english") {
    $weight = 1;
} else if ($project_lang == "non_english") {
    $weight = 2;
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

    if ($_POST['client_cat'] == 0 && $_POST['brand'] == 0) {
        $sql = "SELECT count(*) FROM probe_queue a INNER JOIN probe b ON a.probe_key_id = b.probe_key_id WHERE (a.probe_being_handled = 0 OR a.probe_being_handled = 1 AND  a.account_id = :account_id) AND b.probe_ticket_id =:ticket AND b.client_category_id IS NULL AND b.brand_id IS NULL";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'],'ticket'=>$_POST['ticket']]);
        $probe_brand_count = $stmt->fetchColumn();
    } else if ($_POST['client_cat'] == 0 && $_POST['brand'] != 0 ) {
        $sql = "SELECT count(*) FROM probe_queue a INNER JOIN probe b ON a.probe_key_id = b.probe_key_id WHERE (a.probe_being_handled = 0 OR a.probe_being_handled = 1 AND  a.account_id = :account_id) AND b.probe_ticket_id =:ticket AND b.client_category_id IS NULL AND b.brand_id = :brand_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'],'ticket'=>$_POST['ticket'], 'brand_id'=>(int)$_POST['brand']]);
        $probe_brand_count = $stmt->fetchColumn();
    } else if ($_POST['brand'] == 0 && $_POST['client_cat'] != 0) {
        $sql = "SELECT count(*) FROM probe_queue a INNER JOIN probe b ON a.probe_key_id = b.probe_key_id WHERE (a.probe_being_handled = 0 OR a.probe_being_handled = 1 AND  a.account_id = :account_id) AND b.probe_ticket_id =:ticket AND b.client_category_id = :client_id AND b.brand_id IS NULL";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'],'ticket'=>$_POST['ticket'], 'client_id'=>(int)$_POST['client_cat']]);
        $probe_brand_count = $stmt->fetchColumn();
    } else {
        $sql = "SELECT count(*) FROM probe_queue a INNER JOIN probe b ON a.probe_key_id = b.probe_key_id WHERE (a.probe_being_handled = 0 OR a.probe_being_handled = 1 AND  a.account_id = :account_id) AND b.probe_ticket_id =:ticket AND b.client_category_id = :client_id AND b.brand_id = :brand_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'],'ticket'=>$_POST['ticket'], 'client_id'=>(int)$_POST['client_cat'], 'brand_id'=>(int)$_POST['brand']]);
        $probe_brand_count = $stmt->fetchColumn();
    }

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

    $sql = "SELECT COUNT(*) FROM products WHERE ( product_type = 'brand' OR product_type = 'sku' ) AND products.product_previous IS NOT NULL AND products.product_qa_datetime >= :start_datetime AND products.product_qa_datetime <= :end_datetime AND products.product_status = 2";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
    $rename_error_count = $stmt->fetchColumn();

    $sql = "SELECT COUNT(products.product_id) FROM products INNER JOIN product_qa_errors ON products.product_id = product_qa_errors.product_id WHERE products.product_qa_datetime >= :start_datetime AND products.product_qa_datetime <= :end_datetime AND products.product_status = 2";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
    $error_type_count = $stmt->fetchColumn();
} else {
    $sql = "SELECT count(*) FROM probe_queue a INNER JOIN probe b ON a.probe_key_id = b.probe_key_id WHERE a.probe_being_handled = 0 AND b.probe_ticket_id =:ticket";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket']]);
    $number_of_rows = $stmt->fetchColumn();


    $sql = "SELECT count(*) FROM probe_queue a INNER JOIN probe b ON a.probe_key_id = b.probe_key_id WHERE a.probe_being_handled = 1 AND b.probe_ticket_id =:ticket";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket']]);
    $number_of_handled_rows = $stmt->fetchColumn();

    if ($_POST['client_cat'] == 0 && $_POST['brand'] == 0) {
        $sql = "SELECT count(*) FROM probe_queue a INNER JOIN probe b ON a.probe_key_id = b.probe_key_id WHERE (a.probe_being_handled = 0 OR a.probe_being_handled = 1 AND  a.account_id = :account_id) AND b.probe_ticket_id =:ticket AND b.client_category_id IS NULL AND b.brand_id IS NULL";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'],'ticket'=>$_POST['ticket']]);
        $probe_brand_count = $stmt->fetchColumn();
    } else if ($_POST['client_cat'] == 0 && $_POST['brand'] != 0 ) {
        $sql = "SELECT count(*) FROM probe_queue a INNER JOIN probe b ON a.probe_key_id = b.probe_key_id WHERE (a.probe_being_handled = 0 OR a.probe_being_handled = 1 AND  a.account_id = :account_id) AND b.probe_ticket_id =:ticket AND b.client_category_id IS NULL AND b.brand_id = :brand_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'],'ticket'=>$_POST['ticket'], 'brand_id'=>(int)$_POST['brand']]);
        $probe_brand_count = $stmt->fetchColumn();
    } else if ($_POST['brand'] == 0 && $_POST['client_cat'] != 0) {
        $sql = "SELECT count(*) FROM probe_queue a INNER JOIN probe b ON a.probe_key_id = b.probe_key_id WHERE (a.probe_being_handled = 0 OR a.probe_being_handled = 1 AND  a.account_id = :account_id) AND b.probe_ticket_id =:ticket AND b.client_category_id = :client_id AND b.brand_id IS NULL";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'],'ticket'=>$_POST['ticket'], 'client_id'=>(int)$_POST['client_cat']]);
        $probe_brand_count = $stmt->fetchColumn();
    } else {
        $sql = "SELECT count(*) FROM probe_queue a INNER JOIN probe b ON a.probe_key_id = b.probe_key_id WHERE (a.probe_being_handled = 0 OR a.probe_being_handled = 1 AND  a.account_id = :account_id) AND b.probe_ticket_id =:ticket AND b.client_category_id = :client_id AND b.brand_id = :brand_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'],'ticket'=>$_POST['ticket'], 'client_id'=>(int)$_POST['client_cat'], 'brand_id'=>(int)$_POST['brand']]);
        $probe_brand_count = $stmt->fetchColumn();
    }

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
    $sql = "SELECT COUNT(*) FROM products WHERE ( product_type = 'brand' OR product_type = 'sku' ) AND account_id = :account_id AND products.product_previous IS NOT NULL AND products.product_qa_datetime >= :start_datetime AND products.product_qa_datetime <= :end_datetime AND products.product_status = 2";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
    $rename_error_count = $stmt->fetchColumn();

    $sql = "SELECT COUNT(products.product_id) FROM products INNER JOIN product_qa_errors ON products.product_id = product_qa_errors.product_id WHERE products.account_id = :account_id AND products.product_qa_datetime >= :start_datetime AND products.product_qa_datetime <= :end_datetime AND products.product_status = 2";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
    $error_type_count = $stmt->fetchColumn();
}

if ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor' ) {
    $cycle_start = $_POST['start_time'];
    $cycle_end = $_POST['end_time'];
    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="brand" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>$cycle_start, 'end_datetime'=>$cycle_end]);
    $mon_brand_count = $stmt->fetchColumn();
    if ($mon_brand_count == null) {
        $mon_brand_count = 0;
    }

    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="sku" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>$cycle_start, 'end_datetime'=>$cycle_end]);
    $mon_sku_count = $stmt->fetchColumn();
    if ($mon_sku_count == null) {
        $mon_sku_count = 0;
    }

    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="dvc" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>$cycle_start, 'end_datetime'=>$cycle_end]);
    $mon_dvc_count = $stmt->fetchColumn();
    if ($mon_facing_count == null) {
        $mon_facing_count = 0;
    }

    $sql = 'SELECT SUM(product_facing_count) FROM products WHERE products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>$cycle_start, 'end_datetime'=>$cycle_end]);
    $mon_facing_count = $stmt->fetchColumn();
    if ($mon_facing_count == null) {
        $mon_facing_count = 0;
    }
   
    if ($mon_sku_count == 0 && $mon_brand_count == 0 && $mon_dvc_count == 0 && $mon_facing_count == 0) {
        $mon_accuracy = 0;
    } else {
        $sql = "SELECT COUNT(products.product_id) FROM products INNER JOIN product_qa_errors ON products.product_id = product_qa_errors.product_id WHERE products.product_qa_datetime >= :start_datetime AND products.product_qa_datetime <= :end_datetime AND products.product_status = 2";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['start_datetime'=>$cycle_start, 'end_datetime'=>$cycle_end]);
        $mon_error_type_count = $stmt->fetchColumn();

        $sql = 'SELECT COUNT(*) FROM products WHERE product_qa_status = "disapproved" AND products.product_qa_account_id IS NULL AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['start_datetime'=>$cycle_start, 'end_datetime'=>$cycle_end]);
        $mon_system_errors = $stmt->fetchColumn();

        $total_count = ($mon_brand_count * 1.5) + ($mon_sku_count) + (($mon_facing_count  + $mon_dvc_count) / 2) * $weight;
        $mon_accuracy = round(((($total_count - ($mon_error_type_count + $mon_system_errors * 1)) / $total_count) * 100), 2);
    }
} else {
    $cycle_start = $_POST['start_time'];
    $cycle_end = $_POST['end_time'];
    $sql = 'SELECT COUNT(*) FROM products WHERE products.account_id = :account_id AND product_type ="brand" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$cycle_start, 'end_datetime'=>$cycle_end]);
    $mon_brand_count = $stmt->fetchColumn();
    if ($mon_brand_count == null) {
        $mon_brand_count = 0;
    }

    $sql = 'SELECT COUNT(*) FROM products WHERE products.account_id = :account_id AND product_type ="sku" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$cycle_start, 'end_datetime'=>$cycle_end]);
    $mon_sku_count = $stmt->fetchColumn();
    if ($mon_sku_count == null) {
        $mon_sku_count = 0;
    }

    $sql = 'SELECT COUNT(*) FROM products WHERE products.account_id = :account_id AND product_type ="dvc" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$cycle_start, 'end_datetime'=>$cycle_end]);
    $mon_dvc_count = $stmt->fetchColumn();
    if ($mon_dvc_count == null) {
        $mon_dvc_count = 0;
    }

    $sql = 'SELECT SUM(product_facing_count) FROM products WHERE products.account_id = :account_id AND  products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$cycle_start, 'end_datetime'=>$cycle_end]);
    $mon_facing_count = $stmt->fetchColumn();
    if ($mon_facing_count == null) {
        $mon_facing_count = 0;
    }
    if ($mon_sku_count == 0 && $mon_brand_count == 0 && $mon_dvc_count == 0 && $mon_facing_count == 0) {
        $mon_accuracy = 0;
    } else {
        $sql = "SELECT COUNT(products.product_id) FROM products INNER JOIN product_qa_errors ON products.product_id = product_qa_errors.product_id WHERE products.account_id = :account_id AND products.product_qa_datetime >= :start_datetime AND products.product_qa_datetime <= :end_datetime AND products.product_status = 2";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$cycle_start, 'end_datetime'=>$cycle_end]);
        $mon_error_type_count = $stmt->fetchColumn();

        $sql = 'SELECT COUNT(*) FROM products WHERE products.account_id = :account_id AND product_qa_status = "disapproved" AND products.product_qa_account_id IS NULL AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'], 'start_datetime'=>$cycle_start, 'end_datetime'=>$cycle_end]);
        $mon_system_errors = $stmt->fetchColumn();

        $total_count = ($mon_brand_count * 1.5) + ($mon_sku_count) + (($mon_facing_count  + $mon_dvc_count) / 2) * $weight;
        $mon_accuracy = round(((($total_count - ($mon_error_type_count + $mon_system_errors * 1)) / $total_count) * 100), 2);
    }
}
$date = new DateTime();
$tosub = new DateInterval('PT12H');
$date->sub($tosub);
if ($_POST['client_cat'] == 0 && $_POST['brand'] == 0) {
    $sql = "SELECT `user_db`.`accounts`.account_gid , b.probe_processed_hunter_id ,MIN(b.probe_hunter_processed_time) FROM probe b INNER JOIN `user_db`.`accounts` ON `user_db`.`accounts`.account_id = b.probe_processed_hunter_id WHERE b.probe_ticket_id =:ticket AND b.client_category_id IS NULL AND b.brand_id IS NULL AND b.probe_hunter_processed_time > :date_to_check GROUP BY b.probe_processed_hunter_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket'], 'date_to_check'=>$date->format('Y-m-d H:i:s')]);
    $initial_info = $stmt->fetch(PDO::FETCH_OBJ);
} else if ($_POST['client_cat'] == 0 && $_POST['brand'] != 0 ) {
    $sql = "SELECT `user_db`.`accounts`.account_gid , b.probe_processed_hunter_id ,MIN(b.probe_hunter_processed_time) FROM probe b INNER JOIN `user_db`.`accounts` ON `user_db`.`accounts`.account_id = b.probe_processed_hunter_id  WHERE b.probe_ticket_id =:ticket AND b.client_category_id IS NULL AND b.brand_id = :brand_id AND b.probe_hunter_processed_time > :date_to_check GROUP BY b.probe_processed_hunter_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket'], 'brand_id'=>(int)$_POST['brand'], 'date_to_check'=>$date->format('Y-m-d H:i:s')]);
    $initial_info = $stmt->fetch(PDO::FETCH_OBJ);
} else if ($_POST['brand'] == 0 && $_POST['client_cat'] != 0) {
    $sql = "SELECT `user_db`.`accounts`.account_gid , b.probe_processed_hunter_id ,MIN(b.probe_hunter_processed_time) FROM probe b INNER JOIN `user_db`.`accounts` ON `user_db`.`accounts`.account_id = b.probe_processed_hunter_id  WHERE b.probe_ticket_id =:ticket AND b.client_category_id = :client_id AND b.brand_id IS NULL AND b.probe_hunter_processed_time > :date_to_check GROUP BY b.probe_processed_hunter_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket'], 'client_id'=>(int)$_POST['client_cat'], 'date_to_check'=>$date->format('Y-m-d H:i:s')]);
    $initial_info = $stmt->fetch(PDO::FETCH_OBJ);
} else {
    $sql = "SELECT `user_db`.`accounts`.account_gid , b.probe_processed_hunter_id ,MIN(b.probe_hunter_processed_time) FROM probe b INNER JOIN `user_db`.`accounts` ON `user_db`.`accounts`.account_id = b.probe_processed_hunter_id  WHERE b.probe_ticket_id =:ticket AND b.client_category_id = :client_id AND b.brand_id = :brand_id AND b.probe_hunter_processed_time > :date_to_check GROUP BY b.probe_processed_hunter_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket'], 'client_id'=>(int)$_POST['client_cat'], 'brand_id'=>(int)$_POST['brand'], 'date_to_check'=>$date->format('Y-m-d H:i:s')]);
    $initial_info = $stmt->fetch(PDO::FETCH_OBJ);
}
$user_id = $_SESSION['id'];
$warning = '';
if ($initial_info->probe_processed_hunter_id != $user_id) {
    $warning .= $initial_info->account_gid;
}
$return_arr[] = array("warning"=>$warning,"number_of_rows" => $number_of_rows, "probe_brand_count"=>$probe_brand_count,"processing_probe_row" => $row_count, "number_of_handled_rows"=>$number_of_handled_rows, "brand_count"=>$brand_count, "sku_count"=>$sku_count, "dvc_count"=>$dvc_count, "checked_count"=>$checked_count, "error_count"=>$error_count, "system_error_count"=>$system_error_count, "facing_count"=>$facing_count, "number_of_products_added"=>$number_of_products_added, "rename_error_count"=>$rename_error_count, "error_type_count"=>$error_type_count, "mon_accuracy"=>$mon_accuracy);
echo json_encode($return_arr);
?>
