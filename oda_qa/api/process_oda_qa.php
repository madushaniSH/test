<?php
/*
    Filename: process_oda_qa.php
    Author: Malika Liyanage
*/
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: ../login_auth_one.php');
    exit();
} else {
    if(!($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'ODA Supervisor' || $_SESSION['role'] === 'ODA')){
        header('Location: ../index.php');
        exit();
    }
}
require('../../user_db_connection.php');
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

$product_name = '';
$sql = "SELECT products.product_id, products.product_name, products.product_alt_design_name
    FROM oda_queue INNER JOIN products ON oda_queue.product_id = products.product_id WHERE oda_queue.account_id = :account_id AND oda_queue.qa_being_handled = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$product_info = $stmt->fetch(PDO::FETCH_OBJ);
$row_count = $stmt->rowCount();
$current_product_name = trim($product_info->product_name);
$product_name = trim($product_info->product_name);
$search_string = '';
$product_comment = '';
if ($_POST['product_comment'] == '' || $_POST['product_comment'] == 'undefined') {
    $product_comment = NULL;
} else {
    $product_comment = $_POST['product_comment'];
}

if ($row_count == 1){
    if ($_POST['product_type'] == 'brand') {
        if (trim($product_info->product_name) != trim($_POST['product_rename'])) {
            $sql = "UPDATE products SET product_name = :product_name, product_qa_previous = :product_previous WHERE product_id = :product_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_name'=>trim($_POST['product_rename']), 'product_previous'=>trim($product_info->product_name), 'product_id'=>$product_info->product_id]);
            $product_name = trim($_POST['product_rename']);
        }
    }
    if ($_POST['product_type'] == 'sku') {
        if (trim($product_info->product_name) != trim($_POST['product_rename'])) {
            // updating the qa's assigned product row only
            $sql = "UPDATE products SET product_name = :product_name, product_qa_previous = :product_previous WHERE product_id = :product_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_name'=>trim($_POST['product_rename']), 'product_previous'=>trim($product_info->product_name), 'product_id'=>$product_info->product_id]);
            $product_name = trim($_POST['product_rename']);

            $search_string = $current_product_name.'%';
            // updating all the dvc products which begin with the same product name
            try {
                $sql = "UPDATE products SET product_qa_previous = product_name, product_alt_design_qa_previous = product_alt_design_name, product_name = REPLACE(product_name, :current_product_name, :new_product_name) WHERE product_name LIKE :search_string AND product_type = 'dvc' ";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['current_product_name'=>trim($product_info->product_name),'new_product_name'=>trim($_POST['product_rename']), 'search_string'=>$search_string]);
            } catch(PDOException $e){
                echo $e->getMessage();
            }
        }
    }

    if ($_POST['product_type'] == 'dvc') {
        if (trim($product_info->product_name) != trim($_POST['product_rename'])) {
            $sql = "UPDATE products SET product_name = :product_name, product_qa_previous = :product_previous WHERE product_id = :product_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_name'=>trim($_POST['product_rename']), 'product_previous'=>trim($product_info->product_name), 'product_id'=>$product_info->product_id]);
            $product_name = trim($_POST['product_rename']);
        }

        if (trim($product_info->product_alt_design_name) != trim($_POST['product_alt_rename'])) {
            $sql = "UPDATE products SET product_alt_design_name = :product_alt_design_name, product_alt_design_qa_previous = :product_alt_design_previous WHERE product_id = :product_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_alt_design_name'=>trim($_POST['product_alt_rename']), 'product_alt_design_previous'=>trim($product_info->product_alt_design_name), 'product_id'=>$product_info->product_id]);
        }
        $product_name = trim($_POST['product_alt_rename']);
    }

    if ($_POST['error_qa'] != '') {
        $error_qa = explode(",", $_POST['error_qa']);
        for ($i = 0; $i < count($error_qa); $i++) {
            $sql = 'INSERT INTO product_oda_errors (product_id, error_id) VALUES (:product_id, :error_id)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_id'=>$product_info->product_id, 'error_id'=>$error_qa[$i]]);
        }
    }

    $manu_link = trim($_POST['manu_link']);
    if ($manu_link == '' || $_POST['product_type'] != 'brand') {
        $manu_link = NULL;
    }

    $now = new DateTime();
    $sql = "UPDATE products 
    SET manufacturer_link = :manufacturer_link,product_facing_count = :num_facings ,
        product_oda_account_id = :account_id, product_oda_datetime = :date_time, product_qa_status = :qa_status, product_oda_comment = :product_comment WHERE product_id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['manufacturer_link'=>$manu_link,'account_id'=>$_SESSION['id'], 'date_time'=>$now->format('Y-m-d H:i:s'), 'qa_status'=>$_POST['status'], 'product_id'=>$product_info->product_id, 'num_facings'=>$_POST['num_facings'], 'product_comment'=>$product_comment]);

    $sql = 'DELETE FROM oda_queue WHERE account_id = :account_id AND qa_being_handled = 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id']]);
}

?>
