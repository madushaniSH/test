<?php
/*
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

$sql = 'SELECT DISTINCT probe_processed_hunter_id, account_gid FROM (SELECT DISTINCT probe.probe_processed_hunter_id, a.account_gid FROM probe INNER JOIN user_db.accounts a ON probe.probe_processed_hunter_id = a.account_id UNION ALL SELECT DISTINCT radar_hunt.radar_hunter_id AS "probe_processed_hunter_id", a.account_gid FROM radar_hunt INNER JOIN user_db.accounts a ON radar_hunt.radar_hunter_id = a.account_id) t3';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$hunter_summary = $stmt->fetchAll(PDO::FETCH_ASSOC);
for ($i = 0; $i < count($hunter_summary); $i++){
    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="brand" AND account_id = :account_id AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
    $brand_count = $stmt->fetchColumn();
    $hunter_summary[$i]["Brand Hunted"] = $brand_count;
    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="sku" AND account_id = :account_id AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
    $sku_count = $stmt->fetchColumn();
    $hunter_summary[$i]["SKU Hunted"] = $sku_count;
    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="dvc" AND account_id = :account_id AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
    $dvc_count = $stmt->fetchColumn();
    $hunter_summary[$i]["DVC Hunted"] = $dvc_count;
    $sql = 'SELECT SUM(product_facing_count) FROM products WHERE account_id = :account_id AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
    $facing_count = $stmt->fetchColumn();
    if ($facing_count == NULL) {
        $facing_count = 0;
    }
    $hunter_summary[$i]["Hunted Facing Count"] = $facing_count;
    $sql = 'SELECT COUNT(*) FROM probe WHERE probe.probe_processed_hunter_id = :account_id AND (probe.probe_hunter_processed_time >= :start_datetime AND probe.probe_hunter_processed_time <= :end_datetime)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
    $checked_count = $stmt->fetchColumn();
    $hunter_summary[$i]["Checked Probe Count"] = $checked_count;

    $sql = 'SELECT COUNT(*) FROM radar_sources WHERE radar_sources.account_id = :account_id AND (radar_sources.creation_time >= :start_datetime AND radar_sources.creation_time <= :end_datetime)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
    $checked_count = $stmt->fetchColumn();
    $hunter_summary[$i]["Radar Link Count"] = $checked_count;

    $sql = 'SELECT COUNT(*) FROM products WHERE products.account_id = :account_id AND product_qa_status = "disapproved" AND products.product_qa_account_id IS NOT NULL AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
    $error_count = $stmt->fetchColumn();
    $hunter_summary[$i]["Disapproved Products"] = $error_count;

    $sql = "SELECT COUNT(*) FROM products WHERE ( product_type = 'sku' OR product_type = 'brand' ) AND account_id = :account_id AND (products.product_previous IS NOT NULL OR products.product_alt_design_previous IS NOT NULL) AND products.product_qa_datetime >= :start_datetime AND products.product_qa_datetime <= :end_datetime AND products.product_status = 2";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
    $error_count = $stmt->fetchColumn();
    $hunter_summary[$i]["Rename Errors"] = $error_count;

    $sql = 'SELECT COUNT(*) FROM products WHERE products.account_id = :account_id AND product_qa_status = "disapproved" AND products.product_qa_account_id IS NULL AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
    $error_count = $stmt->fetchColumn();
    $hunter_summary[$i]["System Errors"] = $error_count;

    $sql = "SELECT COUNT(DISTINCT products.product_id) FROM products INNER JOIN product_qa_errors ON products.product_id = product_qa_errors.product_id WHERE products.account_id = :account_id AND products.product_qa_datetime >= :start_datetime AND products.product_qa_datetime <= :end_datetime AND products.product_status = 2";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
    $error_count = $stmt->fetchColumn();
    $hunter_summary[$i]["QA Errors"] = $error_count;

    /*$date = DateTime::createFromFormat('d', '15');
    $today = date("Y-m-d");
    if ($today < $date) {
        $prev_date = DateTime::createFromFormat('d', 15)->add(new DateInterval('P-1M'));
        $cycle_start = $prev_date->format('Y-m-d');
        $cycle_end  = $date->format('Y-m-d');
    } else {
        $cycle_start = $date->format('Y-m-d');
        $next_date = DateTime::createFromFormat('d', 15)->add(new DateInterval('P1M'));
        $cycle_end  = $next_date->format('Y-m-d');
    }*/
    $total_count = ($hunter_summary[$i]["Brand Hunted"] * 1.5)  + $hunter_summary[$i]["SKU Hunted"] + (($hunter_summary[$i]["DVC Hunted"] + $hunter_summary[$i]["Hunted Facing Count"]) / 2);
    $monthly_accuracy = round(((($total_count - ($hunter_summary[$i]["QA Errors"] * 5) )/ $total_count) * 100),2);
    $hunter_summary[$i]["Accuracy"] = $monthly_accuracy . '%';

    unset($hunter_summary[$i][probe_processed_hunter_id]);
}

$return_arr[] = array("hunter_summary"=>$hunter_summary, "dbname" =>$dbname);
echo json_encode($return_arr);
?>