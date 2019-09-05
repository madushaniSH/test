<?php
/*
    Filename: upload_probe.php
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

// Current settings to connect to the user account database
require('product_connection.php');
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
    echo "<p>Connection to $dbname database failed<br>Reason: ".$e->getMessage().'</p>';
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
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script
			  src="https://code.jquery.com/jquery-3.4.1.min.js"
			  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
			  crossorigin="anonymous"></script>
    <script src="scripts/transition.js"></script>
    <script src="scripts/validate_upload_probe.js"></script>
    <link rel="stylesheet" type="text/css" href="styles/main.css" />
    <link rel="stylesheet" type="text/css" href="styles/probe_upload.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>
    <script type="text/javascript"> (function() { var css = document.createElement('link'); css.href = 'https://use.fontawesome.com/releases/v5.1.0/css/all.css'; css.rel = 'stylesheet'; css.type = 'text/css'; document.getElementsByTagName('head')[0].appendChild(css); })(); </script>
    <title>Upload Probe</title>
</head>
<body >
<svg id="fader"></svg>
<nav class="navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="product_hunt.php" class="btn btn-light nav-back"><i class="fas fa-arrow-circle-left"></i></a>
            <a href="index.php" class="navbar-brand">Data Operations</a>
        </div>
    </div>
</nav>
<div id="probe_upload_section" >
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
<div id="ticket_section" class="row hide">
    <div class="col-md-4">
        <label for="ticket_name">Select Ticket ID</label>
        <button class="btn btn-outline-success" data-toggle="modal" data-target="#add_ticket">+Add New Ticket</button>
        <select name="ticket_name" id="ticket_name" class="form-control">
        </select>
    </div>
</div>
<div id="probe-upload" class="hide">
    <h2 id="ticket_message">Selected <span id="project_name_value"></span> <span id="ticket_id_value"></span></h2>
    <button class="btn btn-secondary" onclick="show_upload_options_probe()" id="option1">Probe Upload</button>
    <button class="btn btn-secondary" onclick="show_upload_options_reference()" id="option2">Reference Upload</button>
    <div id="probe-upload-container" class="hide">
        <label for="csv-file"><i class="fas fa-upload"><span> Upload Probe CSV file</span></i></label>
        <input type="file" id="csv-file" name="files"/>
        <div class="text-center">
        <span id="probe_upload_error" class="error-popup"></span>
        <span id="probe_upload_success" class="success-popup"></span>
            <div class="spinner-border text-success" role="status" id="loading-spinner">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
    <div id="ref-upload-container" class="hide">
        <label for="ref-csv-file"><i class="fas fa-upload"><span> Upload Reference CSV file</span></i></label>
        <input type="file" id="ref-csv-file" name="files"/>
        <div class="text-center">
        <span id="ref_upload_error" class="error-popup"></span>
        <span id="ref_upload_success" class="success-popup"></span>
            <div class="spinner-border text-success" role="status" id="loading-spinner-ref">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <span id="ref_process_success" class="success-popup"></span>
    </div>
</div>
</div>
<div class="modal hide fade modal-form" id="add_ticket" tabindex="-1" role="dialog"
    aria-labelledby="add_ticket_title" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_ticket_title">Enter New Ticket</h5>
                <button type="button" class="close" id="close_ticket_form" data-dismiss="modal" aria-label="Close" onclick="clear_ticket_form();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="new_error_form">
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label for="ticket_id">New Ticket ID:</label>
                                <input type="text" id="ticket_id" class="form-control">
                                <span id="ticket_id_error" class="error-popup"></span>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" data-dismiss="modal" onclick="clear_ticket_form();">Cancel</button>
                <button type="button" class="btn btn-success" value="Submit" onclick="validate_new_ticket();">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>    
</body>