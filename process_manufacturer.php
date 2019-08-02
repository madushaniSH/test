<?php
/*
    Filename: process_manufacturer.php
    Author: Malika Liyanage
    Created: 26/07/2019
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

$sql = 'SELECT manufacturer_name FROM manufacturer WHERE manufacturer_name = :manufacturer_name';
$stmt = $pdo->prepare($sql);
$stmt->execute(['manufacturer_name'=>$_POST['manufacturer_name']]);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

if($row_count == 0){
    // the file dir the uploaded image of the user is supposed to be stored in
    $image_upload_dir = "images/system/projects/$dbname/manufacturer/images/";

    // set the default file extension whitelist
    $whitelist_ext = array('jpeg','jpg','jfif');

    // set default file type white list
    $whitelist_type = array('image/jpeg', 'image/jpg', 'image/jfif');

    // checks if file path exists if not creates it
    if (!file_exists($image_upload_dir)){
        mkdir($image_upload_dir,0777,true);
    }

    $image_file = $image_upload_dir.basename($_FILES["manu_logo"]["name"]);
    $valid_upload = true;
    $image_file_type = strtolower(pathinfo($image_file,PATHINFO_EXTENSION));
    $error = '';
    $valid_upload = true;

    // check if image file is an actual image
    $check = getimagesize($_FILES["manu_logo"]["tmp_name"]);
    if($check === false){
        $valid_upload = false;
        $error .= "Please upload a valid image<br>";
    }

    list($width, $height, $type, $attr) = getimagesize($_FILES["manu_logo"]["tmp_name"]);
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
    if (!in_array($_FILES["manu_logo"]["type"], $whitelist_type)) {
        $valid_upload = false;
        $error .= "Invalid file Type<br>";
    }

    if($valid_upload){
        if(move_uploaded_file($_FILES["manu_logo"]["tmp_name"], $image_file)) {
            $manufacturer_local_name = $_POST['manufacturer_local_name'];
            $manufacturer_source = $_POST['manufacturer_source'];
            
            if ($manufacturer_local_name == '') {
                $manufacturer_local_name = NULL;
            }

            if ($manufacturer_source == '') {
                $manufacturer_source = NULL;
            }

            $sql = 'INSERT INTO manufacturer (manufacturer_name, manufacturer_local_name, manufacturer_source, manufacturer_image_location) VALUES (:manufacturer_name, :manufacturer_local_name, :manufacturer_source, :manufacturer_image_location)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['manufacturer_name'=>$_POST['manufacturer_name'], 'manufacturer_local_name'=>$manufacturer_local_name, 'manufacturer_source'=>$manufacturer_source, 'manufacturer_image_location'=>$image_file]);

            echo "<span class=\"success-popup\">Submitted</span>";
            // script for closing modal
            echo "
            <script>
                jQuery(document).ready(function() {
                    get_manufacturer_list();
                    document.getElementById('close_suggest_manufacturer').click();
                });                
            </script>
            ";
        }else{
            echo "<span class=\"error-popup\">Server Error</span>";
        }
    }else{
        echo "<span class=\"error-popup\">$error</span>";
    }
}else{
    echo "<span class=\"error-popup\">Manufacturer already added</span>";
}
?>