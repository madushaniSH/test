<?php
/*
    Author: Malika Liyanage
*/
session_start();
// If the user is not logged in redirect to the login page...
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
$dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;

/*
    Attempts to connect to the databse, if no connection was estabishled
    kills the script
*/
try {
    // Creating a new PDO instance
    $pdo = new PDO($dsn, $user, $pwd);
    // setting the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // throws error message
    echo "<p>Connection to database failed<br>Reason: " . $e->getMessage() . '</p>';
    exit();
}


$warning = '';
try {
    $sql = 'SELECT product_ean_id FROM product_ean WHERE product_id = :product_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['product_id' => $_POST['productId']]);
    $row_count = $stmt->rowCount();

    $sql = 'SELECT assign_datetime FROM product_ean_queue WHERE product_id = :product_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['product_id' => $_POST['productId']]);
    $assign_datetime = $stmt->fetchColumn();

    $productId = $_POST['productId'];
    $productEAN = $_POST['selectedEAN'];
    $unmatchReasonId = $_POST['unmatchReasonId'];
    $duplicateProductName = $_POST['duplicateProductName'];
    $webLinks = explode(",", $_POST['webLinks']);
    $itemCode = $_POST['itemCode'];
    $additionalComment = $_POST['additionalComment'];
    $matchWith = $_POST['matchWith'];

    if  ($productEAN === '') {
        $productEAN = NULL;
    }

    if ($unmatchReasonId === '') {
        $unmatchReasonId = NULL;
    }

    if ($duplicateProductName === '') {
        $duplicateProductName = NULL;
    }

    if ($itemCode === '') {
        $itemCode = NULL;
    }

    if ($additionalComment === '') {
        $additionalComment = NULL;
    }

    if ($matchWith === '' || $matchWith === 'undefined') {
        $matchWith = NULL;
    }

    if ($row_count == 0) {
        $sql = 'INSERT INTO product_ean (product_id, product_ean, unmatch_reason_id, duplicate_product_name, account_id, product_item_code, additional_comment, matched_method, ean_assign_datetime)
            VALUES (:product_id, :product_ean, :unmatch_id, :duplicate_product_name, :account_id, :item_code, :additional_comment, :match_with, :assign_datetime)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(
            [
                'product_id' => $productId,
                'product_ean' => $productEAN,
                'unmatch_id' => $unmatchReasonId,
                'duplicate_product_name' => $duplicateProductName,
                'account_id' => $_SESSION['id'],
                'item_code' => $itemCode,
                'additional_comment' => $additionalComment,
                'match_with' => $matchWith,
                'assign_datetime' => $assign_datetime
            ]
        );

        foreach ($webLinks as $webLink) {
            if ($webLink !== '') {
                $sql = 'INSERT INTO product_weblinks (product_id, weblink) VALUES (:product_id, :weblink)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute(
                    [
                        'product_id' => $productId,
                        'weblink' => $webLink,
                    ]
                );
            }
        }

        $sql = 'DELETE FROM product_ean_queue WHERE account_id = :account_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id' => $_SESSION['id']]);
    }
} catch (PDOException $e) {
    $warning = $e->getMessage();
}
$return_arr[] = array('error' => $warning);
echo json_encode($return_arr);



