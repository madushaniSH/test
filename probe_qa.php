<?php
/*
    Filename: probe_qa.php
    Author: Malika Liyanage
*/

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
    header('Location: login_auth_one.php');
    exit();
} else {
    if (!($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor' || $_SESSION['role'] === 'SRT Analyst')) {
        header('Location: index.php');
        exit();
    }
}

// unset the variable out from session. out is used to store error messages from details.php
if (isset($_SESSION['out'])) {
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
try {
    // Creating a new PDO instance
    $pdo = new PDO($dsn, $user, $pwd);
    // setting the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" type="text/css" href="styles/main.css" />
    <link rel="stylesheet" type="text/css" href="styles/probe_hunt.css" />
    <link rel="stylesheet" type="text/css" href="styles/probe_qa.css" />
    <script src="scripts/transition.js"></script>
    <script src="scripts/validate_probe_qa.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>
    <!-- Prerenders font awesome-->
    <script type="text/javascript">
    (function() {
        var css = document.createElement('link');
        css.href = 'https://use.fontawesome.com/releases/v5.10.0/css/all.css';
        css.rel = 'stylesheet';
        css.type = 'text/css';
        document.getElementsByTagName('head')[0].appendChild(css);
    })();
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/js/plugins/piexif.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/js/fileinput.min.js"></script>
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
    <div id="over_counter_section">
        <div class="row">
            <div class="col">
                <label for="project_name">Select Project Name</label>
                <select name="project_name" id="project_name" class="form-control">
                    <option value="" selected disabled>Select</option>
                    <?php
                        foreach ($project_rows as $project_row) {
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
        <div class="row text-center hide" id="counters">
            <div class="col">
                <div class="counter overall-counter">
                    <i class="fas fa-clipboard-list fa-2x"></i>
                    <h2 id="current_brand_count" class="timer count-title count-number">
                        <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Loading...</span>
                        </div>
                    </h2>
                    <p class="count-text ">Pending Brand Count</p>
                </div>
            </div>
            <div class="col">
                <div class="counter overall-counter">
                    <i class="fas fa-clipboard-list fa-2x"></i>
                    <h2 id="current_sku_count" class="timer count-title count-number">
                        <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Loading...</span>
                        </div>
                    </h2>
                    <p class="count-text ">Pending SKU Count</p>
                </div>
            </div>
            <div class="col">
                <div class="counter overall-counter">
                    <i class="fas fa-clipboard-list fa-2x"></i>
                    <h2 id="current_dvc_count" class="timer count-title count-number">
                        <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Loading...</span>
                        </div>
                    </h2>
                    <p class="count-text ">Pending DVC Count</p>
                </div>
            </div>
            <div class="col">
                <div class="counter overall-counter">
                    <i class="fas fa-clipboard-list fa-2x"></i>
                    <h2 id="current_facing_count" class="timer count-title count-number">
                        <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Loading...</span>
                        </div>
                    </h2>
                    <p class="count-text ">Pending Facing Count</p>
                </div>
            </div>
        </div>
    </div>
    <div id="probe_hunt_section" class="hide">
    <div class="row">
        <div class="col">
        </div>
        <div class="col">
            <label for="brand_name">Select Brand Name</label>
            <select name="brand_name" id="brand_name" class="form-control">
            </select>
            <span id="brand_name_error" class="error-popup"></span>
        </div>
        <div class="col">
            <label for="dvc_name">Select Brand Name</label>
            <select name="dvc_name" id="dvc_name" class="form-control">
            </select>
            <span id="dvc_name_error" class="error-popup"></span>
        </div>
        <div class="col">
            <label for="facing_name">Select Brand Name</label>
            <select name="facing_name" id="facing_name" class="form-control">
            </select>
            <span id="facing_name_error" class="error-popup"></span>
        </div>
    </div>
        <div class="row">                        
            <div id="probe_qa_counter" class="col">
                <div class="row text-center">
                    <div class="col">
                        <button type="button" id="brand_qa_button" class="btn qa_button" onclick="assign_brand();">
                        <div class="counter">
                            <i class="far fa-copyright fa-2x"></i>
                            <h2 id="current_brand_count_2" class="timer count-title count-number">
                                <div class="spinner-border text-success" role="status">
                                <span class="sr-only">Loading...</span>
                                </div>
                            </h2>
                            <p class="count-text ">Remaining Brands</p>
                        </div>
                        </button>
                    </div>

                    <div class="col">
                        <button type="button" id="sku_qa_button" class="btn qa_button" onclick="assign_sku();">
                        <div class="counter">
                            <i class="fas fa-boxes fa-2x"></i>
                            <h2 id="current_sku_count_2" class="timer count-title count-number">
                                <div class="spinner-border text-success" role="status">
                                <span class="sr-only">Loading...</span>
                                </div>
                            </h2>
                            <p class="count-text ">Remaining SKU</p>
                        </div>
                        </button>
                    </div>

                    <div class="col">
                        <button type="button" id="dvc_qa_button" class="btn qa_button" onclick="assign_dvc();">
                        <div class="counter">
                            <i class="fas fa-eye fa-2x"></i>
                            <h2 id="current_dvc_count_2" class="timer count-title count-number">
                                <div class="spinner-border text-success" role="status">
                                <span class="sr-only">Loading...</span>
                                </div>
                            </h2>
                            <p class="count-text ">Remaining DVC</p>
                        </div>
                        </button>
                    </div>


                    <div class="col">
                        <button type="button" id="facing_qa_button" class="btn qa_button" onclick="assign_facing();">
                        <div class="counter">
                            <i class="fas fa-eye fa-2x"></i>
                            <h2 id="current_facing_count_2" class="timer count-title count-number">
                                <div class="spinner-border text-success" role="status">
                                <span class="sr-only">Loading...</span>
                                </div>
                            </h2>
                            <p class="count-text ">Remaining Facings</p>
                        </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div id="probe_qa_options" class="col hide">
            <p class="error-popup" id="probe_qa_message"></p>
                <button class="btn" id="exit_btn" onclick="window.location.href='product_hunt.php'"><i
                        class="fas fa-chevron-circle-left fa-3x"></i><br>Exit</i></button>
            </div>
        </div>
<div class="modal hide fade modal-form" id="qa_probe" tabindex="-1" role="dialog"
    aria-labelledby="qa_probe_title" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qa_probe_title"></h5>
                <button type="button" class="close" id="close_probe_title" data-dismiss="modal" aria-label="Close" onclick="unassign_probe();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form action="POST" id="qa_form">
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label for="product_name">Product Name:</label>
                                <input type="text" id="product_name" class="form-control" readonly>
                                <span id="name_error" class="error-popup"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group hide" id="alt_name_section">
                                <label for="alt_name">Alternative Design Name:</label>
                                <input type="text" id="alt_name" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <p class="border-bottom my-3">QA Information</p>
                    <div class="form-row" id="rename_section">
                        <div class="col">
                            <div class="form-group">
                                <label for="product_rename">Product Re-Name:</label>
                                <input type="text" id="product_rename" class="form-control">
                                <span id="product_rename_error" class="error-popup"></span>
                                <div class="alert alert-warning fade show hide" role="alert" id="rename_alert" >
                                    <strong>Warning!</strong> This will overwrite the existing Product Name
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group hide" id="alt_rename_section">
                                <label for="product_alt_rename">Product Alternative Re-Name:</label>
                                <input type="text" id="product_alt_rename" class="form-control">
                                <span id="product_alt_rename_error" class="error-popup"></span>
                                <div class="alert alert-warning fade show hide" role="alert" id="dvc_rename_alert" >                            
                                    <strong>Warning!</strong> This will overwrite the existing Alternative Design Name
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col col-md-7">
                            <div class="form-group">
                            <p>Error Type  <button type="button" class="btn btn-outline-danger add-error"  data-toggle="modal" data-target="#qa_error">Add Error</button></p>
                            <select name="error_qa" id="error_qa" class="form-control" multiple="multiple">
                                
                            </select>
                            <span id="error_qa_error" class="error-popup"></span>
                            <span id="error_facing_error" class="error-popup"></span>
                            </div>
                        </div>
                        <div class="col col-md-4">
                            <div class="form-group">
                                <label for="num_facings">Number of Facings: <span id="output"></span></label>
                                <div class="slidecontainer">
                                    <input type="range" min="0" max="5" value="0" class="slider" id="num_facings">
                                </div>
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
                    <div class="form-row">
                        <div class="col">
                            <p>Status</p>
                            <div class="form-check form-check-inline">
                                <label class="btn btn-success">
                                    <input class="form-check-input" type="radio" name="approve_status" id="approve" value="approve">                                   
                                    <label class="form-check-label" for="approve">Approved</label>
                                    <i class="fas fa-check-square"></i>
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <label class="btn btn-danger">
                                    <input class="form-check-input" type="radio" name="approve_status" id="disapprove" value="disapprove">                                   
                                    <label class="form-check-label" for="disapprove">Disapproved</label>
                                    <i class="fas fa-times-circle"></i>
                                </label>
                            </div>
                            <span id="status_error" class="error-popup"></span>
                        </div>
                    </div>
                    <div class="form-row hide" id="error_image_section">
                        <div class="col">
                            <label class="colcontrol-label">
                                Image Attachment(s)
                            </label>
                            <div class="col">
                                <span class="btn btn-default btn-file">
                                    <input id="error_images" name="error_images" type="file" class="file" multiple data-show-caption="true">
                                </span>
                            </div>
                            <span id="image_error" class="error-popup"></span>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" data-dismiss="modal" onclick="unassign_probe();">Cancel</button>
                <button type="button" class="btn btn-success" value="Submit" onclick="validate_qa_form();">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>    
<div class="modal hide fade modal-form" id="qa_error" tabindex="-1" role="dialog"
    aria-labelledby="qa_error_title" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qa_error_title">Add New Error Type</h5>
                <button type="button" class="close" id="close_error_form" data-dismiss="modal" aria-label="Close" onclick="clear_error_form()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="new_error_form">
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label for="error_new_name">New Error Type:</label>
                                <input type="text" id="error_new_name" class="form-control">
                                <span id="error_new_error" class="error-popup"></span>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" data-dismiss="modal" onclick="clear_error_form()">Cancel</button>
                <button type="button" class="btn btn-success" value="Submit" onclick="validate_new_error_type();">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>    
</body>