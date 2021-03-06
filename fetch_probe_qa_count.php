<?php
/*
    Filename: fetch_probe_qa_count.php
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

if ($_POST['type'] == 'probe') {
    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN probe_product_info ON probe_product_info.probe_product_info_product_id = products.product_id INNER JOIN probe ON probe_product_info.probe_product_info_key_id = probe.probe_key_id WHERE products.product_type = 'brand' AND probe.probe_ticket_id = :ticket AND products.product_hunt_type = :selected_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket'], "selected_type"=>$_POST['type']]);
    $brand_count = $stmt->fetchColumn();

    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN probe_product_info ON probe_product_info.probe_product_info_product_id = products.product_id INNER JOIN probe ON probe_product_info.probe_product_info_key_id = probe.probe_key_id WHERE products.product_type = 'sku' AND probe.probe_ticket_id = :ticket AND products.product_hunt_type = :selected_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket'], "selected_type"=>$_POST['type']]);
    $sku_count = $stmt->fetchColumn();

    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN probe_product_info ON probe_product_info.probe_product_info_product_id = products.product_id INNER JOIN probe ON probe_product_info.probe_product_info_key_id = probe.probe_key_id WHERE products.product_type = 'dvc' AND probe.probe_ticket_id = :ticket AND products.product_hunt_type = :selected_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket'], "selected_type"=>$_POST['type']]);
    $dvc_count = $stmt->fetchColumn();

    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN probe_product_info ON probe_product_info.probe_product_info_product_id = products.product_id INNER JOIN probe ON probe_product_info.probe_product_info_key_id = probe.probe_key_id WHERE products.product_type = 'facing' AND probe.probe_ticket_id = :ticket AND products.product_hunt_type = :selected_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket'], "selected_type"=>$_POST['type']]);
    $facing_count = $stmt->fetchColumn();

    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN probe_product_info ON probe_product_info.probe_product_info_product_id = products.product_id INNER JOIN probe ON probe_product_info.probe_product_info_key_id = probe.probe_key_id WHERE products.product_type = 'brand' AND (probe_qa_queue.probe_being_handled = 0 OR probe_qa_queue.account_id = :account_id) AND probe.probe_ticket_id = :ticket AND products.product_hunt_type = :selected_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id'], "ticket"=>$_POST['ticket'], "selected_type"=>$_POST['type']]);
    $brand_user_count = $stmt->fetchColumn();

    $search_item = $_POST['sku_brand_name'].' %';
    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN probe_product_info ON probe_product_info.probe_product_info_product_id = products.product_id INNER JOIN probe ON probe_product_info.probe_product_info_key_id = probe.probe_key_id WHERE products.product_type = 'sku' AND (probe_qa_queue.probe_being_handled = 0 OR probe_qa_queue.account_id = :account_id) AND products.product_name LIKE :search_item AND probe.probe_ticket_id = :ticket AND products.product_hunt_type = :selected_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search_item'=>$search_item, 'account_id'=>$_SESSION['id'], "ticket"=>$_POST['ticket'], "selected_type"=>$_POST['type']]);
    $brand_sku_count = $stmt->fetchColumn();


    $search_item = $_POST['sku_dvc_name'];
    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN probe_product_info ON probe_product_info.probe_product_info_product_id = products.product_id INNER JOIN probe ON probe_product_info.probe_product_info_key_id = probe.probe_key_id WHERE products.product_type = 'dvc' AND (probe_qa_queue.probe_being_handled = 0 OR probe_qa_queue.account_id = :account_id)AND products.product_alt_design_name LIKE :search_item AND probe.probe_ticket_id = :ticket AND products.product_hunt_type = :selected_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search_item'=>$search_item, 'account_id'=>$_SESSION['id'], "ticket"=>$_POST['ticket'], "selected_type"=>$_POST['type']]);
    $dvc_sku_count = $stmt->fetchColumn();

    $search_item = $_POST['sku_facing_name'].' %';
    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN probe_product_info ON probe_product_info.probe_product_info_product_id = products.product_id INNER JOIN probe ON probe_product_info.probe_product_info_key_id = probe.probe_key_id WHERE products.product_type = 'facing' AND (probe_qa_queue.probe_being_handled = 0 OR probe_qa_queue.account_id = :account_id)AND products.product_name LIKE :search_item AND probe.probe_ticket_id = :ticket AND products.product_hunt_type = :selected_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search_item'=>$search_item, 'account_id'=>$_SESSION['id'], "ticket"=>$_POST['ticket'], "selected_type"=>$_POST['type']]);
    $facing_sku_count = $stmt->fetchColumn();


    $sql = 'SELECT probe_qa_queue.probe_qa_queue_id, products.product_type FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id WHERE probe_qa_queue.account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id']]);
    $probe_info = $stmt->fetch(PDO::FETCH_OBJ);
    $row_count = $stmt->rowCount(PDO::FETCH_OBJ);
}else if ($_POST['type'] == 'radar') {
    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN radar_sources ON radar_sources.radar_product_id = products.product_id INNER JOIN radar_hunt ON radar_hunt.radar_hunt_id = radar_sources.radar_hunt_id WHERE products.product_type = 'brand' AND radar_hunt.radar_ticket_id = :ticket AND products.product_hunt_type = :selected_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket'], "selected_type"=>$_POST['type']]);
    $brand_count = $stmt->fetchColumn();

    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN radar_sources ON radar_sources.radar_product_id = products.product_id INNER JOIN radar_hunt ON radar_hunt.radar_hunt_id = radar_sources.radar_hunt_id WHERE products.product_type = 'sku' AND radar_hunt.radar_ticket_id = :ticket AND products.product_hunt_type = :selected_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket'], "selected_type"=>$_POST['type']]);
    $sku_count = $stmt->fetchColumn();

    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN radar_sources ON radar_sources.radar_product_id = products.product_id INNER JOIN radar_hunt ON radar_hunt.radar_hunt_id = radar_sources.radar_hunt_id WHERE products.product_type = 'dvc' AND radar_hunt.radar_ticket_id = :ticket AND products.product_hunt_type = :selected_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket'], "selected_type"=>$_POST['type']]);
    $dvc_count = $stmt->fetchColumn();

    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN radar_sources ON radar_sources.radar_product_id = products.product_id INNER JOIN radar_hunt ON radar_hunt.radar_hunt_id = radar_sources.radar_hunt_id WHERE products.product_type = 'facing' AND radar_hunt.radar_ticket_id = :ticket AND products.product_hunt_type = :selected_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket'], "selected_type"=>$_POST['type']]);
    $facing_count = $stmt->fetchColumn();

    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN radar_sources ON radar_sources.radar_product_id = products.product_id INNER JOIN radar_hunt ON radar_hunt.radar_hunt_id = radar_sources.radar_hunt_id WHERE products.product_type = 'brand' AND (probe_qa_queue.probe_being_handled = 0 OR probe_qa_queue.account_id = :account_id) AND radar_hunt.radar_ticket_id = :ticket AND products.product_hunt_type = :selected_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id'], "ticket"=>$_POST['ticket'], "selected_type"=>$_POST['type']]);
    $brand_user_count = $stmt->fetchColumn();

    $search_item = $_POST['sku_brand_name'].' %';
    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN radar_sources ON radar_sources.radar_product_id = products.product_id INNER JOIN radar_hunt ON radar_hunt.radar_hunt_id = radar_sources.radar_hunt_id WHERE products.product_type = 'sku' AND (probe_qa_queue.probe_being_handled = 0 OR probe_qa_queue.account_id = :account_id) AND products.product_name LIKE :search_item AND products.product_hunt_type = :selected_type AND radar_hunt.radar_ticket_id = :ticket";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search_item'=>$search_item, 'account_id'=>$_SESSION['id'], "selected_type"=>$_POST['type'], "ticket"=>$_POST['ticket']]);
    $brand_sku_count = $stmt->fetchColumn();


    $search_item = $_POST['sku_dvc_name'];
    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN radar_sources ON radar_sources.radar_product_id = products.product_id INNER JOIN radar_hunt ON radar_hunt.radar_hunt_id = radar_sources.radar_hunt_id WHERE products.product_type = 'dvc' AND (probe_qa_queue.probe_being_handled = 0 OR probe_qa_queue.account_id = :account_id)AND products.product_alt_design_name LIKE :search_item AND products.product_hunt_type = :selected_type AND radar_hunt.radar_ticket_id = :ticket";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search_item'=>$search_item, 'account_id'=>$_SESSION['id'], "selected_type"=>$_POST['type'], "ticket"=>$_POST['ticket']]);
    $dvc_sku_count = $stmt->fetchColumn();

    $search_item = $_POST['sku_facing_name'].' %';
    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN radar_sources ON radar_sources.radar_product_id = products.product_id INNER JOIN radar_hunt ON radar_hunt.radar_hunt_id = radar_sources.radar_hunt_id WHERE products.product_type = 'facing' AND (probe_qa_queue.probe_being_handled = 0 OR probe_qa_queue.account_id = :account_id)AND products.product_name LIKE :search_item AND products.product_hunt_type = :selected_type AND radar_hunt.radar_ticket_id = :ticket";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search_item'=>$search_item, 'account_id'=>$_SESSION['id'], "selected_type"=>$_POST['type'], "ticket"=>$_POST['ticket']]);
    $facing_sku_count = $stmt->fetchColumn();


    $sql = 'SELECT probe_qa_queue.probe_qa_queue_id, products.product_type FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id WHERE probe_qa_queue.account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id']]);
    $probe_info = $stmt->fetch(PDO::FETCH_OBJ);
    $row_count = $stmt->rowCount(PDO::FETCH_OBJ);
}else if ($_POST['type'] == 'reference') {
    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN ref_product_info ON ref_product_info.product_id = products.product_id INNER JOIN reference_info ON ref_product_info.reference_info_id = reference_info.reference_info_id WHERE products.product_type = 'brand' AND reference_info.reference_ticket_id = :ticket AND products.product_hunt_type = :selected_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket'], "selected_type"=>$_POST['type']]);
    $brand_count = $stmt->fetchColumn();

    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN ref_product_info ON ref_product_info.product_id = products.product_id INNER JOIN reference_info ON ref_product_info.reference_info_id = reference_info.reference_info_id WHERE products.product_type = 'sku' AND reference_info.reference_ticket_id = :ticket AND products.product_hunt_type = :selected_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket'], "selected_type"=>$_POST['type']]);
    $sku_count = $stmt->fetchColumn();

    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN ref_product_info ON ref_product_info.product_id = products.product_id INNER JOIN reference_info ON ref_product_info.reference_info_id = reference_info.reference_info_id WHERE products.product_type = 'dvc' AND reference_info.reference_ticket_id = :ticket AND products.product_hunt_type = :selected_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket'], "selected_type"=>$_POST['type']]);
    $dvc_count = $stmt->fetchColumn();

    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN ref_product_info ON ref_product_info.product_id = products.product_id INNER JOIN reference_info ON ref_product_info.reference_info_id = reference_info.reference_info_id WHERE products.product_type = 'facing' AND reference_info.reference_ticket_id = :ticket AND products.product_hunt_type = :selected_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$_POST['ticket'], "selected_type"=>$_POST['type']]);
    $facing_count = $stmt->fetchColumn();

    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN ref_product_info ON ref_product_info.product_id = products.product_id INNER JOIN reference_info ON ref_product_info.reference_info_id = reference_info.reference_info_id WHERE products.product_type = 'brand' AND reference_info.reference_ticket_id = :ticket AND (probe_qa_queue.probe_being_handled = 0 OR probe_qa_queue.account_id = :account_id) AND products.product_hunt_type = :selected_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id'], "ticket"=>$_POST['ticket'], "selected_type"=>$_POST['type']]);
    $brand_user_count = $stmt->fetchColumn();

    $search_item = $_POST['sku_brand_name'].' %';
    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN ref_product_info ON ref_product_info.product_id = products.product_id INNER JOIN reference_info ON ref_product_info.reference_info_id = reference_info.reference_info_id WHERE products.product_type = 'sku' AND (probe_qa_queue.probe_being_handled = 0 OR probe_qa_queue.account_id = :account_id) AND products.product_name LIKE :search_item AND products.product_hunt_type = :selected_type AND reference_info.reference_ticket_id = :ticket";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search_item'=>$search_item, 'account_id'=>$_SESSION['id'], "selected_type"=>$_POST['type'], "ticket"=>$_POST['ticket']]);
    $brand_sku_count = $stmt->fetchColumn();


    $search_item = $_POST['sku_dvc_name'];
    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN ref_product_info ON ref_product_info.product_id = products.product_id INNER JOIN reference_info ON ref_product_info.reference_info_id = reference_info.reference_info_id WHERE products.product_type = 'dvc' AND (probe_qa_queue.probe_being_handled = 0 OR probe_qa_queue.account_id = :account_id)AND products.product_alt_design_name LIKE :search_item AND products.product_hunt_type = :selected_type AND reference_info.reference_ticket_id = :ticket";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search_item'=>$search_item, 'account_id'=>$_SESSION['id'], "selected_type"=>$_POST['type'], "ticket"=>$_POST['ticket']]);
    $dvc_sku_count = $stmt->fetchColumn();

    $search_item = $_POST['sku_facing_name'].' %';
    $sql = "SELECT count(*) FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN ref_product_info ON ref_product_info.product_id = products.product_id INNER JOIN reference_info ON ref_product_info.reference_info_id = reference_info.reference_info_id WHERE products.product_type = 'facing' AND (probe_qa_queue.probe_being_handled = 0 OR probe_qa_queue.account_id = :account_id)AND products.product_name LIKE :search_item AND products.product_hunt_type = :selected_type AND reference_info.reference_ticket_id = :ticket";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search_item'=>$search_item, 'account_id'=>$_SESSION['id'], "selected_type"=>$_POST['type'], "ticket"=>$_POST['ticket']]);
    $facing_sku_count = $stmt->fetchColumn();


    $sql = 'SELECT probe_qa_queue.probe_qa_queue_id, products.product_type FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id WHERE probe_qa_queue.account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id']]);
    $probe_info = $stmt->fetch(PDO::FETCH_OBJ);
    $row_count = $stmt->rowCount(PDO::FETCH_OBJ);
}
$return_arr[] = array("brand_count" => $brand_count, "sku_count" => $sku_count, "dvc_count" => $dvc_count,"processing_probe_row" => $row_count, "product_type" => $probe_info->product_type, "brand_sku_count"=>$brand_sku_count, "brand_dvc_count"=>$dvc_sku_count, "brand_user_count"=>$brand_user_count, "facing_count"=>$facing_count, "facing_sku_count"=>$facing_sku_count);
echo json_encode($return_arr);
?>