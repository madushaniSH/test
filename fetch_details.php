<?php
/*
    Filename: get_qa_brand_list.php
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

$sql = 'SELECT products.product_id, probe.probe_id AS "Probe ID", brand.brand_name AS "Brand", products.product_alt_design_name AS "Alternative Design Name", products.product_name AS "English Product Name" , products.product_type AS "Product Type", products.product_creation_time AS "Product Creation Time", client_category.client_category_name AS "Category", products.account_id AS "hunter_gid", products.product_qa_account_id AS "qa_gid", products.product_qa_datetime AS "QA Time", products.product_qa_status AS "Product Status"
FROM products
INNER JOIN probe_product_info
ON products.product_id = probe_product_info.probe_product_info_product_id
INNER JOIN probe 
ON probe_product_info.probe_product_info_key_id = probe.probe_key_id
LEFT JOIN brand
ON probe.brand_id = brand.brand_id
LEFT JOIN client_category
ON probe.client_category_id = client_category.client_category_id
WHERE products.product_status = 2';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$hunted_product_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

for ($i = 0; $i < count($hunted_product_info); $i++){
    if ($hunted_product_info[$i]["Alternative Design Name"] == null) {
        $hunted_product_info[$i]["Alternative Design Name"] = '';
    }
    if ($hunted_product_info[$i]["Brand"] == null) {
        $hunted_product_info[$i]["Brand"] = '';
    }
    if ($hunted_product_info[$i]["Category"] == null) {
        $hunted_product_info[$i]["Category"] = '';
    }
    $sql = 'SELECT b.account_gid FROM products INNER JOIN user_db.accounts b ON products.account_id = b.account_id WHERE b.account_id = :account_id AND products.product_id = :product_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$hunted_product_info[$i][hunter_gid], 'product_id'=>$hunted_product_info[$i][product_id]]);
    $hunter_gid = $stmt->fetch(PDO::FETCH_OBJ);
    $hunted_product_info[$i]['Hunter GID'] = $hunter_gid->account_gid;
    unset($hunted_product_info[$i][hunter_gid]);

    $sql = 'SELECT b.account_gid FROM products INNER JOIN user_db.accounts b ON products.product_qa_account_id = b.account_id WHERE b.account_id = :account_id AND products.product_id = :product_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$hunted_product_info[$i][qa_gid], 'product_id'=>$hunted_product_info[$i][product_id]]);
    $qa_gid = $stmt->fetch(PDO::FETCH_OBJ);
    $hunted_product_info[$i]['QA GID'] = $qa_gid->account_gid;
    unset($hunted_product_info[$i][qa_gid]);

    $sql = 'SELECT a.project_error_name FROM product_qa_errors b INNER JOIN project_errors a ON b.error_id = a.project_error_id WHERE b.product_id = :product_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['product_id'=>$hunted_product_info[$i][product_id]]);
    $qa_errors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $error_string = '';
    for ($j = 0; $j < count($qa_errors); $j++) {
        $error_string .= $qa_errors[$j][project_error_name].',';
    }
    if ($error_string != '') {
        $error_string = rtrim($error_string, ",");
    } 

    $hunted_product_info[$i]['QA Errors'] = $error_string;
    
    $sql = 'SELECT project_error_image_location FROM project_error_images WHERE product_id = :product_id LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['product_id'=>$hunted_product_info[$i][product_id]]);
    $row_count = $stmt->rowCount(PDO::FETCH_OBJ);
    $file_info = $stmt->fetch(PDO::FETCH_OBJ);
    $error_image_path = '';

    if ($row_count != 0) {
        $error_image_path = substr($file_info->project_error_image_location, 0, strrpos($file_info->project_error_image_location, '/') );
    } 

    $hunted_product_info[$i]['Error Image Location'] = $error_image_path;
    unset($hunted_product_info[$i][product_id]);
}

$sql = 'SELECT probe.probe_id AS "Probe ID", probe.probe_process_comment AS "Comment", probe_status.probe_status_name AS "Probe Status", probe.probe_hunter_processed_time  AS "Probe Processed Time",a.account_gid AS "Hunter GID"
FROM probe
LEFT JOIN probe_status
ON probe.probe_status_id = probe_status.probe_status_id
LEFT JOIN user_db.accounts a
ON probe.probe_processed_hunter_id = a.account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$probe_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

for ($i = 0; $i < count($probe_details); $i++){
    if ($probe_details[$i]["Comment"] == null) {
        $probe_details[$i]["Comment"] = '';
    }
    if ($probe_details[$i]["Hunter GID"] == null) {
        $probe_details[$i]["Hunter GID"] = '';
    }
    if ($probe_details[$i]["Probe Status"] == null) {
        $probe_details[$i]["Probe Status"] = '';
    }
    if ($probe_details[$i]["Probe Processed Time"] == null) {
        $probe_details[$i]["Probe Processed Time"] = '';
    }
}

$sql = 'SELECT DISTINCT probe.probe_processed_hunter_id, a.account_gid FROM probe INNER JOIN user_db.accounts a ON probe.probe_processed_hunter_id = a.account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$hunter_summary = $stmt->fetchAll(PDO::FETCH_ASSOC);

for ($i = 0; $i < count($hunter_summary); $i++){
    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="brand" AND account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id]]);
    $brand_count = $stmt->fetchColumn();
    $hunter_summary[$i]["Brand Hunted"] = $brand_count;

    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="sku" AND account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id]]);
    $sku_count = $stmt->fetchColumn();
    $hunter_summary[$i]["SKU Hunted"] = $sku_count;

    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="dvc" AND account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id]]);
    $dvc_count = $stmt->fetchColumn();
    $hunter_summary[$i]["DVC Hunted"] = $dvc_count;

    $sql = 'SELECT COUNT(*) FROM probe WHERE probe.probe_processed_hunter_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id]]);
    $checked_count = $stmt->fetchColumn();
    $hunter_summary[$i]["Checked Probe Count"] = $checked_count;

    $sql = 'SELECT COUNT(*) FROM products WHERE products.account_id = :account_id AND product_qa_status = "disapproved"';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id]]);
    $error_count = $stmt->fetchColumn();
    $hunter_summary[$i]["Error Count"] = $error_count;
    unset($hunter_summary[$i][probe_processed_hunter_id]);
}

$return_arr[] = array("hunted_product_info"=>$hunted_product_info, "probe_details"=>$probe_details, "hunter_summary"=>$hunter_summary);
echo json_encode($return_arr);
?>