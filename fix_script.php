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
        $sql = 'DROP TABLE IF EXISTS '.$dbname.'.`radar_sources`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(); 
        $sql = 'DROP TABLE IF EXISTS '.$dbname.'.`radar_queue`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(); 
        $sql = 'DROP TABLE IF EXISTS '.$dbname.'.`radar_hunt`';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(); 
        $sql = 'CREATE TABLE IF NOT EXISTS '.$dbname.'.`radar_hunt` (
      `radar_hunt_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
      `radar_category` varchar(255) NOT NULL,
      `radar_brand` varchar(255) NOT NULL,
      `radar_added_user_id` int(11) NOT NULL,
      `radar_added_date` datetime NOT NULL DEFAULT current_timestamp(),
       `radar_ticket_id` int(11) NOT NULL,
       CONSTRAINT '.$dbname.'_RADAR_ACCOUNT_ID FOREIGN KEY (`radar_added_user_id`) REFERENCES `user_db`.`accounts` (`account_id`),
       CONSTRAINT '.$dbname.'_RADAR_TICKET_ID FOREIGN KEY (`radar_ticket_id`) REFERENCES `project_tickets` (`project_ticket_system_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(); 

        $sql = 'CREATE TABLE IF NOT EXISTS '.$dbname.'.`radar_queue` (
      `radar_queue_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
      `radar_hunt_key_id` int(11) NOT NULL,
      `account_id` int(11) DEFAULT NULL,
      `radar_being_handled` tinyint(1) NOT NULL DEFAULT 0,
       CONSTRAINT '.$dbname.'_RADAR_QUEUE_ACCOUNT_ID FOREIGN KEY (`account_id`) REFERENCES `user_db`.`accounts` (`account_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(); 

    $sql = 'CREATE TABLE IF NOT EXISTS '.$dbname.'.`radar_sources` (
  `radar_source_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `radar_hunt_id` int(11) NOT NULL,
  `radar_status_id` int(11) NOT NULL,
  `radar_source_link` varchar(2083) NOT NULL,
  `radar_comment` varchar(255) DEFAULT NULL,
  `radar_product_id` int(11) DEFAULT NULL,
  `account_id` int(11) NOT NULL,
  `creation_time` datetime NOT NULL DEFAULT current_timestamp(),
 CONSTRAINT '.$dbname.'_RADAR_HUNT_ID FOREIGN KEY (`radar_hunt_id`) REFERENCES `radar_hunt` (`radar_hunt_id`),
 CONSTRAINT '.$dbname.'_RADAR_PRODUCT_ID FOREIGN KEY (`radar_product_id`) REFERENCES `products` (`product_id`),
 CONSTRAINT '.$dbname.'_RADAR_STATUS FOREIGN KEY (`radar_status_id`) REFERENCES `probe_status` (`probe_status_id`),
 CONSTRAINT '.$dbname.'_RADAR_ACCOUNT FOREIGN KEY (`account_id`) REFERENCES `user_db`.`accounts` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(); 
    } catch(PDOException $e) {
        echo $e->getMessage();//Remove or change message in production code
    }
    $sql = "ALTER TABLE `products` ADD `product_hunt_type` enum(\'probe\',\'radar\',\'reference\') NOT NULL DEFAULT \'probe\'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}
?>