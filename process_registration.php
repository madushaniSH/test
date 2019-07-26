<?php
/*
    Filename: add_new_user.php
    Author: Malika Liyanage
    Created: 17/07/2019
    Purpose: Used for server-side validation of the user input. If it is valid adds it to the db
*/

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
	header('Location: login_auth_one.php');
	exit();
}else{
    if(!($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor')){
        header('Location: index.php');
	    exit();
    }
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
$success_message = '';
// Validating passed first name
$first_name = trim($_POST['first_name']);
if($first_name != ''){
    if(!preg_match("/^(?=.{1,50}$)[a-z]+(?:['_.\s][a-z]+)*$/i", $first_name)){
        $error_message .= "<p><strong><em>First Name</em></strong> is invalid";
    }
}else{
    $error_message .= '<p><strong><em>First Name</em></strong> cannot be empty';
}

// Validating passed last name
$last_name = trim($_POST['last_name']);
if($last_name != ''){
    if(!preg_match("/^(?=.{1,50}$)[a-z]+(?:['_.\s][a-z]+)*$/i", $last_name)){
        $error_message .= "<p><strong><em>Last Name</em></strong> is invalid";
    }
}else{
    $error_message .= '<p><strong><em>Last Name</em></strong> cannot be empty</p>';
}

// Validating passed GID
$gid = trim($_POST['gid']);
if($gid != ''){
    if($gid[0] != 'G'){
        $error_message .= '<p><strong><em>GID</em></strong> must begin with G';
    }
}else{
    $error_message .= '<p><strong><em>GID</em></strong> cannot be empty</p>';
}

// Validating NIC
$nic = trim($_POST['nic']);
if($nic == ''){
    $error_message .= '<p><strong><em>NIC</em></strong> cannot be empty';
}

// Validating username email address
$username = strtolower(trim($_POST['username']));
if($username != ''){
    if(!filter_var($username, FILTER_VALIDATE_EMAIL)){
        $error_message .= '<p><strong><em>Work Email</em></strong> entered is not a valid email';
    }
}else{
    $error_message .= '<p><strong><em>Work Email</em></strong> cannot be left empty';
}

// Validating password
$password = $_POST['pwd'];
if($password != ''){
    if (!(strlen($password) >= 6)){
        $error_message .= '<p><strong><em>Password</em></strong> must be at least 6 characters';
    }
}else {
    $error_message .= '<p><strong><em>Password</em></strong> cannot be empty';
}

// validating confirm password
$confirm_password = $_POST['confirm_pwd'];
if($confirm_password != ''){
    if (!(strlen($confirm_password) >= 6)){
        $error_message .= '<p><strong><em>Confirm Password</em></strong> musta be at least 6 characters';
    }
}else{
    $error_message .= '<p><strong><em>Confirm Password</em></strong> cannot be empty';
}

// Checks if the passwords are not empty and match each other
if($confirm_password != '' && $password != '' && $confirm_password != $password){
    $error_message .= '<p><strong><em>Both Passwords</em></strong> must match';
}

if($error_message == ''){
    $sql = "SELECT account_gid FROM accounts WHERE account_gid = :gid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['gid'=>$gid]);
    $row_count = $stmt->rowCount(PDO::FETCH_OBJ);

    if($row_count != 0){
        $error_message .= '<p>GID : <strong><em>'.$gid.'</strong></em> already exists in the database</p>';
    }else{
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO accounts (account_gid, account_nic, account_email, account_password, account_first_name, account_last_name) VALUES (:gid, :nic, :email, :user_password, :first_name, :last_name)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['gid'=>$gid, 'nic'=>$nic, 'email'=>$username, 'user_password'=>$hashed_password, 'first_name'=>$first_name, 'last_name'=>$last_name]);
        $success_message .= '<p class='.'"alert alert-success text-center"'.'>Account Successfully Created</p>';
    }
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
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="scripts/transition.js"></script>
    <link rel="stylesheet" type="text/css" href="styles/main.css" />
    <title>Processing Registration</title>
</head>
<body class="register-page">
<svg id="fader"></svg>
<nav class="navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="index.php" class="navbar-brand">Data Operations</a>
        </div>
    </div>
</nav>
<div class="confirm-message">
    <h1 class="text-center">User Registration</h1>
<?php
if($error_message != ''){
    $error_message .= "<br><button type=\"button\" class=\"btn btn-dark\" onclick=\"window.location.href='add_new_user.php'\">Back to Registration</button><button type=\"button\" class=\"btn btn-dark\"  onclick=\"window.location.href='index.php'\">Back to Dashboard</button>";
    echo "$error_message";
}
if($success_message != ''){
    $success_message .= "<br><button type=\"button\" class=\"btn btn-dark\" onclick=\"window.location.href='add_new_user.php'\">Back to Registration</button><button type=\"button\" class=\"btn btn-dark\"  onclick=\"window.location.href='index.php'\">Back to Dashboard</button>";
    echo $success_message;
}
?>
</div>
</body>
</html>