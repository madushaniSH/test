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

// getting the list of projects into an array from the passes string.
$project_array = explode(",",$_POST['project_name']);
$count_projects = count($project_array);
$hunter_summary = array();
$max_size = 0;
for ($i = 0; $i < $count_projects; $i++) {
    $dbname = $project_array[$i];
    $dsn = 'mysql:host='.$host.';dbname='.$dbname;
    $pdo = new PDO($dsn, $user, $pwd);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = 'SELECT project_language FROM `project_db`.projects WHERE project_name = :project_name';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["project_name"=>$dbname]);
    $project_lang = $stmt->fetch(PDO::FETCH_OBJ);
    $project_language = $project_lang->project_language;
    $sql = 'SELECT DISTINCT probe_processed_hunter_id, account_gid FROM (SELECT DISTINCT b.probe_processed_hunter_id, a.account_gid FROM '.$dbname.'.probe b INNER JOIN user_db.accounts a ON b.probe_processed_hunter_id = a.account_id UNION ALL SELECT DISTINCT c.radar_hunter_id AS "probe_processed_hunter_id", a.account_gid FROM '.$dbname.'.radar_hunt c INNER JOIN user_db.accounts a ON c.radar_hunter_id = a.account_id UNION ALL SELECT DISTINCT d.reference_processed_hunter_id AS "probe_processed_hunter_id", a.account_gid FROM '.$dbname.'.reference_info d INNER JOIN user_db.accounts a ON d.reference_processed_hunter_id = a.account_id) t3';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $this_project_hunters = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count_hunters = count($this_project_hunters);
    for ($j = 0; $j < $count_hunters; $j++) {
        $found = false;
        for ($k = 0; $k < count($hunter_summary); $k++) {
            if ($hunter_summary[$k]["probe_processed_hunter_id"] == $this_project_hunters[$j]["probe_processed_hunter_id"]) {
                $found = true;
                if ($project_language == "english") {
                    $hunter_summary[$k]["project_weight"][$i] = 1;
                } else if ($project_language == "non_english") {                
                    $hunter_summary[$k]["project_weight"][$i] = 2;
                }
                break;
            }
        }
        if(!$found) {
            $hunter_summary[$max_size]["probe_processed_hunter_id"] = $this_project_hunters[$j]["probe_processed_hunter_id"];
            $hunter_summary[$max_size]["account_gid"] = $this_project_hunters[$j]["account_gid"];
            $hunter_summary[$max_size]["Brand Hunted"] = 0;
            $hunter_summary[$max_size]["SKU Hunted"] = 0;
            $hunter_summary[$max_size]["DVC Hunted"] = 0;
            $hunter_summary[$max_size]["Hunted Facing Count"] = 0;
            $hunter_summary[$max_size]["Checked Probe Count"] = 0;
            $hunter_summary[$max_size]["Radar Link Count"] = 0;
            $hunter_summary[$max_size]["Checked Reference Count"] = 0;
            $hunter_summary[$max_size]["Disapproved Products"] = 0;
            $hunter_summary[$max_size]["Rename Errors"] = 0;
            $hunter_summary[$max_size]["System Errors"] = 0;
            $hunter_summary[$max_size]["QA Errors"] = 0;
            $hunter_summary[$max_size]["Accuracy"] = 0;
            if ($project_language == "english") {
                $hunter_summary[$max_size]["project_weight"][$i] = 1;
            } else if ($project_language == "non_english") {                
                $hunter_summary[$max_size]["project_weight"][$i] = 2;
            }
            $max_size++;
        }
    }
    $pdo = NULL;
}
for ($i = 0; $i < count($hunter_summary); $i++){
    for ($j = 0; $j < $count_projects; $j++) {
        $weight =  $hunter_summary[$i]["project_weight"][$j];
        $dbname = $project_array[$j];
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
        $hunter_summary[$i]["Brand Hunted"] += (int)($brand_count * $weight);
        $sql = 'SELECT COUNT(*) FROM '.$dbname.'.products a WHERE a.product_type ="sku" AND a.account_id = :account_id AND (a.product_creation_time >= :start_datetime AND a.product_creation_time <= :end_datetime) AND a.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $sku_count = $stmt->fetchColumn();
        if ($sku_count == NULL) {
            $sku_count = 0;
        }
        $hunter_summary[$i]["SKU Hunted"] += (int)($sku_count * $weight );
        $sql = 'SELECT COUNT(*) FROM '.$dbname.'.products a WHERE product_type ="dvc" AND a.account_id = :account_id AND (a.product_creation_time >= :start_datetime AND a.product_creation_time <= :end_datetime) AND a.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $dvc_count = $stmt->fetchColumn();
        if ($dvc_count == NULL) {
            $dvc_count = 0;
        }
        $hunter_summary[$i]["DVC Hunted"] += (int)($dvc_count * $weight);
        $sql = 'SELECT SUM(a.product_facing_count) FROM '.$dbname.'.products a WHERE a.account_id = :account_id AND (a.product_creation_time >= :start_datetime AND a.product_creation_time <= :end_datetime) AND a.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $facing_count = $stmt->fetchColumn();
        if ($facing_count == NULL) {
            $facing_count = 0;
        }
        $hunter_summary[$i]["Hunted Facing Count"] += (int)($facing_count* $weight); 
        $sql = 'SELECT COUNT(*) FROM '.$dbname.'.probe a WHERE a.probe_processed_hunter_id = :account_id AND (a.probe_hunter_processed_time >= :start_datetime AND a.probe_hunter_processed_time <= :end_datetime)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $checked_count = $stmt->fetchColumn();
        if ($checked_count == NULL) {
            $checked_count = 0;
        }
        $hunter_summary[$i]["Checked Probe Count"] += (int)$checked_count;

        $sql = 'SELECT COUNT(*) FROM '.$dbname.'.radar_sources a WHERE a.account_id = :account_id AND (a.creation_time >= :start_datetime AND a.creation_time <= :end_datetime)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $checked_count = $stmt->fetchColumn();
        if ($checked_count == NULL) {
            $checked_count = 0;
        }
        $hunter_summary[$i]["Radar Link Count"] += (int)$checked_count;

        $sql = 'SELECT COUNT(*) FROM '.$dbname.'.reference_info a WHERE (a.reference_hunter_processed_time >= :start_datetime AND a.reference_hunter_processed_time <= :end_datetime) AND a.reference_processed_hunter_id = :account_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $checked_count = $stmt->fetchColumn();
        if ($checked_count == NULL) {
            $checked_count = 0;
        }
        $hunter_summary[$i]["Checked Reference Count"] += (int)$checked_count;

        $sql = 'SELECT COUNT(*) FROM '.$dbname.'.products a WHERE a.account_id = :account_id AND a.product_qa_status = "disapproved" AND a.product_qa_account_id IS NOT NULL AND (a.product_creation_time >= :start_datetime AND a.product_creation_time <= :end_datetime) AND a.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $error_count = $stmt->fetchColumn();
        if ($error_count == NULL) {
            $error_count = 0;
        }
        $hunter_summary[$i]["Disapproved Products"] += (int)$error_count;

        $sql = "SELECT COUNT(*) FROM ".$dbname.".products a WHERE ( a.product_type = 'sku' OR a.product_type = 'brand' ) AND a.account_id = :account_id AND (a.product_previous IS NOT NULL OR a.product_alt_design_previous IS NOT NULL) AND a.product_qa_datetime >= :start_datetime AND a.product_qa_datetime <= :end_datetime AND a.product_status = 2";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $error_count = $stmt->fetchColumn();
        $hunter_summary[$i]["Rename Errors"] += (int)$error_count;
        if ($error_count == NULL) {
            $error_count = 0;
        }

        $sql = 'SELECT COUNT(*) FROM '.$dbname.'.products a WHERE a.account_id = :account_id AND a.product_qa_status = "disapproved" AND a.product_qa_account_id IS NULL AND (a.product_creation_time >= :start_datetime AND a.product_creation_time <= :end_datetime) AND a.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $error_count = $stmt->fetchColumn();
        if ($error_count == NULL) {
            $error_count = 0;
        }
        $hunter_summary[$i]["System Errors"] += (int)$error_count;

        $sql = "SELECT COUNT(a.product_id) FROM ".$dbname.".products a INNER JOIN ".$dbname.".product_qa_errors b ON a.product_id = b.product_id WHERE a.account_id = :account_id AND a.product_qa_datetime >= :start_datetime AND a.product_qa_datetime <= :end_datetime AND a.product_status = 2";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $error_count = $stmt->fetchColumn();
        if ($error_count == NULL) {
            $error_count = 0;
        }
        $hunter_summary[$i]["QA Errors"] += (int)$error_count;
        $pdo = NULL;
    }

    $total_count = ($hunter_summary[$i]["Brand Hunted"] * 1.5)  + $hunter_summary[$i]["SKU Hunted"] + (($hunter_summary[$i]["DVC Hunted"] + $hunter_summary[$i]["Hunted Facing Count"]) / 2);
    $monthly_accuracy = round(((($total_count - ($hunter_summary[$i]["QA Errors"] * 1) )/ $total_count) * 100),2);
    if ($monthly_accuracy == NULL) {
        $monthly_accuracy = 0;
    }
    $hunter_summary[$i]["Accuracy"] = $monthly_accuracy . '%';

    unset($hunter_summary[$i][project_weight]);
    unset($hunter_summary[$i][probe_processed_hunter_id]);
}
$return_arr[] = array("hunter_summary"=>$hunter_summary);
echo json_encode($return_arr);
?>