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
    if (!($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'ODA Supervisor' || $_SESSION['role'] === 'ODA')) {
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
    $productChainName = $_POST['chainProductName'];
    $productName = $_POST['productName'];
    $productEAN = $_POST['selectedEAN'];
    $unmatchReasonId = $_POST['unmatchReasonId'];
    $duplicateProductName = $_POST['duplicateProductName'];
    $webLinks = explode(",", $_POST['webLinks']);
    $itemCode = $_POST['itemCode'];
    $additionalComment = $_POST['additionalComment'];
    $matchWith = $_POST['matchWith'];

    if ($productEAN === '') {
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


    function getProductId ($pdo, $productName) {
        $id = NULL;
        $sql = 'SELECT product_id FROM products WHERE product_name = :product_name';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['product_name' => $productName]);
        $row_count = $stmt->rowCount();
        $id = $stmt->fetchColumn();

        if ($row_count === 0) {
            $sql = 'INSERT INTO products (product_name, product_type, product_qa_status, account_id ) 
            VALUES (:product_name, "SKU", "active", :account_id)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_name' => $productName, 'account_id' => $_SESSION['id']]);

            $sql = 'SELECT product_id FROM products WHERE product_name = :product_name';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['product_name' => $productName]);
            $id = $stmt->fetchColumn();
        }
        return $id;
    }

    $productId = getProductId($pdo, $productName);
    $chainProductId = getProductId($pdo, $productChainName);


    $sql = 'SELECT product_ean_id FROM product_ean WHERE product_id = :product_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['product_id' => $productId]);
    $row_count = $stmt->rowCount();


    if ($row_count == 0) {
        $sql = 'INSERT INTO product_ean (product_id, product_ean, unmatch_reason_id, duplicate_product_name, account_id, product_item_code, additional_comment, matched_method, chain_product_id)
            VALUES (:product_id, :product_ean, :unmatch_id, :duplicate_product_name, :account_id, :item_code, :additional_comment, :match_with, :chain_id)';
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
                'chain_id' => $chainProductId
            ]
        );

    } else {
        $now = new DateTime();
        $datetime = $now->format('Y-m-d H:i:s');
        $sql = 'UPDATE product_ean pe SET pe.product_ean = :product_ean, pe.unmatch_reason_id = :unmatch_id, 
                          pe.duplicate_product_name = :duplicate_product_name, pe.ean_last_mod_account_id = :account_id, 
                          pe.ean_last_mod_datetime = :mod_datetime, pe.product_item_code = :item_code,
                          pe.additional_comment = :additional_comment , pe.matched_method = :match_with, 
                          pe.chain_product_id = :chain_id WHERE pe.product_id = :product_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(
            [
                'product_ean' => $productEAN,
                'unmatch_id' => $unmatchReasonId,
                'duplicate_product_name' => $duplicateProductName,
                'account_id' => $_SESSION['id'],
                'mod_datetime' => $datetime,
                'item_code' => $itemCode,
                'additional_comment' => $additionalComment,
                'match_with' => $matchWith,
                'chain_id' => $chainProductId,
                'product_id' => $productId,
            ]
        );
    }

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
} catch (PDOException $e) {
    $warning = $e->getMessage();
}
$return_arr[] = array('error' => $warning);
echo json_encode($return_arr);


