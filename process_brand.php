<?php
/*
    Filename: process_brand.php
    Author: Malika Liyanage
    Created: 29/07/2019
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

$sql = 'SELECT brand_name FROM brand WHERE brand_name = :brand_name';
$stmt = $pdo->prepare($sql);
$stmt->execute(['brand_name'=>$_POST['brand_name']]);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

if ($row_count == 0){
    // the file dir the uploaded image of the user is supposed to be stored in
    $image_upload_dir = "images/system/projects/$dbname/brand/images/";
  // set the default file extension whitelist
    $whitelist_ext = array('jpeg','jpg','jfif');

    // set default file type white list
    $whitelist_type = array('image/jpeg', 'image/jpg', 'image/jfif');

    // checks if file path exists if not creates it
    if (!file_exists($image_upload_dir)){
        mkdir($image_upload_dir,0777,true);
    }

    $image_file = $image_upload_dir.basename($_FILES["brand_logo"]["name"]);
    $valid_upload = true;
    $image_file_type = strtolower(pathinfo($image_file,PATHINFO_EXTENSION));
    $error = '';
    $valid_upload = true;

    // check if image file is an actual image
    $check = getimagesize($_FILES["brand_logo"]["tmp_name"]);
    if($check === false){
        $valid_upload = false;
        $error .= "Please upload a valid image<br>";
    }

    list($width, $height, $type, $attr) = getimagesize($_FILES["brand_logo"]["tmp_name"]);
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
    if (!in_array($_FILES["brand_logo"]["type"], $whitelist_type)) {
        $valid_upload = false;
        $error .= "Invalid file Type<br>";
    }

    if ($valid_upload) {
        if(move_uploaded_file($_FILES["brand_logo"]["tmp_name"], $image_file)) {
            $brand_local_name = $_POST['brand_local_name'];
            $brand_global_code = $_POST['brand_global_code'];

            if ($brand_global_code == '') {
                $brand_global_code = NULL;
            }
            
            if ($brand_local_name == '') {
                $brand_local_name = NULL;
            }

            $sql = 'INSERT INTO brand (manufacturer_id, brand_name, brand_local_name, brand_source, brand_image_location, brand_recognition_level, brand_global_code) VALUES (:manufacturer_id, :brand_name, :brand_local_name, :brand_source, :brand_image_location, :brand_recognition_level, :brand_global_code)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['manufacturer_id'=>$_POST['brand_manufacturer'], 'brand_name'=>$_POST['brand_name'], 'brand_local_name'=>$brand_local_name ,'brand_source'=>$_POST['brand_source'], 'brand_image_location'=>$image_file, 'brand_recognition_level'=>$_POST['recognition_value'], 'brand_global_code'=>$brand_global_code]);
            echo "<span class=\"success-popup\">Submitted</span>";
        } else {
            echo "<span class=\"error-popup\">Server Error</span>"; 
        }
    } else {
        echo "<span class=\"error-popup\">$error</span>";
    }
} else {
    echo "<span class=\"error-popup\">Brand already added</span>";
}

?>