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
    <script src="scripts/transition.js"></script>
    <script src="scripts/validate_probe_hunt.js"></script>
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
<div class="row">
    <div  id="hunter_counter" class="col hide">
        <div class="downArrow bounce" id="arrow_sec">
            <a class="btn" id="show_button"><i class="fas fa-chevron-circle-down fa-3x"></i><br>Show Daily Count</a>
        </div>
	    <div class="row text-center hide" id="counters">
	        <div class="col">
	            <div class="counter">
                  <i class="fas far fa-copyright fa-2x green_icon"></i>
                  <h2 id="brand_count" class="timer count-title count-number">
                    <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </h2>
                    <p class="count-text ">Hunted Brand Count</p>
                </div>
            </div>
            <div class="col">
	            <div class="counter">
                  <i class="fas fa-boxes fa-2x green_icon"></i>
                  <h2 id="sku_count" class="timer count-title count-number">
                    <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </h2>
                    <p class="count-text ">Hunted SKU Count</p>
                </div>
            </div>
            <div class="col">
	            <div class="counter">
                  <i class="fas fa-eye fa-2x green_icon"></i>
                  <h2 id="dvc_count" class="timer count-title count-number">
                    <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </h2>
                    <p class="count-text ">Hunted DVC Count</p>
                </div>
            </div>
            <div class="col">
	            <div class="counter">
                  <i class="far fa-lightbulb fa-2x green_icon"></i>
                  <h2 id="facing_count" class="timer count-title count-number">
                    <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </h2>
                    <p class="count-text ">Hunted Facing Count</p>
                </div>
            </div>
            <div class="col">
	            <div class="counter">
                  <i class="fas fa-clipboard-list fa-2x green_icon"></i>
                  <h2 id="checked_probe_count" class="timer count-title count-number">
                    <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </h2>
                    <p class="count-text ">Checked Probe Count</p>
                </div>
            </div>
            <div class="col">
	            <div class="counter">
                  <i id ="qa_error" class="fas fa-skull-crossbones fa-2x"></i>
                  <h2 id="qa_error_count" class="timer count-title count-number">
                    <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </h2>
                    <p class="count-text ">QA Error Count</p>
                </div>
            </div>
            <div class="col">
	            <div class="counter">
                  <i id ="system_error" class="fa-2x fas fa-laptop-code"></i>
                  <h2 id="system_error_count" class="timer count-title count-number">
                    <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </h2>
                    <p class="count-text ">System Error Count</p>
                </div>
            </div>
        </div>
    </div>
</div>
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
<div id="ticket_section" class="hide">
<div class="row">
    <div class="col">
        <label for="ticket">Select Ticket ID</label>
        <select name="ticket" id="ticket" class="form-control">
        </select>
        <span id="ticket_error" class="error-popup"></span>    
    </div>
</div>
</div>
<div class="row">
    <div  id="probe_hunt_counter" class="col hide">
	    <div class="row text-center">
	        <div class="col">
	            <div class="counter">
                  <i class="fas fa-clipboard-list fa-2x"></i>
                  <h2 id="current_probe_count" class="timer count-title count-number">
                    <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </h2>
                    <p class="count-text ">Unassigned Probes</p>
                </div>
            </div>
            <div class="col">
	            <div class="counter">
                  <i class="far fa-lightbulb fa-2x"></i>
                  <h2 id="current_probe_handle_count" class="timer count-title count-number">
                    <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </h2>
                    <p class="count-text ">Probes Currently Being Handled</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div id="probe_hunt_options" class="col hide">
    <button class="btn" id="exit_btn" onclick="window.location.href='product_hunt.php'"><i class="fas fa-chevron-circle-left fa-3x"></i><br>Exit</i></button>
    <button class="btn hide" id="continue_btn" onclick="get_probe_info()"><i class="fas fa-chevron-circle-right fa-3x"></i><br>Continue</button>
    <p class="error-popup" id="probe_message"></p>
    </div>
