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

$valid_submission = false;
$error = '';
$success = '';
$product_type = $_POST['product_type'];
if (isset($_POST['alt_design_name']) && $_POST['alt_design_name'] != '') {
    $alt_design_name = trim($_POST['alt_design_name']);
} else {
    $alt_design_name = NULL;
}
try {
    $sql = 'INSERT INTO products (product_name, product_type, product_status, product_alt_design_name, product_facing_count, account_id) VALUES (:product_name, :product_type, :product_status, :product_alt_design_name, :product_facing_count, :account_id)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['product_name'=>trim($_POST['product_name']), 'product_type'=>$_POST['product_type'], 'product_status'=>$_POST['status'],'product_alt_design_name'=>$alt_design_name, 'product_facing_count'=>$_POST['facings'], 'account_id'=>$_SESSION['id']]);
    $last_id = (int)$pdo->lastInsertId();
    $sql = 'SELECT probe_key_id FROM probe_queue WHERE account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id']]);
    $probe_info = $stmt->fetch(PDO::FETCH_OBJ);
    $sql = 'INSERT INTO probe_product_info (probe_product_info_key_id, probe_product_info_product_id, probe_product_info_account_id) VALUES (:probe_product_info_key_id, :probe_product_info_product_id, :probe_product_info_account_id)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['probe_product_info_key_id'=>$probe_info->probe_key_id, 'probe_product_info_product_id'=>$last_id, 'probe_product_info_account_id'=>$_SESSION['id']]);
    $success = 'Product Saved';

    $is_duplicate = false;
    $duplicate_error = '';
    $fetched_info;
    $fetched_count = 0;

    if ($product_type == 'brand' ||$product_type == 'sku') {
        $sql = 'SELECT product_id, product_creation_time, product_type FROM products WHERE product_name = :product_name AND product_type = :product_type AND (product_qa_status = "pending" OR product_qa_status = "approved" )';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['product_name'=>trim($_POST['product_name']), 'product_type'=>$_POST['product_type']]);
        $fetched_count = $stmt->rowCount();
        $fetched_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else if ($product_type == 'dvc') {
        $sql = 'SELECT product_id, product_creation_time, product_type FROM products WHERE product_alt_design_name = :product_alt_design_name AND product_type = :product_type AND (product_qa_status = "pending" OR product_qa_status = "approved" )';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['product_alt_design_name'=>$alt_design_name, 'product_type'=>$_POST['product_type']]);
        $fetched_count = $stmt->rowCount();
        $fetched_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if ($fetched_count != 1) {
        $min_date_time;
        $min_product_id;
        $insert_index;
        $error_id;
        for ($i = 0; $i < count($fetched_info); $i++){
            if ($i == 0) {
                $min_date_time = $fetched_info[$i][product_creation_time];
                $min_product_id = $fetched_info[$i][product_id];
            } else {
                if ($min_date_time > $fetched_info[$i][product_creation_time]) {
                    $min_date_time = $fetched_info[$i][product_creation_time];
                    $min_product_id = $fetched_info[$i][product_id];
                }
            }
            if ($fetched_info[$i][product_id] == $last_id) {
                $insert_index = $i;
            }
        }

        if ($fetched_info[$insert_index][product_creation_time] != $min_date_time || $fetched_info[$insert_index][product_id] != $min_product_id){
            if ($fetched_info[$insert_index][product_type] == 'brand') {
                $error_id = 14;
                $duplicate_error = 'Duplicate BRAND, Error Count Increased';
            } else if ($fetched_info[$insert_index][product_type] == 'sku') {
                $error_id = 2;
                $duplicate_error = 'Duplicate SKU, Error Count Increased';
            } else if ($fetched_info[$insert_index][product_type] == 'dvc') {
                $error_id = 8;
                $duplicate_error = 'Duplicate DVC, Error Count Increased';
            }
            $is_duplicate = true;
            $sql = 'UPDATE products SET product_qa_status = "disapproved" WHERE product_id = :product_id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_id'=>$fetched_info[$insert_index][product_id]]);
        }

    }

    if ($_POST['status'] == 2 && !$is_duplicate) {
        $sql = 'INSERT INTO probe_qa_queue (product_id) VALUES (:product_id)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['product_id'=>$last_id]);
    }
}
catch(PDOException $e) {
    $error =$e->getMessage();
}

$return_arr[] = array("product_type" => $product_type, "error"=>$error, "success"=> $success ,"duplicate_error"=>$duplicate_error);
echo json_encode($return_arr);
?>