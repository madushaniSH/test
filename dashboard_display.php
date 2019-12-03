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
    if(!($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'dashboard_display')){
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
    <link href="styles/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="styles/main.css" />
    <link rel="stylesheet" type="text/css" href="styles/display.css" />
</head>

<body id="page-top">
<!-- Page Wrapper -->
<div id="wrapper">
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <!-- Main Content -->
        <div id="content">
            <!-- Begin Page Content -->
            <div class="container-fluid">
                <div id="loader"></div>
                <div id="main_div">
                    <div id="slideshow">
                    <div class="row">
                        <div class="col">
                            <!-- Basic Card Example -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Total Points by Region</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-1">
                                            <p>Rank</p>
                                        </div>
                                        <div class="col-md-2">
                                            <p>Region</p>
                                        </div>
                                        <div class="col">
                                            <p>Productivity</p>
                                        </div>
                                        <div class="col">
                                            <p>Resubmit Count</p>
                                        </div>
                                        <div class="col">
                                            <p>Hunter Accuracy</p>
                                        </div>
                                        <div class="col-md-2">
                                            <p>Hunter Naming Accuracy</p>
                                        </div>
                                        <div class="col">
                                            <p>QA Accuracy</p>
                                        </div>
                                        <div class="col-md-2">
                                            <p>QA Naming Accuracy</p>
                                        </div>
                                        <div class="col">
                                            <p class="text-success">Points</p>
                                        </div>
                                    </div>
                                    <div id="project_section">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="top">
                        <div class="col">
                            <!-- Basic Card Example -->
                            <div class="card">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-success">Top Three Hunters</h6>
                                </div>
                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-md-1">
                                            <p>Rank</p>
                                        </div>
                                        <div class="col-md-1">
                                        </div>
                                        <div class="col-md-3">
                                            <p>Hunter</p>
                                        </div>
                                        <div class="col">
                                            <p>Region</p>
                                        </div>
                                        <div class="col-md-3">
                                            <p>Naming Accuracy</p>
                                        </div>
                                        <div class="col">
                                            <p>Accuracy</p>
                                        </div>
                                        <div class="col">
                                            <p class="text-success">Points</p>
                                        </div>
                                    </div>
                                    <div id="leader_board_section">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="bottom">
                    <div class="col">
                            <div class="card">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-danger">Bottom Three Hunters</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-1">
                                            <p>Rank</p>
                                        </div>
                                        <div class="col-md-1">
                                        </div>
                                        <div class="col-md-3">
                                            <p>Hunter</p>
                                        </div>
                                        <div class="col">
                                            <p>Region</p>
                                        </div>
                                        <div class="col-md-3">
                                            <p>Naming Accuracy</p>
                                        </div>
                                        <div class="col">
                                            <p>Accuracy</p>
                                        </div>
                                        <div class="col">
                                            <p class="text-danger">Points</p>
                                        </div>
                                    </div>
                                    <div id="bottom_board_section">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <?php
                    if($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor'){
                        echo'
                    <div class="row my-3 ">
                        <div class="col">
                            <!-- Basic Card Example -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Hunter Details</h6>
                                </div>
                                <div class="card-body">
                                 <div class = "row my-3">
                                 <div class="col">
                                    <div class="table-responsive">
                                        <label for="hunter_filter_rank">Filter Hunter Name</label>
                                        <select name="hunter_filter_rank" id="hunter_filter_rank" class="form-control">
                                        <option value="" selected>None</option>
                                        </select>
                                        <label for="hunter_filter_region">Filter by Region</label>
                                        <select name="hunter_filter_rank" id="hunter_filter_region" class="form-control">
                                        <option value="" selected>All</option>
                                        </select>
                                        <table class="table table-bordered" id="dataTableHunter" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Rank</th>
                                                    <th>Hunter</th>
                                                    <th>Region</th>
                                                    <th>Productivity</th>
                                                    <th>Naming Accuracy</th>
                                                    <th>Accuracy</th>
                                                    <th>Points</th>
                                               </tr>
                                            </thead>
                                        </table>
                                 </div>
                                 </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    ';
                    }
                    ?>
                    <?php
                    if($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor'){
                        echo '
                        <div class="row my-3">
                         <div class="col-lg-6">
                                <!-- Basic Card Example -->
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Product Count By Region</h6>
                                    </div>
                                    <div class="card-body">
                                    <p id="display_message_product_chart" class="hide"> Overall Count is 0, get back to work soldier!</p>
                                     <canvas id="product_chart" class="hide"></canvas>
                                    </div>
                                </div>
                            </div>
                        ';
                    }else {
                        echo '
                        <div class="row my-3 hide">
                            <div class="col-lg-6">
                                <!-- Basic Card Example -->
                                <div class="card shadow mb-4">
                                  <div class="card-header py-3">
                                      <h6 class="m-0 font-weight-bold text-primary">Error Type Chart</h6>
                                  </div>
                                  <div class="card-body">
                                  <p id="display_message_chart" class="hide"> Overall Error Count is 0</p>
                                    <canvas id="error_type_chart" class="hide"></canvas>
                                  </div>
                            </div>
                        </div>
                        ';
                    }
                    ?>
                    <?php
                    if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Supervisor') {
                        echo '
                            <div class="col-lg-6">
                                <!-- Basic Card Example -->
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Project Error Comparisons</h6>
                                    </div>
                                    <div class="card-body">
                                    <p id="display_message_chart_project_comp" class="hide"> Yay, you got no errors!</p>
                                     <canvas id="error_type_chart_project_comp" class="hide"></canvas>
                                    </div>
                                </div>
                            </div>
                            ';
                    }
                    ?>
                </div>
                <div class="row my-3 hide">
                    <div class="col">
                        <!-- Basic Card Example -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Product Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2 ">
                                        <label for="project_region">Select Project Region</label>
                                        <select name="project_region" id="project_region" class="form-control">
                                            <option value="AMER" selected >AMER</option>
                                            <option value="EMEA">EMEA</option>
                                            <option value="APAC">APAC</option>
                                            <option value="DPG">DPG</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label for="project_name">Select Project Name</label>
                                        <select name="project_name" id="project_name" class="form-control" multiple="multiple">
                                        </select>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <label for="datetime_filter">Date Range</label>
                                        <input id="datetime_filter" type="text" name="datetimes" value="" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <button class="btn btn-success btn-icon-split" id="fetch_details_hunter">
                                            <span class="text">Fetch Details</span>
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="hide" id="load_section">
                                            <div class="spinner-border text-success" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class = "row my-3">
                                    <div class="col">
                                        <div class="table-responsive hide" id="table_section">
                                            <label for="hunter_filter">Select Hunter GID</label>
                                            <select name="hunter_filter" id="hunter_filter" class="form-control">
                                            </select>
                                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                <thead>
                                                <tr>
                                                    <th>Project Name</th>
                                                    <th>Date</th>
                                                    <th>Hunter GID</th>
                                                    <th>Brands Hunted <span class="table_count_button" id="brand_count"></span></th>
                                                    <th>SKU Hunted <span class="table_count_button" id="sku_count"></span></th>
                                                    <th>DVC Hunted <span class="table_count_button" id="dvc_count"></span></th>
                                                    <th>Facing Hunted <span class="table_count_button" id="facing_count"></span></th>
                                                    <th>Error Count <span class="table_count_button" id="error_count"></span></th>
                                                    <th>Rename Count <span class="table_count_button" id="rename_count"></span></th>
                                                    <th>Explore</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Supervisor') {
                    echo '
                    <div class="row my-3">
                        <div class="col">
                            <!-- Basic Card Example -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Project Error Type Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="project_region_error_type">Select Project Region</label>
                                            <select name="project_region_error_type" id="project_region_error_type" class="form-control">
                                                <option value="ALL" selected >ALL</option>
                                                <option value="AMER" >AMER</option>
                                                <option value="EMEA">EMEA</option>
                                                <option value="APAC">APAC</option>
                                                <option value="DPG">DPG</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label for="project_name_error_type">Select Project Name</label>
                                            <select name="project_name_error_type" id="project_name_error_type" class="form-control" multiple="multiple">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col-md-4">
                                            <label for="datetime_filter_error_type">Date Range</label>
                                            <input id="datetime_filter_error_type" type="text" name="datetimes" value="" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <button class="btn btn-success btn-icon-split" id="fetch_project_error_lists">
                                                <span class="text">Fetch Details</span>
                                            </button>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="hide" id="load_section_error_type">
                                                <div class="spinner-border text-success" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div id="display_message_error_type_chart" class="hide">
                                        <p> ¯\_(ツ)_/¯ 0 errors found.</p>
                                        <img src="images/tenor.gif">
                                    </div>
                                    <div class="row my-3">
                                        <div id="chart-container">
                                            <canvas id="error_type_chart_project" class="hide"></canvas>
                                        </div>
                                    </div>
                                 </div>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
                ?>
            </div>
        </div>
        <!-- /.container-fluid -->
    </div>

</div>
</div>
<!-- Product Detail Modal-->
<div class="modal fade" id="product_detail_modal" tabindex="-1" role="dialog" aria-labelledby="product_detail_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="product_detail_modal_title"></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="product_data_table" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Alt Name</th>
                        <th>Type</th>
                        <th>Creation Time</th>
                        <th>QA Time</th>
                        <th>QA Status</th>
                        <th>QA Errors</th>
                        <th>Error URL</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            </div>
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
<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="scripts/sb-admin-2.min.js"></script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet"/>
<script src="vendor/chart.js/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
<script src="scripts/dashboard.js"></script>
<script src="scripts/display.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/JQuery-Snowfall/1.7.4/snowfall.jquery.min.js"></script>
<script src="scripts/snow.js"></script>
</body>
