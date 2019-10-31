<?php
/*
    Filename: index.php
    Author: Malika Liyanage
    Created: 16/07/2019
    Purpose: Main page of the website
*/

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
	header('Location: login_auth_one.php');
	exit();
}

    if($_SESSION['role'] === 'SRT' || $_SESSION['role'] === 'SRT Analyst'){
        header('Location: dashboard.php');
    }

    if ($_SESSION['role'] === 'ODA') {
        header('Location: oda_dashboard.php');
    }

// unset the variable out from session. out is used to store error messages from details.php
if(isset($_SESSION['out'])){
    unset($_SESSION['out']);
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
    <link rel="stylesheet" type="text/css" href="styles/main.css" />
    <script src="scripts/transition.js"></script>
    <!-- Prerenders font awesome-->
    <script type="text/javascript"> (function() { var css = document.createElement('link'); css.href = 'https://use.fontawesome.com/releases/v5.10.1/css/all.css'; css.rel = 'stylesheet'; css.type = 'text/css'; document.getElementsByTagName('head')[0].appendChild(css); })(); </script>
    <title>Landing Page</title>
</head>
<body>
<svg id="fader"></svg>
<nav class="navbar navbar-expand-md">
    <div class="mx-auto order-0">
        <a class="navbar-brand mx-auto" href="index.php">Data Operations</a>
    </div>
    <div class="navbar-collapse collapse w-100 order-3 dual-collapse2">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="details.php"><span class="fas fa-user-cog"> <?php echo $_SESSION["username"]?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php"><span class="fas fa-sign-out-alt"> Logout</a>
            </li>
        </ul>
    </div>
</nav>
<?php
if ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor' || $_SESSION['role'] === 'Training' || $_SESSION['role'] == 'ODA Supervisor') {
    echo "
<a href=\"products.php\" class=\"btn btn-lg dashboard-btn\">
    <span>
        <i class=\"fas fa-glass-martini-alt fa-2x\"></i>
    </span>
    Products and Brands
</a>";
}
if ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor' || $_SESSION['role'] === 'ODA Supervisor') {
    echo "
<a href=\"add_new_user.php\" class=\"btn btn-lg dashboard-btn\">
    <span>
        <i class=\"fas fa-user-plus fa-2x\"></i>
    </span>
    Add new user
</a>";
}
if($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor'){
    echo"
<a href=\"dashboard.php\" class=\"btn btn-lg dashboard-btn\">
    <span>
        <i class=\"fas fa-dragon fa-2x\"></i>
    </span>
    Product Hunt
</a>
";
}
if($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'ODA Supervisor'){
    echo "
<a href=\"oda_dashboard.php\" class=\"btn btn-lg dashboard-btn\">
    <span>
        <i class=\"fas fa-puzzle-piece fa-2x\"></i>
    </span>
    ODA
</a>
";
}
require ('footer.php');
?>
</body>
</html>