<?php
set_time_limit(0);
// Current settings to connect to the user account database
require('./user_db_connection.php');
$dbname = 'project_db';
// Setting up the DSN
$dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
/*
    Attempts to connect to the databse, if no connection was estabishled
    kills the script
*/
$user = 'root';
$pwd = '$$ma12qwqwSr4';
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
$sql = 'SELECT project_db_name FROM projects WHERE 1';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$project_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
for ($i = 0; $i < count($project_info); $i++) {
    $dbname = $project_info[$i]["project_db_name"];
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
    $pdo = new PDO($dsn, $user, $pwd);
    $e = '';
    try {
        $sql = 'ALTER TABLE `probe_queue` ADD `assign_datetime` DATETIME NULL DEFAULT NULL AFTER `probe_being_handled`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = 'ALTER TABLE `probe` ADD `probe_start_datetime` DATETIME NULL DEFAULT NULL AFTER `probe_added_user_id`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = 'ALTER TABLE `radar_queue` ADD `assign_datetime` DATETIME NULL DEFAULT NULL AFTER `account_id`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = 'ALTER TABLE `radar_hunt` ADD `radar_start_datetime` DATETIME NULL DEFAULT NULL AFTER `radar_hunter_id`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = 'ALTER TABLE `reference_queue` ADD `assign_datetime` DATETIME NULL DEFAULT NULL AFTER `reference_being_handled`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = 'ALTER TABLE `reference_info` ADD `ref_start_datetime` DATETIME NULL DEFAULT NULL AFTER `reference_added_user_id`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = 'ALTER TABLE `probe_qa_queue` ADD `assign_datetime` DATETIME NULL DEFAULT NULL AFTER `account_id`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = 'ALTER TABLE `products` ADD `qa_start_datetime` DATETIME NULL DEFAULT NULL AFTER `product_qa_account_id`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = 'ALTER TABLE `products` ADD `oda_start_datetime` DATETIME NULL DEFAULT NULL AFTER `product_oda_account_id`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = 'ALTER TABLE `product_ean_queue` ADD `assign_datetime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `product_being_handled`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = 'ALTER TABLE `product_ean` ADD `ean_assign_datetime` DATETIME NULL DEFAULT NULL AFTER `account_id`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

    } catch (PDOException $e) {
        $e->getMessage();
    }
    $tot = $i + 1;
    if ($e !== '') {
        echo "Project Name: " . $dbname . " error. " . $e;
    } else {
        echo "Project Name: " . $dbname . " done. " . $tot ."/".count($project_info)."\n";
    }
}

