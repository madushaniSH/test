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
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
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
    <link rel="stylesheet" type="text/css" href="styles/ref_hunt.css" />
    <script src="scripts/validate_ref_hunt.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
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
    <title>Reference Hunt</title>
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
            <li class='nav-item'>
                <a class='nav-link' href='ticket_handler/ticket.php'>
                    <i class='fas fa-ticket-alt fa-2x'></i>
                    <span>Ticket Manager</span></a>
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

            <li class=\"nav-item \">
                <a class=\"nav-link\" href=\"radar.php\">
                    <i class=\"fas fa-satellite-dish fa-2x\"></i>
                    <span>Radar Hunt</span></a>
            </li>

            <li class=\"nav-item active\">
                <a class=\"nav-link\" href=\"ref_hunt.php\">
                    <i class=\"fas fa-book fa-2x\"></i>
                    <span>Reference Hunt</span></a>
            </li>

            <li class=\"nav-item\">
               <a class=\"nav-link\" href=\"constructor\constructor.php\">
               <i class=\"fas fa-drafting-compass\"></i>
               <span>Product Name Constructor</span></a>
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
            <li class=\"nav-item\">
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
                    <div class="row">
                        <div id="hunter_counter" class="col hide">
                            <div class="downArrow bounce" id="arrow_sec">
                                <a class="btn" id="show_button"><i class="fas fa-chevron-circle-down fa-3x"></i><br> <?php if ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor' ){ echo "Show <span id=\"acc_pro\"></span> Daily Progress";} else {echo "Show Daily Count for <span id=\"acc_pro\"></span>";} ?></a>
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
                                        <p class="count-text ">Reference Count</p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="counter">
                                        <i id="qa_error" class="fas fa-skull-crossbones fa-2x"></i>
                                        <h2 id="qa_error_count" class="timer count-title count-number">
                    <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </h2>
                                        <p class="count-text ">Disapproved Products</p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="counter">
                                        <i id="error_type_error" class="fa-2x fas fa-exclamation-circle"></i>
                                        <h2 id="error_type_count" class="timer count-title count-number">
                    <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </h2>
                                        <p class="count-text ">QA Errors</p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="counter">
                                        <i id="rename_error" class="fa-2x fas fa-eye-slash"></i>
                                        <h2 id="rename_error_count" class="timer count-title count-number">
                    <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </h2>
                                        <p class="count-text ">Rename Errors</p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="counter">
                                        <i id="system_error" class="fa-2x fas fa-laptop-code"></i>
                                        <h2 id="system_error_count" class="timer count-title count-number">
                    <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </h2>
                                        <p class="count-text ">System Errors</p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="counter">
                                        <i id="accuracy" class="fa-2x fas fa-bullseye"></i>
                                        <h2 id="mon_acc_count" class="timer count-title count-number">
                    <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </h2>
                                        <p class="count-text ">Cycle Accuracy</p>
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
                                    <option value="" selected disabled>Select</option>
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
                            <div id="ref_hunt_counter" class="col hide">
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
                                <label for="brand_name_filter">Filter by Brand Name</label>
                                <select name="brand_name_filter" id="brand_name_filter" class="form-control">
                                </select>
                                <div class="col my-3">
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
                                <button class="btn" id="exit_btn" onclick="window.location.href='dashboard.php'"><i class="fas fa-chevron-circle-left fa-3x"></i>
                                    <br>Exit</i>
                                </button>
                                <p class="error-popup" id="probe_message"></p>
                            </div>
                        </div>
