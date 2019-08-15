<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
	header('Location: login_auth_one.php');
	exit();
}

// Current settings to connect to the user account database
require('product_connection.php');
$dbname = 'project_db';
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
    echo "<p>Connection to $dbname database failed<br>Reason: ".$e->getMessage().'</p>';
    exit();
}

$sql = 'SELECT project_id FROM projects WHERE project_name = :project_name';
$stmt = $pdo->prepare($sql);
$stmt->execute(['project_name'=>$_POST['project_name']]);
$row_count = $stmt->rowCount();

if ($row_count == 0) {
    $sql = 'INSERT INTO projects (project_name, project_region, project_db_name) VALUES (:project_name, :project_region, :project_db_name)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['project_name'=>$_POST['project_name'], 'project_region'=>$_POST['project_region'], 'project_db_name'=>$_POST['project_database']]);

    $sql = 'CREATE DATABASE IF NOT EXISTS '.$_POST['project_database'].' DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $stmt  = $pdo->prepare($sql);
    $stmt->execute();

    $pdo = NULL;
    $dbname = $_POST['project_database'];
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
        echo "<p>Connection to $dbname database failed<br>Reason: ".$e->getMessage().'</p>';
        exit();
    }

    $sql = 'CREATE TABLE `brand` (
        `brand_id` int(11) NOT NULL  PRIMARY KEY AUTO_INCREMENT,
        `brand_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `client_category` (
        `client_category_id` int(11) NOT NULL  PRIMARY KEY AUTO_INCREMENT,
        `client_category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `probe` (
        `probe_key_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `brand_id` int(11) DEFAULT NULL,
        `client_category_id` int(11) DEFAULT NULL,
        `probe_id` int(11) NOT NULL,
        `probe_added_date` datetime NOT NULL DEFAULT current_timestamp(),
        `probe_added_user_id` int(11) NOT NULL,
        CONSTRAINT '.$dbname.'_BRAND_ID FOREIGN KEY (`brand_id`) REFERENCES `brand` (`brand_id`),
        CONSTRAINT '.$dbname.'_CLIENT_CATEGORY_ID FOREIGN KEY (`client_category_id`) REFERENCES `client_category` (`client_category_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `probe_queue` (
        `probe_queue_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `probe_key_id` int(11) NOT NULL,
        `account_id` int(11) DEFAULT NULL,
        `probe_being_handled` tinyint(1) NOT NULL DEFAULT 0,
        `probe_processed` tinyint(1) NOT NULL DEFAULT 0,
         CONSTRAINT '.$dbname.'_ACCOUNT_ID FOREIGN KEY (`account_id`) REFERENCES `user_db`.`accounts` (`account_id`),
         CONSTRAINT '.$dbname.'_PROBE_KEY_ID FOREIGN KEY (`probe_key_id`) REFERENCES `probe` (`probe_key_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `probe_status` (
        `probe_status_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `probe_status_name` varchar(255) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'INSERT INTO `probe_status` (`probe_status_id`, `probe_status_name`) VALUES
    (1, \'Already Added\'),
    (2, \'Hunted\'),
    (3, \'Irrelevant\'),
    (4, \'Recognition Level Issue\'),
    (5, \'Validation Error\')';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `products` (
        `product_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `product_name` varchar(255) NOT NULL,
        `product_type` enum(\'brand\',\'sku\',\'dvc\') NOT NULL,
        `product_alt_design_name` varchar(255) DEFAULT NULL,
        `product_creation_time` datetime NOT NULL DEFAULT current_timestamp(),
        `account_id` int(11) NOT NULL,
         CONSTRAINT `PRODUCT_ACCOUNT_ID` FOREIGN KEY (`account_id`) REFERENCES `user_db`.`accounts` (`account_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    echo "<span class=\"success-popup\">Project Created</span>    ";
} else {
    echo "<span class=\"error-popup\">Project with that name already exists</span>    ";
}

?>