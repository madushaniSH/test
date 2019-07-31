<?php
/*
    Filename: process_client_sub_category.php
    Author: Malika Liyanage
    Created: 25/07/2019
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

$sql = 'SELECT client_sub_category_name FROM client_sub_category WHERE client_sub_category_name = :client_sub_category_name';
$stmt = $pdo->prepare($sql);
$stmt->execute(['client_sub_category_name'=>$_POST['client_sub_category_name']]);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

if ($row_count == 0){
    $sql = 'INSERT INTO client_sub_category (client_sub_category_name, client_sub_category_local_name) VALUES (:client_sub_category_name, :client_sub_category_local_name)';
    $stmt = $pdo->prepare($sql);
    if($_POST['client_sub_category_local_name'] != ''){
        $stmt->execute(['client_sub_category_name'=>$_POST['client_sub_category_name'], 'client_sub_category_local_name'=>$_POST['client_sub_category_local_name']]);
    }else{
        $stmt->execute(['client_sub_category_name'=>$_POST['client_sub_category_name'], 'client_sub_category_local_name'=>NULL]);
    }
    echo "<span class=\"success-popup\">Submitted</span>";
    // script for closing modal
    echo "
    <script>
        jQuery(document).ready(function() {
            document.getElementById('close_suggest_client_sub_category').click();
        });                
    </script>
    ";    
}else{
    echo "<span class=\"error-popup\">Client Sub Category already added</span>";
}