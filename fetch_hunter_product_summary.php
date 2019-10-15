<?php
/*
    Author: Malika Liyanage
*/
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
	header('Location: login_auth_one.php');
	exit();
}
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
	header('Location: login_auth_one.php');
	exit();
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

$sql = 'SELECT project_db_name FROM `project_db`.projects WHERE project_region = :region';
$stmt = $pdo->prepare($sql);
$stmt->execute(['region'=>$_POST['project_region']]);
$project_array = $stmt->fetchAll(PDO::FETCH_ASSOC);

$begin = new DateTime(strval($_POST['start_datetime']));
$end = new DateTime(strval($_POST['end_datetime']));
$end_date_for_loop = $end->modify('+1 day');

$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($begin, $interval, $end_date_for_loop);
$summary = array();

for ($i = 0; $i < count($project_array); $i++) {
    $dbname = $project_array[$i]["project_db_name"];
    $dsn = 'mysql:host='.$host.';dbname='.$dbname;
    $pdo = new PDO($dsn, $user, $pwd);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $summary[info][$i]['project_name'] = $dbname;

    $count = 0;
    foreach ($period as $dt) {
        $date = $dt->format("Y-m-d");
        $summary[info][$i][$count] = array_fill_keys(array('sku', 'brand', 'dvc', 'facing', 'errors'),0);
        $sql = 'SELECT COUNT(*) FROM '.$dbname.'.products a WHERE a.product_type ="sku" AND a.account_id = :account_id AND (DATE(a.product_creation_time) = :date) AND a.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'], 'date'=>$date]);
        $sku_count = $stmt->fetchColumn();
        if ($sku_count == NULL) {
            $sku_count = 0;
        }
        $sql = 'SELECT COUNT(*) FROM '.$dbname.'.products a WHERE a.product_type ="brand" AND a.account_id = :account_id AND (DATE(a.product_creation_time) = :date) AND a.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'], 'date'=>$date]);
        $brand_count = $stmt->fetchColumn();
        if ($brand_count == NULL) {
            $brand_count = 0;
        }
        $sql = 'SELECT COUNT(*) FROM '.$dbname.'.products a WHERE a.product_type ="dvc" AND a.account_id = :account_id AND (DATE(a.product_creation_time) = :date) AND a.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'], 'date'=>$date]);
        $dvc_count = $stmt->fetchColumn();
        if ($dvc_count == NULL) {
            $dvc_count = 0;
        }
        $summary[info][$i][$count][date] = $date;
        $summary[info][$i][$count]['sku'] = $sku_count;
        $summary[info][$i][$count]['brand'] = $brand_count;
        $summary[info][$i][$count]['dvc'] = $dvc_count;
    }
    
}

$return_arr[] = array("summary"=>$summary);
echo json_encode($return_arr);
?>