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
        $sql = 'DROP TABLE IF EXISTS product_ean';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = 'CREATE TABLE `product_ean` (
        `product_ean_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `product_id` int(11) NOT NULL,
        `product_ean` VARCHAR(20) DEFAULT NULL,
        `product_item_code` VARCHAR(24) DEFAULT NULL,
        `additional_comment` varchar(500) DEFAULT NULL,
        `account_id` int(11) default null,
        `ean_creation_time` datetime NOT NULL DEFAULT current_timestamp(),
        `ean_last_mod_datetime` DATETIME NULL DEFAULT NULL,
        `ean_last_mod_account_id` int(11) default null,
        `unmatch_reason_id` int(11) DEFAULT  NULL,
        `duplicate_product_name` varchar(500) DEFAULT NULL,
        CONSTRAINT ' . $dbname . '_ODA_EAN_PRODUCT_ID FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
        CONSTRAINT ' . $dbname . '_ODA_EAN_UNMATCH_REASON_ID FOREIGN KEY (`unmatch_reason_id`) REFERENCES `unmatch_reasons` (`unmatch_reason_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
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

