<?php
/*
    Author: Malika Liyanage
*/
session_start();
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

$product_info[] = array();

$sql = 'SELECT probe_qa_queue_id FROM probe_qa_queue WHERE account_id = :account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id' => $_SESSION['id']]);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);
$already_assigned = 0;

if ($row_count == 0) {
    $sql = 'UPDATE probe_qa_queue pqq SET pqq.account_id = :account_id, pqq.probe_being_handled = 1 WHERE pqq.product_id = :product_id AND pqq.account_id IS NULL';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id' => $_SESSION['id'], 'product_id' => $_POST['product_id']]);

    $sql = 'SELECT probe_qa_queue_id FROM probe_qa_queue WHERE account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id' => $_SESSION['id']]);
    $row_count = $stmt->rowCount(PDO::FETCH_OBJ);
} else {
    $already_assigned = 1;
}

if ($row_count === 1) {
    $sql = '
SELECT 
    p.product_id,
    p.product_name,
    p.product_alt_design_name,
    p.product_facing_count,
    p.product_hunt_type
FROM
    products p
        INNER JOIN
    probe_qa_queue pqq ON p.product_id = pqq.product_id
WHERE
	pqq.account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id' => $_SESSION['id']]);
    $product_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$return_arr[] = array("row_count" => $row_count, "already_assigned" => $already_assigned, "product_info" => $product_info);
echo json_encode($return_arr);
?>
