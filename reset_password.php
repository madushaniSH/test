<?php
/*
    Filename: reset_password.php
    Author: Malika Liyanage
    Created: 16/07/2019
    Purpose: Takes the user to a password reset screen
*/
session_start();
// Redirects the user if the username was not set
if(!isset($_SESSION['id'])){
    header("location: login_auth_one.php");
    exit();
}

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

$error_message = '';
$confirm_message = '';
if(!empty($_GET['fp_code'])){
    $fp_code = $_GET['fp_code'];
    $sql = 'SELECT account_id, account_password, account_password_reset_request, account_password_reset_identity FROM accounts WHERE account_password_reset_identity = :reset_identity';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['reset_identity'=>$fp_code]);
    $row_count = $stmt->rowCount(PDO::FETCH_OBJ);

    if($row_count > 0){
        $user_information = $stmt->fetch(PDO::FETCH_OBJ);
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(!empty($_POST['new_password']) && !empty($_POST['confirm_new_password'])){
                if($_POST['new_password'] == $_POST['confirm_new_password']){
                    if((!(strlen($_POST['new_password']) >= 6)) && (!(strlen($_POST['confirm_new_password']) >= 6))){
                        $error_message = 'Passwords must be more than 6 characters in length';
                    }else{
                        $hashed_new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                        $sql = "UPDATE accounts SET account_password = :new_password, account_password_reset_request = :new_request, account_password_reset_identity = :new_identity WHERE account_id = :id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(['new_password'=>$hashed_new_password, 'new_request'=>0, 'new_identity'=>NULL, 'id'=>$user_information->account_id]);
                        $error_message = '';
                        $confirm_message = 'Password successfully reset';
                    }
                }else{
                    $error_message = 'Passwords must match';
                }
            }else{
                $error_message = 'All fields are mandatory';
            }
        }
    }else{
       header('location: login_auth_one.php');
    }
}else{
    header('location: login_auth_one.php');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='icon' href='favicon.ico' type='image/x-icon' />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="styles/main.css" />
    <link rel="stylesheet" type="text/css" href="styles/login.css" />
    <script src="scripts/transition.js"></script>
    <title>Reset Password</title>
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
<?php
if(!empty($_GET['fp_code'])){
    echo "
        <form action=\"reset_password.php?fp_code=$fp_code\" method=\"POST\">
    ";
}else{
    echo "
        <form action=\"reset_password.php\" method=\"POST\">
    ";
}
?>
        <h1 class="text-center">Reset Your Account Password</h1>
<?php
if($confirm_message == ''){
    echo "
        <div class=\"form-group\">
            <input type=\"password\" class=\"form-control\" placeholder=\"New Password\" required=\"required\" id=\"new_password\" name=\"new_password\">
        </div>
         <div class=\"form-group\">
            <input type=\"password\" class=\"form-control\" placeholder=\"Confirm New Password\" required=\"required\" id=\"confirm_new_password\" name=\"confirm_new_password\">
        </div>
    ";
    if($error_message != ''){
        echo "<p class=\"alert alert-danger\">$error_message</p>";
    }

    echo "
        <div class=\"form-group\">
            <button type=\"submit\" class=\"btn btn-primary btn-block\">Confirm</button>
        </div>
    ";
}else{
    echo "<p class=\"alert alert-success text-center\">$confirm_message</p>";
    echo "<a class=\"text-center\" href=\"login_auth_one.php\">Back to login page</a>";
}
?>
         
    </form>
</div>
</body>
</html>