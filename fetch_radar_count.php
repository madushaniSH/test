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



if ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor' ) {
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

    $current_link_count = 0;
    if ($row_count == 1) {
        $sql = 'SELECT DISTINCT(a.radar_category) AS name FROM radar_queue b INNER JOIN radar_hunt a ON a.radar_hunt_id = b.radar_hunt_key_id WHERE (b.radar_being_handled = 0 OR b.account_id = :account_id) AND a.radar_category IS NOT NULL AND b.radar_queue_id = :radar_queue_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['radar_queue_id' =>$radar_queue_info->radar_queue_id,'account_id'=>$_SESSION['id']]);
        $radar_info = $stmt->fetch(PDO::FETCH_OBJ);
        $radar_cat = $radar_info->name;

        $sql = 'SELECT count(*) FROM radar_queue b INNER JOIN radar_hunt a ON a.radar_hunt_id = b.radar_hunt_key_id INNER JOIN radar_sources c ON c.radar_hunt_id = b.radar_hunt_key_id WHERE b.radar_queue_id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' =>$radar_queue_info->radar_queue_id]);
        $current_link_count = $stmt->fetchColumn();
    }

    $search_item = $_POST['radar_cat'].'';
    $sql = "SELECT count(*) FROM radar_queue b INNER JOIN radar_hunt a ON a.radar_hunt_id = b.radar_hunt_key_id  WHERE (b.radar_being_handled = 0 OR b.account_id = :account_id) AND a.radar_category = :search_item AND a.radar_ticket_id = :ticket";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search_item'=>$search_item, 'account_id'=>$_SESSION['id'], "ticket"=>$_POST['ticket']]);
    $radar_cat_count = $stmt->fetchColumn();
 
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

    $sql = 'SELECT COUNT(*) FROM radar_sources WHERE radar_sources.creation_time >= :start_datetime AND radar_sources.creation_time <= :end_datetime';
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

    $sql = "SELECT COUNT(DISTINCT products.product_id) FROM products INNER JOIN product_qa_errors ON products.product_id = product_qa_errors.product_id WHERE products.product_qa_datetime >= :start_datetime AND products.product_qa_datetime <= :end_datetime AND products.product_status = 2";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>$min_time, 'end_datetime'=>$maxtime]);
    $error_type_count = $stmt->fetchColumn();
} else {
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
    $current_link_count = 0;

    if ($row_count == 1) {
        $sql = 'SELECT DISTINCT(a.radar_category) AS name FROM radar_queue b INNER JOIN radar_hunt a ON a.radar_hunt_id = b.radar_hunt_key_id WHERE (b.radar_being_handled = 0 OR b.account_id = :account_id) AND a.radar_category IS NOT NULL AND b.radar_queue_id = :radar_queue_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['radar_queue_id' =>$radar_queue_info->radar_queue_id,'account_id'=>$_SESSION['id']]);
        $radar_info = $stmt->fetch(PDO::FETCH_OBJ);
        $radar_cat = $radar_info->name;

        $sql = 'SELECT count(*) FROM radar_queue b INNER JOIN radar_hunt a ON a.radar_hunt_id = b.radar_hunt_key_id INNER JOIN radar_sources c ON c.radar_hunt_id = b.radar_hunt_key_id WHERE b.radar_queue_id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' =>$radar_queue_info->radar_queue_id]);
        $current_link_count = $stmt->fetchColumn();

    }

    $search_item = $_POST['radar_cat'].'';
    $sql = "SELECT count(*) FROM radar_queue b INNER JOIN radar_hunt a ON a.radar_hunt_id = b.radar_hunt_key_id  WHERE (b.radar_being_handled = 0 OR b.account_id = :account_id) AND a.radar_category = :search_item AND a.radar_ticket_id = :ticket";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search_item'=>$search_item, 'account_id'=>$_SESSION['id'], "ticket"=>$_POST['ticket']]);
    $radar_cat_count = $stmt->fetchColumn();
    
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

    $sql = 'SELECT COUNT(*) FROM radar_sources WHERE radar_sources.account_id = :account_id AND (radar_sources.creation_time >= :start_datetime AND radar_sources.creation_time <= :end_datetime)';
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
        $total_count = ($mon_brand_count * 1.5) + ($mon_sku_count) + (($mon_facing_count  + $mon_dvc_count) / 2);
        $mon_accuracy = round(((($total_count - ($mon_error_type_count * 5)) / $total_count) * 100), 2);
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
        $total_count = ($mon_brand_count * 1.5) + ($mon_sku_count) + (($mon_facing_count  + $mon_dvc_count) / 2);
        $mon_accuracy = round(((($total_count - ($mon_error_type_count * 5)) / $total_count) * 100), 2);
    }
}
$return_arr[] = array("number_of_rows" => $number_of_rows, "processing_probe_row" => $row_count, "number_of_handled_rows"=>$number_of_handled_rows, "radar_cat_count"=>$radar_cat_count, "radar_cat"=>$radar_cat, "brand_count"=>$brand_count, "sku_count"=>$sku_count, "dvc_count"=>$dvc_count, "checked_count"=>$checked_count, "error_count"=>$error_count, "system_error_count"=>$system_error_count, "facing_count"=>$facing_count, "number_of_products_added"=>$number_of_products_added, "rename_error_count"=>$rename_error_count, "error_type_count"=>$error_type_count, "mon_accuracy"=>$mon_accuracy, "current_link_count"=>$current_link_count);
echo json_encode($return_arr);
?>