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
  $pwd = '$$Ma12qwqwsr4';
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
      $sql = "ALTER TABLE `products` ADD `product_submit_status` ENUM('resubmit','normal') NOT NULL DEFAULT 'normal'";
     $stmt = $pdo->prepare($sql);
     $stmt->execute();
  }
?>