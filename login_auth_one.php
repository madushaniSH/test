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
    if($_POST['username'] != ''){
        // Preparing SQL Statement
        $sql = 'SELECT account_id, account_first_name FROM accounts WHERE account_email = :username';
        $stmt = $pdo->prepare($sql);
        // executing SQL statement
        $stmt->execute(['username'=>$_POST['username']]);
        // gets the number of row returned from the SQL query
        $row_count = $stmt->rowCount(PDO::FETCH_OBJ);

        if($row_count == 1){
            $user_information = $stmt->fetch(PDO::FETCH_OBJ);
            session_regenerate_id();
            $_SESSION['username'] = $_POST['username'];
            $_SESSION['id'] = $user_information->account_id;
            $_SESSION['first_name'] = $user_information->account_first_name;
            header("location: login_auth_two.php");
        }else{
            $error_message = 'The username you have entered doesnot match any account';
        }
    }else{
        $error_message = 'Username cannot be left blank';
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
    <link rel="stylesheet" type="text/css" href="styles/login.css" />
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
        <form action="login_auth_one.php" method="POST" novalidate>
            <h1 class="text-center">Login</h1>
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Username" required="required" id="username"
                    name="username">
            </div>
<?php
if(isset($error_message)){
    echo "<p class=\"alert alert-danger\">$error_message</p>";
}
?>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Next</button>
            </div>
        </form>
    </div>
</body>

</html>