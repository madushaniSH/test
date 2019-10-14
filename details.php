<?php
/*
    Filename: details.php
    Author: Malika Liyanage
    Created: 18/07/2019
    Purpose: Displays users account details
*/

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
	header('Location: login_auth_one.php');
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

$sql = 'SELECT account_gid, account_nic, account_email, account_first_name, account_last_name, account_profile_picture_location FROM accounts WHERE account_id = :id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['id'=>$_SESSION['id']]);
$user_information = $stmt->fetch(PDO::FETCH_OBJ);
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
    <title>User Details</title>
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
<div class="details">
    <h1 class="text-center">Details</h1>
    <figure>
        <img src="<?php echo $user_information->account_profile_picture_location?>" alt="Avatar" class="avatar-image rounded-circle mx-auto d-block">
        <figcaption class="text-center"><?php echo $_SESSION['role']; ?></figcaption>
    </figure>
    <p class="text-center profile-upload-label">Update Profile Image</p>
<form action="upload.php" METHOD="POST" enctype="multipart/form-data" id="profile-pic-upload" class="text-center">
    <div class="form-row">
        <div class="form-group col-md-6">
            <input type="file" name="file_to_upload" id="file_to_upload" name="file_to_upload">
        </div>
        <div class="form-group col-md-6">
            <input type="submit" value="Upload" name="submit">
        </div>
    </div>
</form>
<?php
if(isset($_SESSION['out'])){
    $error_array = $_SESSION['out'];
    if(is_array($error_array['error'])){
        $message = '';
        echo "<p class=\"alert alert-danger text-center\">";
        foreach ($error_array['error'] as $msg){
            echo $msg.'<br>';
        }
        echo "</p>";
    }
}
echo "
    <p>Username   : $user_information->account_email</p>
    <p>GID        : $user_information->account_gid</p>
    <p>NIC        : $user_information->account_nic</p>
    <p>First name : $user_information->account_first_name</p>
    <p>Last name  : $user_information->account_last_name</p>
";
?>
<button type="button" class="btn btn-dark"  onclick="window.location.href='index.php'">Back to Dashboard</button>
</div>
</body>
</html>