</div>
</div>
<div class="modal hide fade modal-form" id="add_probe" tabindex="-1" role="dialog"
    aria-labelledby="add_probe_title" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_probe_title"></h5>
                <button type="button" class="close hide" id="close_probe_title" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form action="POST" id="probe_form">
            <div class="row">
                <div class="form-group col-md-5">
                    <label for="status">*Status:</label>
                    <select name="status" id="status" class="form-control">
                    </select>
                    <span id="status_error" class="error-popup"></span>
                </div>
                <div class="form-group col-md-7">
                    <label for="comment">Comment:</label>
                    <input type="text" id="comment" class="form-control">
                    <span id="comment_error" class="error-popup"></span>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-5">
                    <label for="remark">Remark:</label>
                    <input type="text" id="remark" class="form-control">
                    <span id="remark_error" class="error-popup"></span>
                </div>
                <div class="form-group col-md-5">
                    <button role="button" class="btn btn-outline-danger" onclick="return add_rec_comment();">Recongnition Issue Recongnized</button>
                    <button role="button" class="btn btn-outline-primary" onclick="return add_cant_find_comment();">Some Products Not Found</button>
                </div>
            </div>
            <div id="hunt_information" class="hide">
                <p class="border-bottom my-3">Additional Information</p>
                <div class="row">
                    <div class="form-group col-md-5">
                        <label for="product_name">*Product Name:</label>
                        <input type="text" id="product_name" class="form-control">
                        <span id="product_name_error" class="error-popup"></span>
                    </div>
                    <div class="form-group col-md-5">
                        <label for="product_type">*Product Type:</label>
                        <select name="product_type" id="product_type" class="form-control">
                            <option value=""selected disabled>Select</option>
                            <option value="brand">Brand</option>
                            <option value="sku">SKU</option>
                            <option value="dvc">DVC</option>
                            <option value="facing">Facing</option>
                        </select>
                        <span id="product_type_error" class="error-popup"></span>
                    </div>
                </div>
                <div class="row hide" id="alt_design_info">
                    <div class="form-group col-md-5">
                        <label for="alt_design_name" id="alt_name_label">*Alternative Design Name:</label>
                        <input type="text" id="alt_design_name" class="form-control">
                        <span id="alt_design_name_error" class="error-popup"></span>
                    </div>
                </div>
                <div class="row" id="facing_count">
                    <div class="form-group col-md-5">
                        <label for="num_facings">Number of Facings: <span id="output"></span></label>
                        <div class="slidecontainer">
                            <input type="range" min="0" max="5" value="0" class="slider" id="num_facings">
                            <span id="facing_error" class="error-popup"></span>
                        </div>
                    </div>
                </div>
<script>
var slider = document.getElementById("num_facings");
var output = document.getElementById("output");
output.innerHTML = slider.value; // Display the default slider value

// Update the current slider value (each time you drag the slider handle)
slider.oninput = function() {
  output.innerHTML = this.value;
}
</script>
                <div class="row" id="link_section">
                    <div class="form-group col-md-5 hide" id="manu_link_section">
                        <label for="manu_link">*Manufacturer Source Link:</label>
                        <input type="text" id="manu_link" class="form-control">
                        <span id="manu_link_error" class="error-popup"></span>
                    </div>
                    <div class="form-group col-md-5">
                        <label for="product_link">Product Source Link:</label>
                        <input type="text" id="product_link" class="form-control">
                        <span id="product_link_error" class="error-popup"></span>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-outline-primary" onclick="add_probe_product();">+ Product</button>
                    <button type="button" class="btn btn-outline-danger hide" id="cancel_product" onclick="cancel_product_button();">Save Changes</button>
                </div>
                <div id="probe_product_count_section">
                    <p>Products Added to Probe -> <span id="product_count"></span></p>
                </div>
                <span id="server_error" class="error-popup"></span>
                <span id="server_success" class="success-popup"></span>
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" value="Submit" onclick="validate_probe_submission();" id="submit_probe">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>