<?php
/*
    Filename: probe_hunt.php
    Author: Malika Liyanage
*/

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
	header('Location: login_auth_one.php');
	exit();
} else {
    if(!($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor' || $_SESSION['role'] === 'SRT')){
        header('Location: index.php');
	    exit();
    }
}

// unset the variable out from session. out is used to store error messages from details.php
if(isset($_SESSION['out'])){
    unset($_SESSION['out']);
}

// Current settings to connect to the user account database
require('user_db_connection.php');
$dbname = 'project_db';
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


$sql = 'SELECT project_name, project_region, project_db_name FROM projects';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$project_rows = $stmt->fetchAll(PDO::FETCH_OBJ);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='icon' href='favicon.ico' type='image/x-icon' />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script
			  src="https://code.jquery.com/jquery-3.4.1.min.js"
			  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
			  crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="styles/main.css" />
    <link rel="stylesheet" type="text/css" href="styles/probe_hunt.css" />
    <link rel="stylesheet" type="text/css" href="styles/ref_hunt.css" />
    <script src="scripts/transition.js"></script>
    <script src="scripts/validate_ref_hunt.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <!-- Prerenders font awesome-->
    <script type="text/javascript"> (function() { var css = document.createElement('link'); css.href = 'https://use.fontawesome.com/releases/v5.10.0/css/all.css'; css.rel = 'stylesheet'; css.type = 'text/css'; document.getElementsByTagName('head')[0].appendChild(css); })(); </script>
    <title>Product Hunt</title>
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
                <a class="nav-link" href="details.php"><span class="fas fa-user-cog"> Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php"><span class="fas fa-sign-out-alt"> Logout</a>
            </li>
        </ul>
    </div>
</nav>
<div id="probe_hunt_section">
<div class="row">
    <div class="col">
        <label for="project_name">Select Project Name</label>
        <select name="project_name" id="project_name" class="form-control">
        <option value=""selected disabled>Select</option>
    <?php
    foreach($project_rows as $project_row){
        echo "<option value=\"$project_row->project_db_name\">$project_row->project_name ($project_row->project_region)</option>";
    }
    ?>
        </select>
        <span id="project_name_error" class="error-popup"></span>    
    </div>
</div>
<div class="row">
    <div  id="ref_hunt_counter" class="col hide">
	    <div class="row text-center">
	        <div class="col">
	            <div class="counter">
                  <i class="fas fa-clipboard-list fa-2x"></i>
                  <h2 id="current_ref_count" class="timer count-title count-number">
                    <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </h2>
                    <p class="count-text ">Unassigned References</p>
                </div>
            </div>
            <div class="col">
	            <div class="counter">
                  <i class="far fa-lightbulb fa-2x"></i>
                  <h2 id="current_ref_handle_count" class="timer count-title count-number">
                    <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </h2>
                    <p class="count-text ">References Currently Being Handled</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div id="ref_hunt_options" class="col hide">
    <div class="col">
        <label for="brand_name_filter">Filter by Brand Name</label>
        <select name="brand_name_filter" id="brand_name_filter" class="form-control">
        </select>
    </div>
    <div class="col">
        <button type="button" id="ref_brand_button" class="btn qa_button" onclick="get_ref_info();">
        <div class="counter">
            <i class="fas fa-boxes fa-2x"></i>
            <h2 id="current_brand_ref_count" class="timer count-title count-number">
                <div class="spinner-border text-success" role="status">
                <span class="sr-only">Loading...</span>
                </div>
            </h2>
            <p class="count-text ">Remaining References For Brand</p>
        </div>
        </button>
    </div>
    <button class="btn" id="exit_btn" onclick="window.location.href='product_hunt.php'"><i class="fas fa-chevron-circle-left fa-3x"></i><br>Exit</i></button>
    <p class="error-popup" id="probe_message"></p>
    </div>
</div>
</div>
<div class="modal hide fade modal-form" id="add_reference" tabindex="-1" role="dialog"
    aria-labelledby="add_reference_title" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_reference_title"></h5>
                <button type="button" class="close hide" id="close_probe_title" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" value="Submit" onclick="validate_probe_submission();" id="submit_probe">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>