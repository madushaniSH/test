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
  $user = 'root';
  $pwd = '$$ma12qwqwSr4';
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
    $sql = 'CREATE TABLE IF NOT EXISTS `product_client_category` (
    `product_client_category_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `product_id` int(11) NOT NULL,
    `client_category_id` int(11) NOT NULL,
    CONSTRAINT '.$dbname.'_PRODCUT_CLIENT_CAT_CLIENT_CAT_ID FOREIGN KEY (`client_category_id`) REFERENCES `client_category` (`client_category_id`),
    CONSTRAINT '.$dbname.'_PRODCUT_CLIENT_CAT_PRODUCT_ID FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);

    $stmt->execute();
      $sql = 'CREATE TABLE IF NOT EXISTS  `oda_queue` (
  `oda_queue_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `qa_being_handled` tinyint(1) NOT NULL DEFAULT \'0\'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();

      $sql = 'CREATE TABLE IF NOT EXISTS  `product_oda_errors` (
  `product_oda_error_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `error_id` int(11) NOT NULL,
   CONSTRAINT '.$dbname.'_ODA_ERROR_ID FOREIGN KEY (`error_id`) REFERENCES `project_errors` (`project_error_id`),
   CONSTRAINT '.$dbname.'_ODA_ERROR_PR_ID FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();

      $sql = 'ALTER TABLE products CHANGE product_qa_status product_qa_status enum(\'pending\', \'approved\', \'disapproved\', \'active\', \'rejected\')';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();

      $sql = 'ALTER TABLE products
        ADD `product_qa_previous` varchar(255) DEFAULT NULL,
        ADD `product_alt_design_qa_previous` varchar(255) DEFAULT NULL,
        ADD `product_oda_account_id` int(11) DEFAULT NULL,
        ADD `product_oda_datetime` datetime DEFAULT NULL,
        ADD CONSTRAINT '.$dbname.'_PRODUCT_ODA_ACCOUNT_ID FOREIGN KEY (`product_oda_account_id`) REFERENCES `user_db`.`accounts` (`account_id`)
       ';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
  }
?>