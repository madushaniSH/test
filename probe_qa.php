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

$pdo = NULL;

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

$sql = 'SELECT account_gid, account_nic, account_email, CONCAT (account_first_name," ",account_last_name) AS name, account_profile_picture_location FROM accounts WHERE account_id = :id';
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
     <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom styles for this template-->
    <link href="styles/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="styles/main.css" />
    <link rel="stylesheet" type="text/css" href="styles/probe_hunt.css" />
    <link rel="stylesheet" type="text/css" href="styles/probe_qa.css" />
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
    <title>Quality Assurance</title>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-home"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Data Operations</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item ">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <?php
            if ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor'){
                echo"
            <!-- Divider -->
            <hr class=\"sidebar-divider\">

            <!-- Heading -->
            <div class=\"sidebar-heading\">
                Management Tools
            </div>
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"new_project.php\">
                    <i class=\"fas fa-meteor fa-2x\"></i>   
                    <span>Create New Project</span></a>
            </li>

            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"upload_probe.php\">
                    <i class=\"fas fa-rocket fa-2x\"></i>
                    <span>Upload Probes</span></a>
            </li>

            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"export_projects.php\">
                    <i class=\"fas fa-file-export fa-2x\"></i>
                    <span>Export Project</span></a>
            </li>

            <li class=\"nav-item \">
                <a class=\"nav-link\" href=\"show_active.php\">
                    <i class=\"fas fa-users fa-2x\"></i>
                    <span>Manage Queue</span></a>
            </li>
            "
            ;
            }
             if ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor' || $_SESSION['role'] === 'SRT'){
                echo"
            <!-- Divider -->
            <hr class=\"sidebar-divider\">

            <!-- Heading -->
            <div class=\"sidebar-heading\">
                Hunter Tools
            </div>
            <li class=\"nav-item\">
                <a class=\"nav-link \" href=\"probe_hunt.php\">
                    <i class=\"fas fa-th-list fa-2x\"></i>
                    <span>Probe Hunt</span></a>
            </li>

            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"radar.php\">
                    <i class=\"fas fa-satellite-dish fa-2x\"></i>
                    <span>Radar Hunt</span></a>
            </li>

            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"ref_hunt.php\">
                    <i class=\"fas fa-book fa-2x\"></i>
                    <span>Reference Hunt</span></a>
            </li>
            "
            ;

            }
            if($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor' || $_SESSION['role'] === 'SRT Analyst'){
                echo"
            <!-- Divider -->
            <hr class=\"sidebar-divider\">

            <!-- Heading -->
            <div class=\"sidebar-heading\">
                Analyst Tools
            </div>
            <li class=\"nav-item active\">
                <a class=\"nav-link\" href=\"probe_qa.php\">
                    <i class=\"fas fa-search-plus fa-2x\"></i>
                    <span>Quality Assurance</span></a>
            </li>
            "
            ;
            }

            ?>
                <!-- Divider -->
                <hr class="sidebar-divider d-none d-md-block">

                <!-- Sidebar Toggler (Sidebar) -->
                <div class="text-center d-none d-md-inline">
                    <button class="rounded-circle border-0" id="sidebarToggle"></button>
                </div>

        </ul>
        <!-- End of Sidebar -->
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $user_information->name;?></span>
                                <img class="img-profile rounded-circle" src="<?php echo $user_information->account_profile_picture_location?>">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="details.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- Begin Page Content -->
                <div class="container-fluid">
                            <div class="card shadow mb-4">
                                <div class="card-body">
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
        <div id="options_hunt" class="hide">
            <div class="row">
            <div class="col">
                <button class="btn btn-secondary" onclick="show_upload_options_probe()" id="option1">Probe QA</button>
                <button class="btn btn-secondary" onclick="show_upload_options_radar()" id="option2">Radar QA</button>
                <button class="btn btn-secondary" onclick="show_upload_options_ref()" id="option3">Reference QA</button>
            </div>
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
        <div class="col">
        </div>
        <div class="col">
        </div>
        <div class="col">
            <label for="dvc_product_name">Select Product Name</label>
            <select name="dvc_product_name" id="dvc_product_name" class="form-control">
            </select>
            <span id="dvc_name_error" class="error-popup"></span>
        </div>
        <div class="col">
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
                    </div>
        <div class="row">
            <div id="probe_qa_options" class="col hide">
            <p class="error-popup" id="probe_qa_message"></p>
                <button class="btn" id="exit_btn" onclick="window.location.href='dashboard.php'"><i
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
            <div class="sticky-top" id="tab_buttons">
                <button class="tablinks" onclick="return open_tab(event, 'ref_hunt_information')" id="def_tab">QA Form</button>
                <button class="tablinks hide" onclick="return open_tab(event, 'ref_information')" id="ref_tab">Reference Information</button>
            </div>
            <div id="ref_information" class="tabcontent">
                <div class="row">
                    <div class="form-group col-md-2">
                        <label for="ref_recognition">Level:</label>
                        <input type="text" id="ref_recognition" class="form-control" readonly>
                    </div>
                    <div class="form-group col-md-10">
                        <label for="ref_short_name">Short Name:</label>
                        <input type="text" id="ref_short_name" class="form-control"readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="ref_sub_brand">Sub Brand:</label>
                        <input type="text" id="ref_sub_brand" class="form-control"readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="ref_manufacturer">Manufacturer:</label>
                        <input type="text" id="ref_manufacturer" class="form-control"readonly>
                    </div>                    
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="ref_category">Category:</label>
                        <input type="text" id="ref_category" class="form-control"readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="ref_sub_category">Sub Category:</label>
                        <input type="text" id="ref_sub_category" class="form-control"readonly>
                    </div>                    
                </div>   
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="ref_base_size">Base Size:</label>
                        <input type="text" id="ref_base_size" class="form-control"readonly>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="ref_size">Size:</label>
                        <input type="text" id="ref_size" class="form-control"readonly>
                    </div>                    
                    <div class="form-group col-md-2">
                        <label for="ref_measurement_unit">Unit:</label>
                        <input type="text" id="ref_measurement_unit" class="form-control"readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="ref_container_type">Container Type:</label>
                        <input type="text" id="ref_container_type" class="form-control"readonly>
                    </div>    
                </div>   
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="ref_agg_level">Agg Level:</label>
                        <input type="text" id="ref_agg_level" class="form-control"readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="ref_segment">Segment:</label>
                        <input type="text" id="ref_segment" class="form-control"readonly>
                    </div>                    
                </div>      
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="ref_upc2">UPC2 Count:</label>
                        <input type="text" id="ref_upc2" class="form-control"readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="ref_flavor_detail">Flavor Detail:</label>
                        <input type="text" id="ref_flavor_detail" class="form-control"readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="ref_case_pack">Case Pack:</label>
                        <input type="text" id="ref_case_pack" class="form-control"readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="ref_multi_pack">Multi Pack:</label>
                        <input type="text" id="ref_multi_pack" class="form-control"readonly>
                    </div>
                </div>
            </div>
            <div id="ref_hunt_information" class="tabcontent">
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
                    <div class="form-row">
                        <div class="col">
                            <p id="product_source_button">Go to Product Source</p> 
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <p id="suggestion_source_button" class="hide">Go to Suggestion Source</p> 
                        </div>
                    </div>
                    <p class="border-bottom my-3"></p>
                    <div class="form-row" id="rename_section">
                        <div class="col">
                            <div class="form-group">
                                <label for="product_rename">Product Re-Name:</label>
                                <input type="text" id="product_rename" class="form-control">
                                <span id="product_rename_error" class="error-popup"></span>
                                <span id="product_dup_rename_error" class="error-popup"></span>
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
                                <span id="product_alt_dup_rename_error" class="error-popup"></span>
                                <div class="alert alert-warning fade show hide" role="alert" id="dvc_rename_alert" >                            
                                    <strong>Warning!</strong> This will overwrite the existing Alternative Design Name
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group hide" id="manu_link_section">
                                <label for="manu_link">Manufacturer Link <span id="manu_source_button"></span> </label>
                                <input type="text" id="manu_link" class="form-control">
                                <span id="manu_error" class="error-popup"></span>
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
                            <p id="error_facing_error" class="error-popup"></p>
                            <p id="error_link_error" class="error-popup"></p>
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
   </div>
                <!-- /.container-fluid -->
            </div>

        </div>
    </div>
    <!-- End of Page Wrapper -->
    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Custom scripts for all pages-->
    <script src="scripts/sb-admin-2.min.js"></script>
</body>