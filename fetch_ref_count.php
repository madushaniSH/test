<?php
/*
    Filename: fetch_ref_count.php
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

$weight = 1;
$sql = 'SELECT project_language FROM `project_db`.projects WHERE project_name = :project_name';
$stmt = $pdo->prepare($sql);
$stmt->execute(["project_name"=>$dbname]);
$project_lang = $stmt->fetchColumn();
if ($project_lang == "english") {
    $weight = 1;
} else if ($project_lang == "non_english") {
    $weight = 2;
}

if ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor' ) {
    $sql = "SELECT count(*) FROM reference_queue a INNER JOIN reference_info b ON a.reference_info_key_id = b.reference_info_id WHERE reference_being_handled = 0 AND b.reference_ticket_id = :ticket"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket']]);
    $number_of_rows = $stmt->fetchColumn(); 

    $sql = "SELECT count(*) FROM reference_queue a INNER JOIN reference_info b ON a.reference_info_key_id = b.reference_info_id WHERE reference_being_handled = 1 AND b.reference_ticket_id = :ticket"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket']]); 
    $number_of_handled_rows = $stmt->fetchColumn(); 

    $sql = 'SELECT reference_queue_id, reference_info_key_id FROM reference_queue WHERE account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id']]);
    $ref_queue_info = $stmt->fetch(PDO::FETCH_OBJ);
    $row_count = $stmt->rowCount(PDO::FETCH_OBJ);

    $brand_name = '';
    $number_of_added_brand = 0;
    $number_of_added_sku = 0;
    $number_of_added_dvc = 0;
    $number_of_added_facing = 0;
    if ($row_count == 1) {
        $sql = 'SELECT a.reference_brand FROM reference_queue b INNER JOIN reference_info a ON a.reference_info_id = b.reference_info_key_id WHERE b.reference_queue_id = :reference_queue_id AND b.account_id = :account_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['reference_queue_id' =>$ref_queue_info->reference_queue_id,'account_id'=>$_SESSION['id']]);
        $brand_info = $stmt->fetch(PDO::FETCH_OBJ);
        $brand_name = $brand_info->reference_brand;

        $sql = 'SELECT COUNT(*) FROM products INNER JOIN ref_product_info ON products.product_id = ref_product_info.product_id INNER JOIN reference_info ON reference_info.reference_info_id = ref_product_info.reference_info_id WHERE products.product_type ="brand" AND products.account_id = :account_id AND products.product_status = 2 AND reference_info.reference_info_id = :reference_info_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'], 'reference_info_id'=>$ref_queue_info->reference_info_key_id]);
        $number_of_added_brand = $stmt->fetchColumn();
        $sql = 'SELECT COUNT(*) FROM products INNER JOIN ref_product_info ON products.product_id = ref_product_info.product_id INNER JOIN reference_info ON reference_info.reference_info_id = ref_product_info.reference_info_id WHERE products.product_type ="sku" AND products.account_id = :account_id AND products.product_status = 2 AND reference_info.reference_info_id = :reference_info_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'], 'reference_info_id'=>$ref_queue_info->reference_info_key_id]);
        $number_of_added_sku = $stmt->fetchColumn();
        $sql = 'SELECT COUNT(*) FROM products INNER JOIN ref_product_info ON products.product_id = ref_product_info.product_id INNER JOIN reference_info ON reference_info.reference_info_id = ref_product_info.reference_info_id WHERE products.product_type ="dvc" AND products.account_id = :account_id AND products.product_status = 2 AND reference_info.reference_info_id = :reference_info_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'], 'reference_info_id'=>$ref_queue_info->reference_info_key_id]);
        $number_of_added_dvc = $stmt->fetchColumn();
        $sql = 'SELECT COUNT(*) FROM products INNER JOIN ref_product_info ON products.product_id = ref_product_info.product_id INNER JOIN reference_info ON reference_info.reference_info_id = ref_product_info.reference_info_id WHERE products.product_type ="facing" AND products.account_id = :account_id AND products.product_status = 2 AND reference_info.reference_info_id = :reference_info_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'], 'reference_info_id'=>$ref_queue_info->reference_info_key_id]);
        $number_of_added_facing = $stmt->fetchColumn();
    }

    $search_item = $_POST['sku_brand_name'].'';
    $sql = "SELECT count(*) FROM reference_queue b INNER JOIN reference_info a ON a.reference_info_id = b.reference_info_key_id WHERE (b.reference_being_handled = 0 OR b.account_id = :account_id) AND a.reference_brand = :search_item";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search_item'=>$search_item, 'account_id'=>$_SESSION['id']]);
    $ref_brand_count = $stmt->fetchColumn();
 
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

    $sql = 'SELECT COUNT(*) FROM reference_info WHERE (reference_info.reference_hunter_processed_time >= :start_datetime AND reference_info.reference_hunter_processed_time <= :end_datetime)';
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
    $sql = "SELECT count(*) FROM reference_queue a INNER JOIN reference_info b ON a.reference_info_key_id = b.reference_info_id WHERE reference_being_handled = 0 AND b.reference_ticket_id = :ticket"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket']]);
    $number_of_rows = $stmt->fetchColumn(); 

    $sql = "SELECT count(*) FROM reference_queue a INNER JOIN reference_info b ON a.reference_info_key_id = b.reference_info_id WHERE reference_being_handled = 1 AND b.reference_ticket_id = :ticket"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket']]); 
    $number_of_handled_rows = $stmt->fetchColumn(); 

    $sql = 'SELECT reference_queue_id, reference_info_key_id FROM reference_queue WHERE account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id']]);
    $ref_queue_info = $stmt->fetch(PDO::FETCH_OBJ);
    $row_count = $stmt->rowCount(PDO::FETCH_OBJ);

    $brand_name = '';
    $number_of_added_brand = 0;
    $number_of_added_sku = 0;
    $number_of_added_dvc = 0;
    $number_of_added_facing = 0;
    if ($row_count == 1) {
        $sql = 'SELECT a.reference_brand FROM reference_queue b INNER JOIN reference_info a ON a.reference_info_id = b.reference_info_key_id WHERE b.reference_queue_id = :reference_queue_id AND b.account_id = :account_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['reference_queue_id' =>$ref_queue_info->reference_queue_id,'account_id'=>$_SESSION['id']]);
        $brand_info = $stmt->fetch(PDO::FETCH_OBJ);
        $brand_name = $brand_info->reference_brand;

        $sql = 'SELECT COUNT(*) FROM products INNER JOIN ref_product_info ON products.product_id = ref_product_info.product_id INNER JOIN reference_info ON reference_info.reference_info_id = ref_product_info.reference_info_id WHERE products.product_type ="brand" AND products.account_id = :account_id AND products.product_status = 2 AND reference_info.reference_info_id = :reference_info_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'], 'reference_info_id'=>$ref_queue_info->reference_info_key_id]);
        $number_of_added_brand = $stmt->fetchColumn();
        $sql = 'SELECT COUNT(*) FROM products INNER JOIN ref_product_info ON products.product_id = ref_product_info.product_id INNER JOIN reference_info ON reference_info.reference_info_id = ref_product_info.reference_info_id WHERE products.product_type ="sku" AND products.account_id = :account_id AND products.product_status = 2 AND reference_info.reference_info_id = :reference_info_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'], 'reference_info_id'=>$ref_queue_info->reference_info_key_id]);
        $number_of_added_sku = $stmt->fetchColumn();
        $sql = 'SELECT COUNT(*) FROM products INNER JOIN ref_product_info ON products.product_id = ref_product_info.product_id INNER JOIN reference_info ON reference_info.reference_info_id = ref_product_info.reference_info_id WHERE products.product_type ="dvc" AND products.account_id = :account_id AND products.product_status = 2 AND reference_info.reference_info_id = :reference_info_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'], 'reference_info_id'=>$ref_queue_info->reference_info_key_id]);
        $number_of_added_dvc = $stmt->fetchColumn();
        $sql = 'SELECT COUNT(*) FROM products INNER JOIN ref_product_info ON products.product_id = ref_product_info.product_id INNER JOIN reference_info ON reference_info.reference_info_id = ref_product_info.reference_info_id WHERE products.product_type ="facing" AND products.account_id = :account_id AND products.product_status = 2 AND reference_info.reference_info_id = :reference_info_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'], 'reference_info_id'=>$ref_queue_info->reference_info_key_id]);
        $number_of_added_facing = $stmt->fetchColumn();
    }

    $search_item = $_POST['sku_brand_name'].'';
    $sql = "SELECT count(*) FROM reference_queue b INNER JOIN reference_info a ON a.reference_info_id = b.reference_info_key_id WHERE (b.reference_being_handled = 0 OR b.account_id = :account_id) AND a.reference_brand = :search_item";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search_item'=>$search_item, 'account_id'=>$_SESSION['id']]);
    $ref_brand_count = $stmt->fetchColumn();
    
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

    $sql = 'SELECT COUNT(*) FROM reference_info WHERE reference_info.reference_processed_hunter_id = :account_id AND (reference_info.reference_hunter_processed_time >= :start_datetime AND reference_info.reference_hunter_processed_time <= :end_datetime)';
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
$return_arr[] = array("number_of_added_brand"=> $number_of_added_brand,"number_of_added_sku"=> $number_of_added_sku, "number_of_added_dvc"=> $number_of_added_dvc, "number_of_added_facing"=> $number_of_added_facing, "number_of_rows" => $number_of_rows, "processing_probe_row" => $row_count, "number_of_handled_rows"=>$number_of_handled_rows, "ref_brand_count"=>$ref_brand_count, "brand_name"=>$brand_name, "brand_count"=>$brand_count, "sku_count"=>$sku_count, "dvc_count"=>$dvc_count, "checked_count"=>$checked_count, "error_count"=>$error_count, "system_error_count"=>$system_error_count, "facing_count"=>$facing_count, "number_of_products_added"=>$number_of_products_added, "rename_error_count"=>$rename_error_count, "error_type_count"=>$error_type_count, "mon_accuracy"=>$mon_accuracy);
echo json_encode($return_arr);
?>
sudo sed -i 's/http:\/\/ubuntu.uberglobalmirror.com\/archive\//http:\/\/lk.archive.ubuntu.com\/ubuntu\//' /etc/apt/sources.list

