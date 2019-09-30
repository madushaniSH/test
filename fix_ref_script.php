<?php
/*
    Filename: assign_qa_product.php
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
$dbname = 'project_db';
$_SESSION['current_database'] = $dbname; 
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
$sql = 'SELECT project_db_name FROM projects WHERE 1';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$project_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
for ($i = 0; $i < count($project_info); $i++) {
    $dbname = $project_info[$i]["project_db_name"];
    $dsn = 'mysql:host='.$host.';dbname='.$dbname;
    $pdo = new PDO($dsn, $user, $pwd);
    echo "<p>".$dbname."</p>";
    try {
        $sql = 'DROP TABLE IF EXISTS '.$dbname.'.`ref_product_info`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(); 
        $sql = 'DROP TABLE IF EXISTS '.$dbname.'.`reference_queue`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(); 
        $sql = 'DROP TABLE IF EXISTS '.$dbname.'.`reference_info`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(); 
    $sql = 'CREATE TABLE IF NOT EXISTS `reference_info` (
      `reference_info_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
      `reference_recognition_level` varchar(255) DEFAULT NULL,
      `reference_ean` varchar(255) DEFAULT NULL,
      `reference_short_name` varchar(255) DEFAULT NULL,
      `reference_category` varchar(255) DEFAULT NULL,
      `reference_sub_category` varchar(255) DEFAULT NULL,
      `reference_brand` varchar(255) DEFAULT NULL,
      `reference_sub_brand` varchar(255) DEFAULT NULL,
      `reference_manufacturer` varchar(255) DEFAULT NULL,
      `reference_base_size` varchar(255) DEFAULT NULL,
      `reference_size` varchar(255) DEFAULT NULL,
      `reference_measurement_unit` varchar(255) DEFAULT NULL,
      `reference_container_type` varchar(255) DEFAULT NULL,
      `reference_agg_level` varchar(255) DEFAULT NULL,
      `reference_segment` varchar(255) DEFAULT NULL,
      `reference_count_upc2` varchar(255) DEFAULT NULL,
      `reference_flavor_detail` varchar(255) DEFAULT NULL,
      `reference_case_pack` varchar(255) DEFAULT NULL,
      `reference_multi_pack` varchar(255) DEFAULT NULL,
      `reference_added_date` datetime NOT NULL DEFAULT current_timestamp(),
      `reference_added_user_id` int(11) NOT NULL,
      `reference_hunter_processed_time` datetime DEFAULT NULL,
      `reference_process_comment` varchar(255) DEFAULT NULL,
      `reference_process_remark` varchar(255) DEFAULT NULL,
      `reference_processed_hunter_id` int(11) DEFAULT NULL,
      `reference_status_id` int(11) DEFAULT NULL,
      `reference_ticket_id` int(11) DEFAULT NULL,
       CONSTRAINT  '.$dbname.'_REF_ADDED_USER_ID FOREIGN KEY (`reference_added_user_id`) REFERENCES `user_db`.`accounts` (`account_id`),
       CONSTRAINT  '.$dbname.'_REF_PROCESSED_HUNTER_ID FOREIGN KEY (`reference_processed_hunter_id`) REFERENCES `user_db`.`accounts` (`account_id`),
       CONSTRAINT  '.$dbname.'_REF_STATUS_ID FOREIGN KEY (`reference_status_id`) REFERENCES `probe_status` (`probe_status_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(); 

    $sql = 'CREATE TABLE IF NOT EXISTS `reference_queue` (
      `reference_queue_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
      `reference_info_key_id` int(11) NOT NULL,
      `account_id` int(11) DEFAULT NULL,
      `reference_being_handled` tinyint(1) NOT NULL DEFAULT 0,
       CONSTRAINT '.$dbname.'_REFERENCE_QUEUE_ACCOUNT_ID FOREIGN KEY (`account_id`) REFERENCES `user_db`.`accounts` (`account_id`),
       CONSTRAINT '.$dbname.'_REFERENCE_QUEUE_REFERENCE_INFO_KEY_ID FOREIGN KEY (`reference_info_key_id`) REFERENCES `reference_info` (`reference_info_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(); 

    $sql = 'CREATE TABLE `ref_product_info` (
  `ref_product_info_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `reference_info_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  CONSTRAINT '.$dbname.'_REFERENCE_INFO_PRODUCT_ID FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  CONSTRAINT '.$dbname.'_REFERENCE_INFO_REF_ID FOREIGN KEY (`reference_info_id`) REFERENCES `reference_info` (`reference_info_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(); 
    } catch(PDOException $e) {
        echo $e->getMessage();//Remove or change message in production code
    }
}
?> 