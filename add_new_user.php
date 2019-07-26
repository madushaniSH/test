<?php
/*
    Filename: add_new_user.php
    Author: Malika Liyanage
    Created: 17/07/2019
    Purpose: Form for adding new users
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
    <script src="scripts/user_registration_validate.js"></script>
    <script src="scripts/transition.js"></script>
    <link rel="stylesheet" type="text/css" href="styles/main.css" />
    <title>Add New User</title>
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
<form action="process_registration.php" method="POST" id="register-form">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" placeholder="First Name" id="first_name" name="first_name">
            <span id="first_name_error" class="error-popup"></span>    
        </div>
        <div class="form-group col-md-6">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" placeholder="Last Name" id="last_name" name="last_name">
            <span id="last_name_error" class="error-popup"></span>    
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-4">
            <lablel for="gid">Enter GID</lablel>
            <input type="text" class="form-control" id="gid" placeholder="GID" name="gid">
            <span id="gid_error" class="error-popup"></span>    
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="nic">Enter NIC Number</label>
            <input type="text" class="form-control" placeholder="NIC" id="nic" name="nic">
            <span id="nic_error" class="error-popup"></span>    
        </div>
    </div>
    <div class="form-group">
        <lablel for="username">Enter work email address (this will be used as your username)</lablel>
        <input type="email" class="form-control" id="username" placeholder="Work Email" name="username">
        <span id="username_error" class="error-popup"></span>    
    </div>
    <div class="form-row">
        <div class="form-group col-md-4">
            <lablel for="pwd">Enter Password</lablel>
            <input type="password" class="form-control" id="pwd" placeholder="Password" name="pwd">
            <span id="password_error" class="error-popup"></span>
        </div>
        <div class="form-group col-md-4">
            <lablel for="confirm_pwd">Confirm Password</lablel>
            <input type="password" class="form-control" id="confirm_pwd" placeholder="Confirm Password" name="confirm_pwd">
            <span id="confirm_password_error" class="error-popup"></span>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
    <a href="index.php">Back to Dashboard</a>
</form>
</body>
</html>