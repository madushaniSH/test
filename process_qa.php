<?php
/*
    Filename: assign_probe.php
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

$product_name = '';
$sql = "SELECT products.product_id, products.product_name FROM probe_qa_queue INNER JOIN products ON probe_qa_queue.product_id = products.product_id WHERE probe_qa_queue.account_id = :account_id AND probe_qa_queue.probe_being_handled = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$product_info = $stmt->fetch(PDO::FETCH_OBJ);
$row_count = $stmt->rowCount();
$current_product_name = trim($product_info->product_name);
$search_string = '';


if ($row_count == 1){
    if ($_POST['product_type'] == 'brand') {
        if (trim($product_info->product_name) != trim($_POST['product_rename'])) {
            $sql = "UPDATE products SET product_name = :product_name, product_previous = :product_previous WHERE product_id = :product_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_name'=>trim($_POST['product_rename']), 'product_previous'=>trim($product_info->product_name), 'product_id'=>$product_info->product_id]);
            $product_name = trim($_POST['product_rename']);
        }
    }
    if ($_POST['product_type'] == 'sku') {
        if (trim($product_info->product_name) != trim($_POST['product_rename'])) {
            // updating the qa's assigned product row only
            $sql = "UPDATE products SET product_name = :product_name, product_previous = :product_previous WHERE product_id = :product_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_name'=>trim($_POST['product_rename']), 'product_previous'=>trim($product_info->product_name), 'product_id'=>$product_info->product_id]);
            $product_name = trim($_POST['product_rename']);

            $search_string = $current_product_name.'%';
            // updating all the dvc products which begin with the same product name
            try {
                $sql = "UPDATE products SET product_previous = product_name, product_alt_design_previous = product_alt_design_name, product_name = REPLACE(product_name, :current_product_name, :new_product_name), product_alt_design_name = REPLACE(product_alt_design_name, :current_product_name_dvc, :new_product_name_dvc)  WHERE (product_name LIKE :search_string OR product_alt_design_name LIKE :search_string_2) AND product_type = 'dvc' ";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['current_product_name'=>trim($product_info->product_name),'new_product_name'=>trim($_POST['product_rename']), 'current_product_name_dvc'=>trim($product_info->product_name),'new_product_name_dvc'=>trim($_POST['product_rename']), 'search_string'=>$search_string,'search_string_2'=>$search_string]);
            } catch(PDOException $e){
                echo $e->getMessage();
            }
        }
    }

    if ($_POST['error_qa'] != '') {
        $error_qa = explode(",", $_POST['error_qa']);
        for ($i = 0; $i < count($error_qa); $i++) {
            $sql = 'INSERT INTO product_qa_errors (product_id, error_id) VALUES (:product_id, :error_id)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_id'=>$product_info->product_id, 'error_id'=>$error_qa[$i]]);
        }
    }
    
    $error_image_count = $_POST['error_image_count'];
    for ($i = 0; $i < $error_image_count; $i++){
        $image_name = 'error_images'.$i;
        // the file dir the uploaded image of the user is supposed to be stored in
        $image_upload_dir = "images/system/projects/".$_POST['project_name']."/QA/".preg_replace('/\s+/', '', $product_name)."/error_images/";
        // checks if file path exists if not creates it
        if (!file_exists($image_upload_dir)){
            mkdir($image_upload_dir,0777,true);
        }
        while (true) {
            $filename = uniqid(rand(), true).'.jpg';
            if (!file_exists($image_upload_dir.$filename)) break;
        }
        $image_file = $image_upload_dir.$filename;
        echo $image_name;
        if(move_uploaded_file($_FILES[$image_name]["tmp_name"],$image_file)){
            $sql = 'INSERT INTO project_error_images (product_id, project_error_image_location) VALUES (:product_id, :image_location)';
            $sql = $pdo->prepare($sql);
            $sql->execute(['product_id'=>$product_info->product_id,'image_location'=>$image_file]);
        }
    }
    
    $now = new DateTime();
    $sql = "UPDATE products SET product_qa_account_id = :account_id, product_qa_datetime = :date_time, product_qa_status = :qa_status WHERE product_id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id'], 'date_time'=>$now->format('Y-m-d H:i:s'), 'qa_status'=>$_POST['status'], 'product_id'=>$product_info->product_id]);
    
    $sql = 'DELETE FROM probe_qa_queue WHERE account_id = :account_id AND probe_being_handled = 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$_SESSION['id']]);
}

?>