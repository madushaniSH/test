<?php
/*
    Filename: new_sku_form.php
    Author: Malika Liyanage
    Created: 23/07/2019
    Purpose: Used for entering a new sku to the system
*/
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
	header('Location: login_auth_one.php');
	exit();
}

// Current settings to connect to the user account database
require('product_connection.php');

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
$image_file = '';

function check_and_upload_images($image_name){
    // the file dir the uploaded image of the user is supposed to be stored in
    $image_upload_dir = "images/system/projects/test_db/products/images/";
    // set the default file extension whitelist
    $whitelist_ext = array('jpg');
    // checks if file path exists if not creates it
    if (!file_exists($image_upload_dir)){
        mkdir($image_upload_dir,0777,true);
    }

    while (true) {
        $filename = uniqid(rand(), true).'.jpg';
        if (!file_exists($image_upload_dir.$filename)) break;
    }
    $GLOBALS[$image_file] = $image_upload_dir.$filename;
    $valid_upload = true;
    $image_file_type = strtolower(pathinfo($GLOBALS[$image_file],PATHINFO_EXTENSION));
    // check if image file is an actual image
    $check = getimagesize($_FILES[$image_name]["tmp_name"]);
    if($check === false){
        $valid_upload = false;
    }
    list($width, $height, $type, $attr) = getimagesize($_FILES[$image_name]["tmp_name"]);
    if(!(($width >= 300)  && ($height >= 300))){
        $valid_upload = false;
    }
    //Check file has the right extension           
    if (!in_array($image_file_type, $whitelist_ext)) {
        $valid_upload = false;
    }
    if ($valid_upload) {
        if (!move_uploaded_file($_FILES[$image_name]["tmp_name"], $GLOBALS[$image_file])) {
            $valid_upload = false;
        }
    }
    return $valid_upload;
}
?>

<?php
$sql = 'SELECT product_name FROM product WHERE product_name = :product_name';
$stmt = $pdo->prepare($sql);
$stmt->execute(['product_name'=>$_POST['name']]);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

if ($row_count == 0) {
    $valid_upload = true;
    if (is_uploaded_file($_FILES['file-input-front']['tmp_name'])){
        if (!check_and_upload_images('file-input-front')) {
            $valid_upload = false;
            $error .= '<br>Front:Error with image upload';
        } else {
            $sql = 'INSERT INTO product_image (product_image_location) VALUES (:product_image_location)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_image_location'=>$GLOBALS[$image_file]]);

            $sql = 'SELECT product_image_id FROM product_image WHERE product_image_location = :product_image_location';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_image_location'=>$GLOBALS[$image_file]]);
            $image_info = $stmt->fetch(PDO::FETCH_OBJ);
        }
    } else {
        $error .= '<br>Front:Image not uploaded';
    }

    if (is_uploaded_file($_FILES['file-input-top']['tmp_name'])){
        if (!check_and_upload_images('file-input-top')) {
            $valid_upload = false;
            $error .= '<br>Top:Error with image upload';
        } else {
            $sql = 'UPDATE product_image SET product_top_image_location = :product_top_image_location WHERE product_image_id = :product_image_id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_top_image_location'=>$GLOBALS[$image_file], 'product_image_id'=>$image_info->product_image_id]);
        }
    } 

    if (is_uploaded_file($_FILES['file-input-back']['tmp_name'])){
        if (!check_and_upload_images('file-input-back')) {
            $valid_upload = false;
            $error .= '<br>Back:Error with image upload';
        } else {
            $sql = 'UPDATE product_image SET product_back_image_location = :product_back_image_location WHERE product_image_id = :product_image_id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_back_image_location'=>$GLOBALS[$image_file], 'product_image_id'=>$image_info->product_image_id]);
        }
    }

    if (is_uploaded_file($_FILES['file-input-bottom']['tmp_name'])){
        if (!check_and_upload_images('file-input-bottom')) {
            $valid_upload = false;
            $error .= '<br>Bottom:Error with image upload';
        } else {
            $sql = 'UPDATE product_image SET product_bottom_image_location = :product_bottom_image_location WHERE product_image_id = :product_image_id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_bottom_image_location'=>$GLOBALS[$image_file], 'product_image_id'=>$image_info->product_image_id]);
        }
    }

    if (is_uploaded_file($_FILES['file-input-side1']['tmp_name'])){
        if (!check_and_upload_images('file-input-side1')) {
            $valid_upload = false;
            $error .= '<br>Side1:Error with image upload';
        } else {
            $sql = 'UPDATE product_image SET product_side1_image_location = :product_side1_image_location WHERE product_image_id = :product_image_id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_side1_image_location'=>$GLOBALS[$image_file], 'product_image_id'=>$image_info->product_image_id]);
        }
    } 

    if (is_uploaded_file($_FILES['file-input-side2']['tmp_name'])){
        if (!check_and_upload_images('file-input-side2')) {
            $valid_upload = false;
            $error .= '<br>Side2:Error with image upload';
        } else {
            $sql = 'UPDATE product_image SET product_side2_image_location = :product_side2_image_location WHERE product_image_id = :product_image_id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_side2_image_location'=>$GLOBALS[$image_file], 'product_image_id'=>$image_info->product_image_id]);
        }
    } 
    

   if($valid_upload){
        $sql = 'INSERT INTO product (product_name, account_id, brand_id, client_category_id, product_image_id) VALUES (:product_name, :account_id, :brand_id, :client_category_id, :product_image_id)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['product_name'=>$_POST['name'], 'account_id'=>$_SESSION['id'], 'brand_id'=>$_POST['brand_id'], 'client_category_id'=>$_POST['client_category_id'], 'product_image_id'=>$image_info->product_image_id]);

        $sql = 'SELECT product_id FROM product WHERE product_name = :product_name';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['product_name'=>$_POST['name']]);
        $product_info = $stmt->fetch(PDO::FETCH_OBJ);

        if (isset($_POST['container_type_id'])) {
            $sql = 'UPDATE product SET product_container_type_id = :product_container_type_id WHERE product_id = :product_id';
            $stmt= $pdo->prepare($sql);
            $stmt->execute(['product_container_type_id'=>$_POST['container_type_id'], 'product_id'=>$product_info->product_id]);
        }

        if (isset($_POST['item_code'])) {
            $sql = 'UPDATE product SET product_item_code = :product_item_code WHERE product_id = :product_id';
            $stmt= $pdo->prepare($sql);
            $stmt->execute(['product_item_code'=>$_POST['item_code'], 'product_id'=>$product_info->product_id]);
        }

        if (isset($_POST['global_code'])) {
            $sql = 'UPDATE product SET product_global_code = :product_global_code WHERE product_id = :product_id';
            $stmt= $pdo->prepare($sql);
            $stmt->execute(['product_global_code'=>$_POST['global_code'], 'product_id'=>$product_info->product_id]);
        }

        echo "<span class=\"success-popup\">Submitted</span>";
        echo "
            <script>
                jQuery(document).ready(function() {
                    window.location.href = 'products.php';
                });                
            </script>
            ";
    }else{
        echo "<span class=\"error-popup\">$error</span>";
    }
} else {
    echo "<span class=\"error-popup\">Product already added</span>";
}
?>
