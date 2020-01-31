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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="styles/main.css" />
    <link rel="stylesheet" type="text/css" href="styles/loginnew.css" />
    <link rel='icon' href='favicon.ico' type='image/x-icon' />
    <script src="scripts/transition.js"></script>
    <title>Data Operations Department Login</title>
</head>

<body>
    <svg id="fader"></svg>
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <a href="index.php" class="navbar-brand">Data Operations</a>
            </div>
        </div>
    </nav>
    <div class="login-form">
        <form action="login_auth_three.php" method="POST" novalidate>
            <h1 class="text-center">Login</h1>
            <div class="form-group">

        <label class="lf--label" for="username">
        <svg x="0px" y="0px" width="12px" height="13px">
        <path fill="#B1B7C4" d="M8.9,7.2C9,6.9,9,6.7,9,6.5v-4C9,1.1,7.9,0,6.5,0h-1C4.1,0,3,1.1,3,2.5v4c0,0.2,0,0.4,0.1,0.7 C1.3,7.8,0,9.5,0,11.5V13h12v-1.5C12,9.5,10.7,7.8,8.9,7.2z M4,2.5C4,1.7,4.7,1,5.5,1h1C7.3,1,8,1.7,8,2.5v4c0,0.2,0,0.4-0.1,0.6 l0.1,0L7.9,7.3C7.6,7.8,7.1,8.2,6.5,8.2h-1c-0.6,0-1.1-0.4-1.4-0.9L4.1,7.1l0.1,0C4,6.9,4,6.7,4,6.5V2.5z M11,12H1v-0.5 c0-1.6,1-2.9,2.4-3.4c0.5,0.7,1.2,1.1,2.1,1.1h1c0.8,0,1.6-0.4,2.1-1.1C10,8.5,11,9.9,11,11.5V12z"/>
        </svg>
        </label> 
        <input type="text" class="form-control" placeholder="Username" required="required" id="username"
                    name="username">
        </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="Password" required="required" id="Password"
                    name="password">
            </div>
<?php
if(isset($error_message)){
    echo "<p class=\"alert alert-danger\">$error_message</p>";
}
?>
            <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">Log In</button>
        </div>
        <div class="form-group">
            <a href="forgot_password.php" class="text-center">Forgot Password?</a>
        </div>
        </form>
    </div>
</body>

</html>