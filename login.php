<?php
/*
    Filename : login_auth_one.php
    Author: Malika Liyanage
    Created: 15/07/2019
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
            $user_information = $stmt->Fetch(PDO::FETCH_OBJ);
            session_regenerate_id();
            $_SESSION['username']=$_POST['username'];
            $_SESSION['id']=$user_information->account_id;
            $_SESSION['username'] = $user_information->account_first_name;
        
        //check password match
        if (password_verify($_POST['password'], $user_information->account_password)){
            session_regenerate_id();
            $_SESSION['logged_in']=TRUE;
            $sql = 'UPDATE accounts SET account_latest_login_date_time = CURRENT_TIMESTAMP(), account_current_active=1 WHERE account_id = :id';
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
            header("location: index.php");
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