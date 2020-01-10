<?php
/*
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
// Current settings to connect to the user account database
require('../../user_db_connection.php');
$dbname = $_POST['project_name'];
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

$sql = 'SELECT product_id FROM product_ean_queue WHERE account_id = :account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id' => $_SESSION['id']]);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);
$already_assigned = 0;

$sql = 'SELECT * FROM product_ean_queue WHERE product_id = :product_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['product_id' => $_POST['product_id']]);
$product_row_count = $stmt->rowCount(PDO::FETCH_OBJ);

if ($row_count == 0 && $product_row_count == 0) {
    $sql = "INSERT INTO product_ean_queue (product_id, account_id, product_being_handled) VALUES (:product_id, :account_id, 1)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id' => $_SESSION['id'], 'product_id' => $_POST['product_id']]);

    $sql = 'SELECT product_id FROM product_ean_queue WHERE account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id' => $_SESSION['id']]);
    $row_count = $stmt->rowCount(PDO::FETCH_OBJ);
} else {
    $already_assigned = 1;
}


$return_arr[] = array("row_count" => $row_count, "already_assigned" => $already_assigned);
echo json_encode($return_arr);
?>
