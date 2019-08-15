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
$sql = 'SELECT product_id FROM products WHERE product_name = :product_name AND product_type = :product_type';
$stmt = $pdo->prepare($sql);
$stmt->execute(['product_name'=>$_POST['product_name'], 'product_type'=>$_POST['product_type']]);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

if ($row_count == 0) {
    if (isset($_POST['alt_design_name']) && $_POST['alt_design_name'] != '') {
        $alt_design_name = $_POST['alt_design_name'];
    } else {
        $alt_design_name = NULL;
    }
    try {
        $sql = 'INSERT INTO products (product_name, product_type, product_alt_design_name, account_id) VALUES (:product_name, :product_type, :product_alt_design_name, :account_id)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['product_name'=>$_POST['product_name'], 'product_type'=>$_POST['product_type'], 'product_alt_design_name'=>$alt_design_name, 'account_id'=>$_SESSION['id']]);
        $last_id = (int)$pdo->lastInsertId();

        $sql = 'SELECT probe_key_id FROM probe_queue WHERE account_id = :account_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id']]);
        $probe_info = $stmt->fetch(PDO::FETCH_OBJ);

        $sql = 'INSERT INTO probe_product_info (probe_product_info_key_id, probe_product_info_product_id, probe_product_info_account_id) VALUES (:probe_product_info_key_id, :probe_product_info_product_id, :probe_product_info_account_id)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['probe_product_info_key_id'=>$probe_info->probe_key_id, 'probe_product_info_product_id'=>$last_id, 'probe_product_info_account_id'=>$_SESSION['id']]);

        $success = 'Product Saved';
    }
    catch(PDOException $e) {
        $error =$e->getMessage();
    }
} 

$return_arr[] = array("product_row_count" => $row_count, "product_type" => $product_type, "error"=>$error, "success"=> $success);
echo json_encode($return_arr);
?>