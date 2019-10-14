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
    $search_term = $_POST['sku_brand_name'];

    do {
        $sql = 'UPDATE reference_queue AS upd INNER JOIN (SELECT t1.reference_info_key_id FROM reference_queue AS t1 INNER JOIN reference_info AS t2 ON t2.reference_info_id = t1.reference_info_key_id WHERE t1.reference_being_handled = 0 AND t1.account_id IS NULL AND t2.reference_brand = :search_term AND t2.reference_ticket_id = :ticket LIMIT 1 ) AS sel ON sel.reference_info_key_id = upd.reference_info_key_id SET upd.account_id = :account_id, upd.reference_being_handled = 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'], 'search_term'=>$search_term, 'ticket'=>$_POST['ticket']]);

        $sql = 'SELECT reference_queue_id FROM reference_queue WHERE account_id = :account_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id']]);
        $reference_info = $stmt->fetch(PDO::FETCH_OBJ);
        $last_id = $reference_info->reference_queue_id;
        $this_count = $stmt->rowCount(PDO::FETCH_OBJ);
        $iterations++;
    } while ($this_count == 0  && $iterations < 10);
}

$sql = 'SELECT c.ticket_id, a.reference_recognition_level, a.reference_ean, a.reference_short_name, a.reference_category, a.reference_sub_category, a.reference_brand, a.reference_sub_brand, a.reference_manufacturer, a.reference_base_size, a.reference_size, a.reference_measurement_unit, a.reference_container_type, a.reference_agg_level, a.reference_segment, a.reference_count_upc2, a.reference_flavor_detail, a.reference_case_pack, a.reference_multi_pack FROM reference_queue b INNER JOIN reference_info a ON a.reference_info_id = b.reference_info_key_id INNER JOIN project_tickets c ON a.reference_ticket_id = c.project_ticket_system_id WHERE b.reference_info_key_id = :last_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['last_id'=>$last_id]);
$ref_info = $stmt->fetch(PDO::FETCH_ASSOC);

$return_arr[] = array("ean" => $ref_info["reference_ean"], "brand" => $ref_info["reference_brand"], "ref_info"=>$ref_info);
echo json_encode($return_arr);
?>