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
    if (!($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor')) {
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
    <link rel="stylesheet" type="text/css" href="styles/export_projects.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.15.1/xlsx.core.min.js"></script>
    <script src="scripts/export_details.js"></script>
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <title>Export Project</title>
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

            <li class=\"nav-item active\">
                <a class=\"nav-link\" href=\"export_projects.php\">
                    <i class=\"fas fa-file-export fa-2x\"></i>
                    <span>Export Project</span></a>
            </li>

            <li class=\"nav-item\">
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
                <a class=\"nav-link\" href=\"probe_hunt.php\">
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
            <div class="card">
                    <?php
if($_SESSION['role'] === 'Admin'){
    echo "
        <div class=\"col\">
            <button class=\"btn btn-success\" onclick=\"special();\">Click Me</button>
        </div>
    ";
}
?>
                        <div id="fetch_project_section">
                            <div class="row" id="export_button_selection">
                                <div class="col">
                                    <button class="btn btn-success" onclick="show_productivity_info();"><i class="fas fa-chart-line fa-2x"></i> Get Hunter Productivity</button>
                                </div>
                                <div class="col">
                                    <button class="btn btn-success" onclick="show_product_info();"><i class="fab fa-product-hunt fa-2x"></i> Get Hunted Products</button>
                                </div>
                                <div class="col">
                                    <button class="btn btn-success" onclick="show_probe_info();"><i class="fas fa-search fa-2x"></i> Get Probe Details</button>
                                </div>
                                <div class="col">
                                    <button class="btn btn-success" onclick="show_hunter_info();"><i class="fas fa-fist-raised fa-2x"></i> Get Hunter Summary</button>
                                </div>
                            </div>
                            <div class="row hide" id="project_select">
                                <div class="col">
                                    <label for="project_name">Select Project Name</label>
                                    <select name="project_name" id="project_name" class="form-control" multiple="multiple">
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
                            <div id="ticket_section" class="row hide">
                                <div class="col-md-4">
                                    <label for="ticket_name">Select Ticket ID</label>
                                    <select name="ticket_name" id="ticket_name" class="form-control">
                                    </select>
                                </div>
                            </div>
                            <div id="generate_productivity_section" class="row hide">
                                <div class="col">
                                    <input id="datetime_filter_productivity" type="text" name="datetimes" value="" />
                                </div>
                            </div>
                            <div id="generate_csv_section" class="row hide">
                                <div class="col">
                                    <input id="datetime_filter" type="text" name="datetimes" value="" />
                                </div>
                            </div>
                            <div id="export_final">
                                <button class="btn btn-primary hide" id="export_button" onclick="fetch_details()"><i class="fas fa-download fa-3x"></i></button>
                                <div class="row">
                                   <div class="col">
                                       <button class="btn btn-primary hide" id="export_button_productivity" onclick="fetch_details_productivity()"><i class="fas fa-download"></i> Fetch Productivity Final Count</button>
                                   </div>
                                    <div class="col">
                                        <button class="btn btn-primary hide" id="export_button_productivity_single" onclick="fetch_details_productivity_single()"><i class="fas fa-download"></i> Fetch Productivity Single Count</button>
                                    </div>
                                    <div class="col">
                                        <button class="btn btn-primary hide" id="export_button_performance_report" onclick="fetch_performance_report()"><i class="fas fa-download"></i> Fetch Performance Report</button>
                                    </div>
                                    <div class="col">
                                        <button class="btn btn-primary hide" id="export_button_performance_qa" onclick="fetch_performance_report_qa()"><i class="fas fa-download"></i> Fetch QA Report</button>
                                    </div>
                                </div>
                                <div class="row my-3 hide" id="performance_loader">
                                    <div class="col">
                                        <div class="spinner-grow text-danger" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <p class="text-warning">Loading Please Wait :D</p>
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