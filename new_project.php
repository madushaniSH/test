<?php
/*
    Filename: new_project.php
    Author: Malika Liyanage
*/

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
	header('Location: login_auth_one.php');
	exit();
} else {
    if(!($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor')){
        header('Location: index.php');
	    exit();
    }
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
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="styles/main.css" />
    <link rel="stylesheet" type="text/css" href="styles/new_project.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>
    <script src="scripts/transition.js"></script>
    <script src="scripts/validate_new_project.js"></script>
    <script>
    jQuery(document).ready(function () {
        jQuery('#project_region').select2({
            width: '100%'
        });
    });
    </script>
    <!-- Prerenders font awesome-->
    <script type="text/javascript"> (function() { var css = document.createElement('link'); css.href = 'https://use.fontawesome.com/releases/v5.10.0/css/all.css'; css.rel = 'stylesheet'; css.type = 'text/css'; document.getElementsByTagName('head')[0].appendChild(css); })(); </script>
    <title>New Project</title>
</head>
<body>
<svg id="fader"></svg>
<nav class="navbar navbar-expand-md">
    <a href="product_hunt.php" class="btn btn-light nav-back"><i class="fas fa-arrow-circle-left"></i></a>
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
<form method="POST" id="create-project-form">
    <div class="form-row">
        <img src="images/sisters-of-battle.svg" class="form-thumbnail" alt="">
    </div>
    <div class="form-row">
        <div class="form-group col">
            <label for="project_name">*Project Name</label>
            <input type="text" class="form-control" id="project_name" placeholder="Project Name" name="project_name">
            <span id="project_name_error" class="error-popup"></span>    
            <div class="server-results"></div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <label for="project_region">*Project Region</label>
            <select name="project_region" id="project_region" class="form-control">
            <option value=""selected disabled>Select</option>
            <option value="APAC">APAC</option>
            <option value="EMEA">EMEA</option>
            <option value="DPG">DPG</option>
            <option value="AMER">AMER</option>
            </select>
            <span id="project_region_error" class="error-popup"></span>    
        </div>
    </div>
    <button type="button" class="btn btn-success" onclick="validate_project_form();">Submit</button>
    <a href="product_hunt.php" class="btn btn-outline-dark">Back to Product Hunt</a>
</form>
</body>