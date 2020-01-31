<?php
/*
    Filename : Login/login_auth_new4.php
    Author: Malika Liyanage
    Created: 31/01/2020
    Purpose: Part of the login authentication system, checks if a user account under the entered username exists
    if it exists redirects to login_auth_two.php else throws an error message

*/
session_start();
// Current settings to connect to the user account database
require('user_db_connection.php');

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

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    require('sanitise.php');
    // check username is empty
    if($_POST['username'] != ''){
        // check password is empty
        if($_POST['password'] != ''){
          
           // Preparing SQL Statement
        $sql = 'SELECT * FROM accounts WHERE account_email = :username';
        $stmt = $pdo->prepare($sql);
        // executing SQL statement
        $stmt->execute(['username'=>$_POST['username']]);
        // gets the number of row returned from the SQL query
        $row_count = $stmt->rowCount(PDO::FETCH_OBJ);
        // check username match
        if($row_count == 1){
            $user_information = $stmt->fetch(PDO::FETCH_OBJ);
            session_regenerate_id();
            $_SESSION['username'] = $_POST['username'];
            $_SESSION['id'] = $user_information->account_id;
            $_SESSION['first_name'] = $user_information->account_first_name;
        
        //check password match
        if (password_verify($_POST['password'], $user_information->account_password)){
            session_regenerate_id();
            $_SESSION['logged_in'] = TRUE;
            $sql = 'UPDATE accounts SET account_latest_login_date_time = CURRENT_TIMESTAMP(), account_current_active = 1 WHERE account_id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id'=>$_SESSION['id']]);


            $sql = 'SELECT designations.designation_name FROM designations
                    INNER JOIN account_designations
                    ON designations.designation_id = account_designations.designation_id
                    WHERE account_designations.account_id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id'=>$_SESSION['id']]);
            $user_information = $stmt->fetch(PDO::FETCH_OBJ);

            $_SESSION['role'] = $user_information->designation_name;
            header("location: ../index.php");
          ///unmatch password
          }else{
            $error_message='The password you have entered is incorrect';
            
          } 
          ///unmatch username
      }else{
            $error_message='The username you have entered is incorrect';
            
          }
          ///password empty
      } else{
            $error_message='Password cannot left blank field';
            
          }
          ///username empty
      }else{
            $error_message='Username cannot left blank field';
          }
           
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="loginnew.css" />
    <link rel='icon' href='favicon.ico' type='image/x-icon' />
    <script src="transition.js"></script>
    <title>Data Operations Login</title>
  
  <title></title>
</head>
<body>
<svg id="fader"></svg>
<img src="images/logo.jpg" alt="Italian Trulli">
<br><br>

<div class='login-form'>

<form action="login_auth_new4.php" method="POST" novalidate>
  <div class="flex-row">
    
    <label class="lf--label" for="username">
      <svg x="0px" y="0px" width="12px" height="13px">
        <path fill="#B1B7C4" d="M8.9,7.2C9,6.9,9,6.7,9,6.5v-4C9,1.1,7.9,0,6.5,0h-1C4.1,0,3,1.1,3,2.5v4c0,0.2,0,0.4,0.1,0.7 C1.3,7.8,0,9.5,0,11.5V13h12v-1.5C12,9.5,10.7,7.8,8.9,7.2z M4,2.5C4,1.7,4.7,1,5.5,1h1C7.3,1,8,1.7,8,2.5v4c0,0.2,0,0.4-0.1,0.6 l0.1,0L7.9,7.3C7.6,7.8,7.1,8.2,6.5,8.2h-1c-0.6,0-1.1-0.4-1.4-0.9L4.1,7.1l0.1,0C4,6.9,4,6.7,4,6.5V2.5z M11,12H1v-0.5 c0-1.6,1-2.9,2.4-3.4c0.5,0.7,1.2,1.1,2.1,1.1h1c0.8,0,1.6-0.4,2.1-1.1C10,8.5,11,9.9,11,11.5V12z"/>
      </svg>
    </label>
    <input id="username" class='lf--input' placeholder='Username' type='text' name="username">
  </div>
  <div class="flex-row">
    <label class="lf--label" for="password">
      <svg x="0px" y="0px" width="15px" height="5px">
        <g>
          <path fill="#B1B7C4" d="M6,2L6,2c0-1.1-1-2-2.1-2H2.1C1,0,0,0.9,0,2.1v0.8C0,4.1,1,5,2.1,5h1.7C5,5,6,4.1,6,2.9V3h5v1h1V3h1v2h1V3h1 V2H6z M5.1,2.9c0,0.7-0.6,1.2-1.3,1.2H2.1c-0.7,0-1.3-0.6-1.3-1.2V2.1c0-0.7,0.6-1.2,1.3-1.2h1.7c0.7,0,1.3,0.6,1.3,1.2V2.9z"/>
        </g>
      </svg>
    </label>
    <input id="password" class='lf--input' placeholder='Password' type='password' name="password">
  </div>

  <?php
if(isset($error_message)){
    echo "<p class=\"alert alert-danger\">$error_message</p>";
}
?>
  <input class='lf--submit' type='submit' value='LOGIN'>
</form>
<div>
<a class='lf--forgot' href="/vendor/forgot_password.php">Forgot password?</a>
</body>
</html>


