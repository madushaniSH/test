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

$sql = 'SELECT project_db_name, project_region FROM `project_db`.projects WHERE 1';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$project_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
$count_projects = count($project_array);
$hunter_summary = array();
$max_size = 0;
$key = NULL;
for ($i = 0; $i < $count_projects; $i++) {
    $dbname = $project_array[$i]["project_db_name"];
    $dsn = 'mysql:host='.$host.';dbname='.$dbname;
    $pdo = new PDO($dsn, $user, $pwd);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = 'SELECT DISTINCT probe_processed_hunter_id, account_gid FROM (SELECT DISTINCT b.probe_processed_hunter_id, a.account_gid FROM '.$dbname.'.probe b INNER JOIN user_db.accounts a ON b.probe_processed_hunter_id = a.account_id INNER JOIN user_db.account_designations g ON g.account_id = a.account_id WHERE g.designation_id = 3  AND ( b.probe_hunter_processed_time >= :start_datetime AND b.probe_hunter_processed_time <= :end_datetime ) UNION ALL SELECT DISTINCT c.radar_hunter_id AS "probe_processed_hunter_id", a.account_gid FROM '.$dbname.'.radar_hunt c INNER JOIN user_db.accounts a ON c.radar_hunter_id = a.account_id INNER JOIN user_db.account_designations g ON g.account_id = a.account_id WHERE g.designation_id = 3 AND (c.radar_processed_time >= :start_datetime AND c.radar_processed_time <= :end_datetime )UNION ALL SELECT DISTINCT d.reference_processed_hunter_id AS "probe_processed_hunter_id", a.account_gid FROM '.$dbname.'.reference_info d INNER JOIN user_db.accounts a ON d.reference_processed_hunter_id = a.account_id INNER JOIN user_db.account_designations g ON g.account_id = a.account_id WHERE g.designation_id = 3 AND (d.reference_hunter_processed_time >= :start_datetime AND d.reference_hunter_processed_time <= :end_datetime) ) t3';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
    $this_project_hunters = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count_hunters = count($this_project_hunters);
    for ($j = 0; $j < $count_hunters; $j++) {
        $found = false;
        for ($k = 0; $k < count($hunter_summary); $k++) {
            if ($hunter_summary[$k]["probe_processed_hunter_id"] == $this_project_hunters[$j]["probe_processed_hunter_id"]) {
                $found = true;
                $project_count = $hunter_summary[$k]["project_count"];
                $hunter_summary[$k]["projects"][$project_count] = $dbname;
                $hunter_summary[$k]["project_region"][$project_count] = $project_array[$i]["project_region"];
                $hunter_summary[$k]["project_count"]++;
                break;
            }
        }
        if(!$found) {
            $hunter_summary[$max_size]["probe_processed_hunter_id"] = $this_project_hunters[$j]["probe_processed_hunter_id"];
            $sql = 'SELECT a.account_profile_picture_location, CONCAT (a.account_first_name," ", a.account_last_name) AS name FROM user_db.accounts a WHERE  a.account_id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id'=>$hunter_summary[$max_size]["probe_processed_hunter_id"]]);
            $user_information = $stmt->fetch(PDO::FETCH_OBJ);
            $hunter_summary[$max_size]["pic_location"] = $user_information->account_profile_picture_location;
            $hunter_summary[$max_size]["name"] = $user_information->name;
            $hunter_summary[$max_size]["account_gid"] = $this_project_hunters[$j]["account_gid"];
            $hunter_summary[$max_size]["Brand Hunted"] = 0;
            $hunter_summary[$max_size]["SKU Hunted"] = 0;
            $hunter_summary[$max_size]["DVC Hunted"] = 0;
            $hunter_summary[$max_size]["Hunted Facing Count"] = 0;
            $hunter_summary[$max_size]["QA Errors"] = 0;
            $hunter_summary[$max_size]["Accuracy"] = 0;
            $hunter_summary[$max_size]["EMEA"] = 0;
            $hunter_summary[$max_size]["AMER"] = 0;
            $hunter_summary[$max_size]["APAC"] = 0;
            $hunter_summary[$max_size]["DPG"] = 0;
            $hunter_summary[$k]["project_count"] = 0;
            $project_count = $hunter_summary[$k]["project_count"];
            $hunter_summary[$max_size]["projects"][$project_count] = $dbname;
            $hunter_summary[$max_size]["project_region"][$project_count] = $project_array[$i]["project_region"];
            $hunter_summary[$k]["project_count"]++;
            $max_size++;
        }
    }
    $pdo = NULL;
}
for ($i = 0; $i < count($hunter_summary); $i++){
    $count_projects = $hunter_summary[$i]["project_count"];
    for ($j = 0; $j < $count_projects; $j++) {
        $dbname = $hunter_summary[$i]["projects"][$j];
        $dsn = 'mysql:host='.$host.';dbname='.$dbname;
        $pdo = new PDO($dsn, $user, $pwd);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = 'SELECT COUNT(*) FROM '.$dbname.'.products a WHERE a.product_type ="brand" AND a.account_id = :account_id AND (a.product_creation_time >= :start_datetime AND a.product_creation_time <= :end_datetime) AND a.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $brand_count = $stmt->fetchColumn();
        if ($brand_count == NULL) {
            $brand_count = 0;
        }
        $hunter_summary[$i]["Brand Hunted"] += (int)$brand_count;
        $sql = 'SELECT COUNT(*) FROM '.$dbname.'.products a WHERE a.product_type ="sku" AND a.account_id = :account_id AND (a.product_creation_time >= :start_datetime AND a.product_creation_time <= :end_datetime) AND a.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $sku_count = $stmt->fetchColumn();
        if ($sku_count == NULL) {
            $sku_count = 0;
        }
        $hunter_summary[$i]["SKU Hunted"] += (int)$sku_count;
        $sql = 'SELECT COUNT(*) FROM '.$dbname.'.products a WHERE product_type ="dvc" AND a.account_id = :account_id AND (a.product_creation_time >= :start_datetime AND a.product_creation_time <= :end_datetime) AND a.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $dvc_count = $stmt->fetchColumn();
        if ($dvc_count == NULL) {
            $dvc_count = 0;
        }
        $hunter_summary[$i]["DVC Hunted"] += (int)$dvc_count;
        $sql = 'SELECT SUM(a.product_facing_count) FROM '.$dbname.'.products a WHERE a.account_id = :account_id AND (a.product_creation_time >= :start_datetime AND a.product_creation_time <= :end_datetime) AND a.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $facing_count = $stmt->fetchColumn();
        if ($facing_count == NULL) {
            $facing_count = 0;
        }
        $hunter_summary[$i]["Hunted Facing Count"] += (int)$facing_count;

        $sql = "SELECT COUNT(DISTINCT a.product_id) FROM ".$dbname.".products a INNER JOIN ".$dbname.".product_qa_errors b ON a.product_id = b.product_id WHERE a.account_id = :account_id AND a.product_qa_datetime >= :start_datetime AND a.product_qa_datetime <= :end_datetime AND a.product_status = 2";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $error_count = $stmt->fetchColumn();
        if ($error_count == NULL) {
            $error_count = 0;
        }
        $hunter_summary[$i]["QA Errors"] += (int)$error_count;
        switch ($hunter_summary[$i]["project_region"][$j]) {
            case 'AMER' : 
                $hunter_summary[$i]["AMER"]++; 
                break;
            case 'EMEA' : 
                $hunter_summary[$i]["EMEA"]++; 
                break;
            case 'APAC' : 
                $hunter_summary[$i]["APAC"]++; 
                break;
            case 'DPG' : 
                $hunter_summary[$i]["DPG"]++; 
            break;
        }
        $pdo = NULL;
    }


    $total_count = ($hunter_summary[$i]["Brand Hunted"] * 1.5)  + $hunter_summary[$i]["SKU Hunted"] + (($hunter_summary[$i]["DVC Hunted"] + $hunter_summary[$i]["Hunted Facing Count"]) / 2);
    $hunter_summary[$i]["Points"] = (int)$total_count;
    $monthly_accuracy = round(((($total_count - ($hunter_summary[$i]["QA Errors"] * 5) )/ $total_count) * 100),2);
    if ($monthly_accuracy == NULL || is_nan($monthly_accuracy)) {
        $monthly_accuracy = 0;
    }
    $hunter_summary[$i]["Accuracy"] = $monthly_accuracy . '%';

}
usort($hunter_summary, "custom_sort");
// Define the custom sort function
function custom_sort($a,$b) {
    return $a['Points']< $b['Points'];
}
for($i = 0; $i < count($hunter_summary); $i++) {
    $hunter_summary[$i]["Rank"] = $i + 1;
    $max = $hunter_summary[$i]["AMER"];
    $region = 'AMER';
    if ($max < $hunter_summary[$i]["EMEA"]){
        $max = $hunter_summary[$i]["EMEA"];
        $region = 'EMEA';
    }
    if ($max < $hunter_summary[$i]["APAC"]){
        $max = $hunter_summary[$i]["APAC"];
        $region = 'APAC';
    }
    if ($max < $hunter_summary[$i]["DPG"]){
        $max = $hunter_summary[$i]["DPG"];
        $region = 'DPG';
    }
    if ($max == 0) {
        $region = 'N/A';
    }
    $hunter_summary[$i]['region'] = $region;
    if ($hunter_summary[$i]["probe_processed_hunter_id"] == $_SESSION['id']) {
        $key =  $i;
    }
    unset($hunter_summary[$i][probe_processed_hunter_id]);
}
$return_arr[] = array("hunter_summary"=>$hunter_summary, "current_info"=>$hunter_summary[$key], "total"=>count($hunter_summary));
echo json_encode($return_arr);
?>