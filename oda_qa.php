<?php
/*
    Author: Malika Liyanage
*/
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
    header('Location: login_auth_one.php');
    exit();
} else {
    if(!($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'ODA Supervisor' || $_SESSION['role'] === 'ODA')){
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

$sql = 'SELECT a.project_name, a.project_region, a.project_db_name FROM project_db.projects a';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$project_rows = $stmt->fetchAll(PDO::FETCH_OBJ);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <link rel='icon' href='favicon.ico' type='image/x-icon' />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard</title>
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="styles/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="styles/main.css" />
    <link rel="stylesheet" type="text/css" href="styles/oda_dashboard.css" />
    <link rel="stylesheet" type="text/css" href="styles/probe_hunt.css" />
    <link rel="stylesheet" type="text/css" href="styles/oda_qa.css" />
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="oda_dashboard.php">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-home"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Data Operations</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item ">
                <a class="nav-link" href="oda_dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>ODA Dashboard</span></a>
            </li>
            <?php
            if ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'ODA Supervisor'){
                echo"
            <!-- Divider -->
            <hr class=\"sidebar-divider\">
            <!-- Heading -->
            <div class=\"sidebar-heading\">
                Management Tools
            </div>
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"upload_product_info.php\">
                    <span>Upload Product Information</span></a>
            </li>
            "
            ;
            }
            ?>
            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Heading -->
            <div class="sidebar-heading">
                Tools
            </div>
            <li class="nav-item active">
                <a class="nav-link" href="oda_qa.php">
                    <i class="fas fa-search"></i>
                    <span>ODA QA</span></a>
            </li>

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
                                </div>
                            </div>
                            <div id="ticket_section" class="hide">
                            <div class="row">
                                <div class="col">
                                    <label for="ticket">Select Ticket ID</label>
                                    <select name="ticket" id="ticket" class="form-control" multiple>
                                    </select>
                                </div>
                            </div>
                            <div id="client_cat" class="hide">
                                <div class="row">
                                    <div class="col">
                                        <label for="client_category">Select Client Category</label>
                                        <select name="client_category" id="client_category" class="form-control" >
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <div id="qa_section" class="hide">
                             <div class="row">
                                 <div class="col">
                                     <div id="toggle_section">
                                         <!-- Rounded switch -->
                                     <label for="toggle">Reference QA</label>
                                     <label class="switch" id="toggle">
                                         <input type="checkbox" id="qa_type_toggle">
                                         <span class="slider round"></span>
                                     </label>
                                     </div>
                                 </div>
                             </div>
                             <div class="row text-center" id="counters">
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
                            <p class="error-popup text-center" id="probe_qa_message"></p>
                        </div>
                    </div>
                    </div>
            </div>
            <!-- /.container-fluid -->
        </div>

    </div>

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
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="scripts/sb-admin-2.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>
    <script src="scripts/oda_qa.js"></script>

</body>