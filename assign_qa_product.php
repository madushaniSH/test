<?php
/*
    Filename: assign_qa_product.php
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

$sql = 'SELECT probe_qa_queue_id FROM probe_qa_queue WHERE account_id = :account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$probe_info = $stmt->fetch(PDO::FETCH_OBJ);
$last_id = $probe_info->probe_qa_queue_id;
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

if ($row_count == 0) {
    $this_count = 0;
    $iterations = 0;
    $search_term = '';
    if ($_POST['product_type'] == 'sku') {
        $search_term .= $_POST['sku_brand_name'].' ';
    }
    if ($_POST['product_type'] == 'dvc') {
        $search_term .=  $_POST['sku_dvc_name'].' ';
    }
    $search_term .= '%';
    do {
        $sql = 'UPDATE probe_qa_queue AS upd INNER JOIN (SELECT t1.product_id FROM probe_qa_queue AS t1 INNER JOIN products AS t2 ON t2.product_id = t1.product_id WHERE t1.probe_being_handled = 0 AND t1.account_id IS NULL AND t2.product_type = :product_type AND t2.product_name LIKE :search_term LIMIT 1 ) AS sel ON sel.product_id = upd.product_id SET upd.account_id = :account_id, upd.probe_being_handled = 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'], 'product_type'=>$_POST['product_type'], 'search_term'=>$search_term]);

        $sql = 'SELECT probe_qa_queue_id FROM probe_qa_queue WHERE account_id = :account_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id']]);
        $probe_info = $stmt->fetch(PDO::FETCH_OBJ);
        $last_id = $probe_info->probe_qa_queue_id;
        $this_count = $stmt->rowCount(PDO::FETCH_OBJ);
        $iterations++;
    } while ($this_count == 0 && $iterations < 10);
}
$sql = 'SELECT probe.probe_id, brand.brand_name, client_category.client_category_name, products.product_type, products.product_name, products.product_alt_design_name FROM  probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id INNER JOIN probe_product_info ON products.product_id = probe_product_info.probe_product_info_product_id LEFT JOIN probe ON probe.probe_key_id = probe_product_info.probe_product_info_key_id LEFT JOIN client_category ON client_category.client_category_id = probe.client_category_id LEFT JOIN brand ON brand.brand_id = probe.brand_id WHERE probe_qa_queue.probe_qa_queue_id = :probe_qa_queue_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['probe_qa_queue_id'=>$last_id]);
$probe_info = $stmt->fetch(PDO::FETCH_OBJ);
$return_arr[] = array("brand_name" => $probe_info->brand_name, "client_category_name" => $probe_info->client_category_name , "product_type"=>$probe_info->product_type, "product_name"=>$probe_info->product_name, "product_alt_design_name"=>$probe_info->product_alt_design_name, "probe_id" => $probe_info->probe_id);
echo json_encode($return_arr);
?>