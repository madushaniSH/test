<?php
/*
    Filename: upload_probe.php
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
$dbname = 'GMI_US';
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

$probe_list = explode (",", $_POST['probe_list']);  
$brand = $_POST['brand'];
$category = $_POST['category'];

if ($brand != 'null') {
    $sql = 'SELECT brand_id FROM brand WHERE brand_name = :brand_name';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['brand_name'=>$brand]);
    $brand_info = $stmt->fetch(PDO::FETCH_OBJ);
    $brand_row_count = $stmt->rowCount(PDO::FETCH_OBJ);
    if ($brand_row_count == 0) {
        $sql = 'INSERT INTO brand (brand_name) VALUES (:brand_name)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['brand_name'=>$brand]);

        $sql = 'SELECT brand_id FROM brand WHERE brand_name = :brand_name';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['brand_name'=>$brand]);
        $brand_info = $stmt->fetch(PDO::FETCH_OBJ);
    }
    $brand_id = $brand_info->brand_id;
} else {
    $brand_id = null;
}

if ($category != 'null') {
    $sql = 'SELECT client_category_id FROM client_category WHERE client_category_name = :client_category_name';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['client_category_name'=>$category]);
    $category_info = $stmt->fetch(PDO::FETCH_OBJ);
    $category_row_cout = $stmt->rowCount(PDO::FETCH_OBJ);

    if ($category_row_cout == 0) {
        $sql = 'INSERT INTO client_category (client_category_name) VALUES (:client_category_name)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['client_category_name'=>$category]);

        $sql = 'SELECT client_category_id FROM client_category WHERE client_category_id = :client_category_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['client_category_id'=>$category]);
        $category_info = $stmt->fetch(PDO::FETCH_OBJ);
    }
    $category_id = $category_info->client_category_id;
} else {
    $category_id = null;
}


for ($i = 0; $i < count($probe_list); $i++) {
    $sql = 'INSERT INTO probe (brand_id, client_category_id, probe_id, probe_added_user_id) VALUES (:brand_id, :client_category_id, :probe_id, :probe_added_user_id)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['brand_id'=>$brand_id, 'client_category_id'=>$category_id, 'probe_id'=>$probe_list[$i], 'probe_added_user_id'=>$_SESSION['id']]);
}
?>