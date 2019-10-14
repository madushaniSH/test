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

$sql = 'SELECT attribute_name FROM attribute WHERE attribute_name = :attribute_name';
$stmt = $pdo->prepare($sql);
$stmt->execute(['attribute_name'=>$_POST['new_attribute']]);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

if ($row_count == 0) {
    $sql = 'INSERT INTO attribute (attribute_name) VALUES (:attribute_name)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['attribute_name'=>$_POST['new_attribute']]);
    echo "<span class=\"success-popup\">Submitted</span>";
    // script for closing modal
    echo "
    <script>
        jQuery(document).ready(function() {
            get_attribute_list();
        });                
    </script>
    ";
} else {
    echo "<span class=\"error-popup\">Attribute already added</span>";
}
?>