</div>
                    </div>
                    <div class="modal hide fade modal-form" id="add_reference" tabindex="-1" role="dialog" aria-labelledby="add_reference_title" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="add_reference_title"></h5>
                                    <button type="button" class="close hide" id="close_probe_title" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="POST" id="probe_form">
                                        <div class="sticky-top" id="tab_buttons">
                                            <button class="tablinks" onclick="return open_tab(event, 'ref_information')" id="def_tab">Reference Information</button>
                                            <button class="tablinks" onclick="return open_tab(event, 'ref_hunt_information')">Hunter Form</button>
                                            <div class="row" id="ref_product_counter">
                                                <p>Brands : <span id="ref_current_brand_counter">0</span> / 1</p>
                                                <p>SKU : <span id="ref_current_sku_counter">0</span> / 1</p>
                                                <p>DVC : <span id="ref_current_dvc_counter">0</span> / -</p>
                                                <p>Facing : <span id="ref_current_facing_counter">0</span> / -</p>
                                            </div>
                                        </div>
                                        <hr/>
                                        <div id="ref_information" class="tabcontent">
                                            <div class="row">
                                                <div class="form-group col-md-2">
                                                    <label for="ref_recognition">Level:</label>
                                                    <input type="text" id="ref_recognition" class="form-control" readonly>
                                                </div>
                                                <div class="form-group col-md-10">
                                                    <label for="ref_short_name">Short Name:</label>
                                                    <input type="text" id="ref_short_name" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="ref_sub_brand">Sub Brand:</label>
                                                    <input type="text" id="ref_sub_brand" class="form-control" readonly>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="ref_manufacturer">Manufacturer:</label>
                                                    <input type="text" id="ref_manufacturer" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="ref_category">Category:</label>
                                                    <input type="text" id="ref_category" class="form-control" readonly>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="ref_sub_category">Sub Category:</label>
                                                    <input type="text" id="ref_sub_category" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-3">
                                                    <label for="ref_base_size">Base Size:</label>
                                                    <input type="text" id="ref_base_size" class="form-control" readonly>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="ref_size">Size:</label>
                                                    <input type="text" id="ref_size" class="form-control" readonly>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label for="ref_measurement_unit">Unit:</label>
                                                    <input type="text" id="ref_measurement_unit" class="form-control" readonly>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="ref_container_type">Container Type:</label>
                                                    <input type="text" id="ref_container_type" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="ref_agg_level">Agg Level:</label>
                                                    <input type="text" id="ref_agg_level" class="form-control" readonly>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="ref_segment">Segment:</label>
                                                    <input type="text" id="ref_segment" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="ref_upc2">UPC2 Count:</label>
                                                    <input type="text" id="ref_upc2" class="form-control" readonly>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="ref_flavor_detail">Flavor Detail:</label>
                                                    <input type="text" id="ref_flavor_detail" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="ref_case_pack">Case Pack:</label>
                                                    <input type="text" id="ref_case_pack" class="form-control" readonly>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="ref_multi_pack">Multi Pack:</label>
                                                    <input type="text" id="ref_multi_pack" class="form-control" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="ref_hunt_information" class="tabcontent">
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
                                                        <span id="product_name_ref_error" class="error-popup"></span>
                                                    </div>
                                                    <div class="form-group col-md-5">
                                                        <label for="product_type">*Product Type:</label>
                                                        <select name="product_type" id="product_type" class="form-control">
                                                            <option value="" selected disabled>Select</option>
                                                            <option value="brand">Brand</option>
                                                            <option value="sku">SKU</option>
                                                            <option value="dvc">DVC</option>
                                                            <option value="facing">Facing</option>
                                                        </select>
                                                        <span id="product_type_error" class="error-popup"></span>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-5 hide" id="alt_design_info">
                                                        <label for="alt_design_name" id="alt_name_label">*Alternative Design Name:</label>
                                                        <input type="text" id="alt_design_name" class="form-control">
                                                        <span id="alt_design_name_error" class="error-popup"></span>
                                                    </div>
                                                    <div class="form-group col-md-5">
                                                        <label for="product_comment">Product Comment:</label>
                                                        <input type="text" id="product_comment" class="form-control">
                                                        <span id="product_comment_error" class="error-popup"></span>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-5">
                                                    <div class="checkbox">
                                                        <label class="check_box_container">Resubmitted Product
                                                        <input type="checkbox" id="resubmitted_product">
                                                        <span class="check_box_checkmark"></span>
                                                    </div>
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
                                                    <button type="button" class="btn btn-outline-primary" id="add_ref_product">+ Product</button>
                                                </div>
                                                <span id="server_error" class="error-popup"></span>
                                                <span id="server_success" class="success-popup"></span>
                                            </div>
                                        </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-success" value="Submit" id="submit_probe">Save changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="confirm_probe" tabindex="-1" role="dialog" aria-labelledby="econfirm_probe_title" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog  modal-dialog-centered modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirm_probe_title">Are You Sure ?</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-success" id="confirm_save">Confirm</button>
                            </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/JQuery-Snowfall/1.7.4/snowfall.jquery.min.js"></script>
    <script src="scripts/snow.js"></script>
</body>