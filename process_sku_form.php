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
$sql = 'SELECT product_name FROM product WHERE product_name = :product_name';
$stmt = $pdo->prepare($sql);
$stmt->execute(['product_name'=>$_POST['name']]);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

if ($row_count == 0) {
    // the file dir the uploaded image of the user is supposed to be stored in
    $image_upload_dir = "images/system/projects/$dbname/products/images/";
    // set the default file extension whitelist
    $whitelist_ext = array('jpeg','jpg','jfif');

    // set default file type white list
    $whitelist_type = array('image/jpeg', 'image/jpg', 'image/jfif');

    // checks if file path exists if not creates it
    if (!file_exists($image_upload_dir)){
        mkdir($image_upload_dir,0777,true);
    }

    $image_file = $image_upload_dir.basename($_FILES["front_image"]["name"]);
    $valid_upload = true;
    $image_file_type = strtolower(pathinfo($image_file,PATHINFO_EXTENSION));
    $error = '';
    $valid_upload = true;

    // check if image file is an actual image
    $check = getimagesize($_FILES["front_image"]["tmp_name"]);
    if($check === false){
        $valid_upload = false;
        $error .= "Please upload a valid image<br>";
    }

    list($width, $height, $type, $attr) = getimagesize($_FILES["front_image"]["tmp_name"]);
    if(!(($width >= 300)  && ($height >= 300))){
        $valid_upload = false;
        $error .= "Error, low quality image<br>";
    }

    //Check file has the right extension           
    if (!in_array($image_file_type, $whitelist_ext)) {
        $valid_upload = false;
        $error .= "Invalid file Extension<br>";
    }

    //Check that the file is of the right type
    if (!in_array($_FILES["front_image"]["type"], $whitelist_type)) {
        $valid_upload = false;
        $error .= "Invalid file Type<br>";
    }

    if($valid_upload){
        if(move_uploaded_file($_FILES["front_image"]["tmp_name"], $image_file)) {
            $sql = 'INSERT INTO product_image (product_image_location) VALUES (:product_image_location)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_image_location'=>$image_file]);

            $sql = 'SELECT product_image_id FROM product_image WHERE product_image_location = :product_image_location';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_image_location'=>$image_file]);
            $image_info = $stmt->fetch(PDO::FETCH_OBJ);

            $sql = 'INSERT INTO product (product_name, brand_id, client_category_id, product_image_id, product_container_type_id) VALUES (:product_name, :brand_id, :client_category_id, :product_image_id, :product_container_type_id)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_name'=>$_POST['name'], 'brand_id'=>$_POST['brand_id'], 'client_category_id'=>$_POST['client_category_id'], 'product_image_id'=>$image_info->product_image_id, 'product_container_type_id'=>$_POST['container_type_id']]);

            echo "<span class=\"success-popup\">Submitted</span>";
        }else{
            echo "<span class=\"error-popup\">Server Error</span>";
        }
    }else{
        echo "<span class=\"error-popup\">$error</span>";
    }
} else {
    echo "<span class=\"error-popup\">Product already added</span>";
}
?>
