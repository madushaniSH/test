<?php
    /*
        Filename : login_auth_two.php
        Author: Malika Liyanage
        Created: 15/07/2019
        Purpose: Part of the login authentication system, checks if entered password matches the passed username
        if it exists redirects to index.php else throws an error message
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

$sql = 'SELECT account_profile_picture_location FROM accounts WHERE account_id = :id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['id'=>$_SESSION['id']]);
$user_profile_image_information = $stmt->fetch(PDO::FETCH_OBJ);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if($_POST['password'] != ''){
        // Preparing SQL Statement
        $sql = 'SELECT account_password FROM accounts WHERE account_id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id'=>$_SESSION['id']]);
        $user_information = $stmt->fetch(PDO::FETCH_OBJ);
        
        // check if the hashed passwords match
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
        }else{
            $error_message = 'The password you have entered is incorrect';
        }
    }else{
        $error_message = 'Password cannot be left blank';
    }
}
   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="styles/main.css" />
    <link rel="stylesheet" type="text/css" href="styles/login.css" />
    <link rel='icon' href='favicon.ico' type='image/x-icon' />
    <script src="scripts/transition.js"></script>
    <title>Data Operations Login</title>
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
    <form action="login_auth_two.php" method="POST" novalidate>
<?php
echo "<h1 class=\"text-center\">Hello ".$_SESSION['first_name']." :)</h1>";
?>
        <img src="<?php echo $user_profile_image_information->account_profile_picture_location?>" alt="Avatar" class="avatar-image rounded-circle mx-auto d-block">
        <div class = "form-group text-center">
            <p>Type in your password to complete the sign in</p>
        </div>
        <div class="form-group">
            <input type="password" class="form-control" placeholder="Password" required="required" id="password" name="password">
        </div>
<?php
if(isset($error_message)){
    echo "<p class=\"alert alert-danger\">$error_message</p>";
}
?>
        <div class="form-group">
            <button type="submit" class="submit">Log In</button>
        </div>
        <div class="form-group">
            <a href="forgot_password.php" class="text-center">Forgot Password?</a>
        </div>
    </form>
</div>
</body>
</html>