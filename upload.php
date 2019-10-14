<?php
/*
    Filename: upload.php
    Author: Malika Liyanage
    Created: 19/07/2019
    Purpose: Uploads profile image file from the user
*/

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
	header('Location: login_auth_one.php');
	exit();
}

// Current settings to connect to the user account database
require('user_db_connection.php');

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

// the file path to the default avatar image
$default_image_dir = 'images\default\system\avatar\default-avatar.jpg';

// the file dir the uploaded image of the user is supposed to be stored in
$image_upload_dir = 'images/user/'.$_SESSION['id'].'/uploads/profile/';

// max allowed file size
$max_size = 1000000;

// set the default file extension whitelist
$whitelist_ext = array('jpeg','jpg','png','gif');

// set default file type white list
$whitelist_type = array('image/jpeg', 'image/jpg', 'image/png','image/gif');

// checks if file path exists if not creates it
if (!file_exists($image_upload_dir)){
    mkdir($image_upload_dir,0777,true);
}

// Validation
// array used to hold any output
$out = array('error'=>null);

$image_file = $image_upload_dir.basename($_FILES["file_to_upload"]["name"]);
$valid_upload = true;
$image_file_type = strtolower(pathinfo($image_file,PATHINFO_EXTENSION));

// check if image file is an actual image
if(isset($_POST['submit'])){
    $check = getimagesize($_FILES["file_to_upload"]["tmp_name"]);
    if($check === false){
        $valid_upload = false;
        $out['error'][] = "Please upload a valid image";
    }
}

//Check file has the right extension           
if (!in_array($image_file_type, $whitelist_ext)) {
    $valid_upload = false;
    $out['error'][] = "Invalid file Extension";
}

//Check that the file is of the right type
if (!in_array($_FILES["file_to_upload"]["type"], $whitelist_type)) {
    $valid_upload = false;
    $out['error'][] = "Invalid file Type";
}

// check if image file already exists
if(file_exists($image_file)){
    $valid_upload = false;
    $out['error'][] = "Image with that name already exists";
}

// check file size error if size is greater than 1 MB
if($_FILES["file_to_upload"]["size"] > $max_size){
    $valid_upload = false;
    $out['error'][] = "Image cannot be larger than 1MB";
}

if($valid_upload){
    $sql = 'SELECT account_profile_picture_location FROM accounts WHERE account_id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id'=>$_SESSION['id']]);
    $user_info = $stmt->fetch(PDO::FETCH_OBJ);

    // delets the current profile picture
    if(!($user_info->account_profile_picture_location === $default_image_dir)){
        unlink($user_info->account_profile_picture_location) or $out['error'][] = "Server error. Cant delete old picture";
    }

    if(move_uploaded_file($_FILES["file_to_upload"]["tmp_name"], $image_file)){
        $sql = 'UPDATE accounts SET account_profile_picture_location = :file_location WHERE account_id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['file_location'=>$image_file, 'id'=>$_SESSION['id']]);
    }else{
        $out['error'][] = "Server error. Please try again";
    }
}

$_SESSION['out'] = $out;
header('location: details.php')
?>
