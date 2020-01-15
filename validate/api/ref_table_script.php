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
    try {
        $sql = 'DROP TABLE IF EXISTS product_ean_queue';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = 'CREATE TABLE `product_ean_queue` (
        `product_ean_queue_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `product_id` int(11) NOT NULL,
        `account_id` int(11) DEFAULT NULL,
        `product_being_handled` tinyint(1) NOT NULL DEFAULT 0,
        CONSTRAINT ' . $dbname . '_ODA_EAN_QUEUE_PRODUCT_ID FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = 'DROP TABLE IF EXISTS product_ean';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = 'CREATE TABLE `product_ean` (
        `product_ean_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `product_name` varchar(250) NOT NULL,
        `product_ean` int(24) DEFAULT NULL,
        `product_item_code` int(24) DEFAULT NULL,
        `additional_comment` varchar(500) DEFAULT NULL,
        `account_id` int(11) default null,
        `ean_creation_time` datetime NOT NULL DEFAULT current_timestamp(),
        `ean_last_mod_datetime` DATETIME NULL DEFAULT NULL,
        `ean_last_mod_account_id` int(11) default null
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = 'DROP TABLE IF EXISTS unmatch_reasons';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = 'CREATE TABLE `unmatch_reasons` (
        `unmatch_reason_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `unmatch_reason` varchar(255) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sql = "INSERT INTO `unmatch_reasons` (`unmatch_reason`) VALUES
            (\"Size doesn't match\"),
            (\"Count doesn't match\"),
            (\"Description doesn't match\"),
            (\"Brand not in this category ref. file\"),
            (\"Flavour/Scent doesn't match\"),
            (\"Sub category doesn't match\"),
            (\"Container type doesn't match\"),
            (\"Life stage doesn't match\"),
            (\"Inch size doesn't match\"),
            (\"Form doesn't match\"),
            (\"SPF value doesn't match\"),
            (\"Sub brand doesn't match\"),
            (\"Breed size doesn't match\"),
            (\"Substiute\"),
            (\"Duplicate\"),
            (\"DVC Duplicate\")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    } catch (PDOException $e) {
        $e->getMessage();
    }
    $tot = $i + 1;
    echo "Project Name: " . $dbname . " " . $tot ."/".count($project_info)."\n";
}

