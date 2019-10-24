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
$sql = 'SELECT project_db_name, project_region, project_language FROM `project_db`.projects WHERE 1';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$project_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
$count_projects = count($project_array);
$hunter_summary = array();
$project_summary = array();
$project_summary["AMER"]["name"] = 'AMER';
$project_summary["AMER"]["productivity"] = 0;
$project_summary["AMER"]["error_count"] = 0;
$project_summary["AMER"]["rename_count"] = 0;
$project_summary["AMER"]["points"] = 0;

$project_summary["EMEA"]["name"] = 'EMEA';
$project_summary["EMEA"]["productivity"] = 0;
$project_summary["EMEA"]["error_count"] = 0;
$project_summary["EMEA"]["rename_count"] = 0;
$project_summary["EMEA"]["points"] = 0;

$project_summary["APAC"]["name"] = 'APAC';
$project_summary["APAC"]["productivity"] = 0;
$project_summary["APAC"]["rename_count"] = 0;
$project_summary["APAC"]["error_count"] = 0;
$project_summary["APAC"]["points"] = 0;

$project_summary["DPG"]["name"] = 'DPG';
$project_summary["DPG"]["productivity"] = 0;
$project_summary["DPG"]["error_count"] = 0;
$project_summary["DPG"]["rename_count"] = 0;
$project_summary["DPG"]["points"] = 0;
$error_chart = array();
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
                if ($project_array[$i]["project_language"] == "english") {
                    $hunter_summary[$k]["project_weight"][$project_count] = 1;
                } else if ($project_array[$i]["project_language"] == "non_english") {                
                    $hunter_summary[$k]["project_weight"][$project_count] = 2;
                }
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
            $hunter_summary[$max_size]["Rename Errors"] = 0;
            $hunter_summary[$max_size]["System Errors"] = 0;
            $hunter_summary[$max_size]["Accuracy"] = 0;
            $hunter_summary[$max_size]["EMEA"] = 0;
            $hunter_summary[$max_size]["AMER"] = 0;
            $hunter_summary[$max_size]["APAC"] = 0;
            $hunter_summary[$max_size]["DPG"] = 0;
            $hunter_summary[$max_size]["project_count"] = 0;
            $project_count = $hunter_summary[$max_size]["project_count"];
            $hunter_summary[$max_size]["projects"][$project_count] = $dbname;
            if ($project_array[$i]["project_language"] == "english") {
                $hunter_summary[$max_size]["project_weight"][$project_count] = 1;
            } else if ($project_array[$i]["project_language"] == "non_english") {                
                $hunter_summary[$max_size]["project_weight"][$project_count] = 2;
            }
            $hunter_summary[$max_size]["project_region"][$project_count] = $project_array[$i]["project_region"];
            $hunter_summary[$max_size]["project_count"]++;
            $hunter_summary[$max_size]["error_sum_index"] = 0;
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
        $hunter_summary[$i]["Brand Hunted"] += (int)($brand_count * $hunter_summary[$i]["project_weight"][$j]);
        $sql = 'SELECT COUNT(*) FROM '.$dbname.'.products a WHERE a.product_type ="sku" AND a.account_id = :account_id AND (a.product_creation_time >= :start_datetime AND a.product_creation_time <= :end_datetime) AND a.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $sku_count = $stmt->fetchColumn();
        if ($sku_count == NULL) {
            $sku_count = 0;
        }
        $hunter_summary[$i]["SKU Hunted"] += (int)($sku_count * $hunter_summary[$i]["project_weight"][$j]);
        $sql = 'SELECT COUNT(*) FROM '.$dbname.'.products a WHERE product_type ="dvc" AND a.account_id = :account_id AND (a.product_creation_time >= :start_datetime AND a.product_creation_time <= :end_datetime) AND a.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $dvc_count = $stmt->fetchColumn();
        if ($dvc_count == NULL) {
            $dvc_count = 0;
        }
        $hunter_summary[$i]["DVC Hunted"] += (int)($dvc_count * $hunter_summary[$i]["project_weight"][$j]);
        $sql = 'SELECT SUM(a.product_facing_count) FROM '.$dbname.'.products a WHERE a.account_id = :account_id AND (a.product_creation_time >= :start_datetime AND a.product_creation_time <= :end_datetime) AND a.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $facing_count = $stmt->fetchColumn();
        if ($facing_count == NULL) {
            $facing_count = 0;
        }
        $hunter_summary[$i]["Hunted Facing Count"] += (int)($facing_count * $hunter_summary[$i]["project_weight"][$j]);

        $sql = "SELECT COUNT(a.product_id) FROM ".$dbname.".products a INNER JOIN ".$dbname.".product_qa_errors b ON a.product_id = b.product_id WHERE a.account_id = :account_id AND a.product_qa_datetime >= :start_datetime AND a.product_qa_datetime <= :end_datetime AND a.product_status = 2";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $error_count = $stmt->fetchColumn();
        if ($error_count == NULL) {
            $error_count = 0;
        }
        $hunter_summary[$i]["QA Errors"] += (int)$error_count;

        $sql = "SELECT COUNT(*) FROM ".$dbname.".products a WHERE ( a.product_type = 'sku' OR a.product_type = 'brand' ) AND a.account_id = :account_id AND (a.product_previous IS NOT NULL) AND a.product_qa_datetime >= :start_datetime AND a.product_qa_datetime <= :end_datetime AND a.product_status = 2";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $rename_count = $stmt->fetchColumn();
        if ($rename_count == NULL) {
            $rename_count = 0;
        }
        $hunter_summary[$i]["Rename Errors"] += (int)$rename_count;

        $sql = 'SELECT COUNT(*) FROM '.$dbname.'.products a WHERE a.account_id = :account_id AND a.product_qa_status = "disapproved" AND a.product_qa_account_id IS NULL AND (a.product_creation_time >= :start_datetime AND a.product_creation_time <= :end_datetime) AND a.product_status = 2';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $system_errors = $stmt->fetchColumn();
        if ($system_errors == NULL) {
            $system_errors = 0;
        }
        $hunter_summary[$i]["System Errors"] += (int)$system_errors;


        $this_project_productivity = (($brand_count * 1.5) + ($sku_count * 1) + ($dvc_count * 0.5) + ($facing_count * 0.5)) * $hunter_summary[$i]["project_weight"][$j];
        $this_project_points = $this_project_productivity - ((($error_count + $system_errors) * 5) + $rename_count);
        $this_project_errors = $error_count + $system_errors;

        $this_project_rename = $rename_count;

        if ($hunter_summary[$i]["probe_processed_hunter_id"] == $_SESSION['id']) {
            $sql = "SELECT COUNT(a.product_id) as 'count', b.error_id FROM ".$dbname.".products a INNER JOIN ".$dbname.".product_qa_errors b ON a.product_id = b.product_id WHERE a.account_id = :account_id AND a.product_qa_datetime >= :start_datetime AND a.product_qa_datetime <= :end_datetime AND a.product_status = 2 GROUP BY b.error_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
            $rowCount = $stmt->rowCount();
            if ($rowCount > 0) {
                $error_summary = $stmt->fetchAll(PDO::FETCH_ASSOC);
                for($l = 0; $l < count($error_summary); $l++) {
                    $current_index = $hunter_summary[$i]["error_sum_index"];
                    $found = false;
                    for ($m = 0; $m < $current_index; $m++) {
                       if ($error_summary[$l]["error_id"] == $error_chart[$m]["error_id"]) {
                           $error_chart[$m]["count"] += $error_summary[$l]["count"];
                           $found = true;
                           break;
                       }
                    }
                    if(!$found) {
                        $sql = 'SELECT a.project_error_name FROM '.$dbname.'.project_errors a WHERE a.project_error_id = :error_id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(['error_id'=>(int)$error_summary[$l]["error_id"]]);
                        $error_info = $stmt->fetch(PDO::FETCH_OBJ);
                        $error_chart[$current_index]["error_name"] = $error_info->project_error_name;
                        $error_chart[$current_index]["error_id"] = (int)$error_summary[$l]["error_id"];
                        $error_chart[$current_index]["count"] = (int)$error_summary[$l]["count"];
                        $hunter_summary[$i]["error_sum_index"]++;
                    }
                }
            }
        }

        switch ($hunter_summary[$i]["project_region"][$j]) {
            case 'AMER' : 
                $hunter_summary[$i]["AMER"]++; 
                $project_summary["AMER"]["productivity"] += $this_project_productivity;
                $project_summary["AMER"]["points"] += $this_project_points;
                $project_summary["AMER"]["error_count"] += $this_project_errors;
                $project_summary["AMER"]["rename_count"] += $this_project_rename;
                break;
            case 'EMEA' : 
                $hunter_summary[$i]["EMEA"]++; 
                $project_summary["EMEA"]["productivity"] += $this_project_productivity;
                $project_summary["EMEA"]["points"] += $this_project_points;
                $project_summary["EMEA"]["error_count"] += $this_project_errors;
                $project_summary["EMEA"]["rename_count"] += $this_project_rename;
                break;
            case 'APAC' : 
                $hunter_summary[$i]["APAC"]++; 
                $project_summary["APAC"]["productivity"] += $this_project_productivity;
                $project_summary["APAC"]["points"] += $this_project_points;
                $project_summary["APAC"]["error_count"] += $this_project_errors;
                $project_summary["APAC"]["rename_count"] += $this_project_rename;
                break;
            case 'DPG' : 
                $hunter_summary[$i]["DPG"]++; 
                $project_summary["DPG"]["productivity"] += $this_project_productivity;
                $project_summary["DPG"]["points"] += $this_project_points;
                $project_summary["DPG"]["error_count"] += $this_project_errors;
                $project_summary["DPG"]["rename_count"] += $this_project_rename;
            break;
        }
        $pdo = NULL;
    }


    $total_count = ($hunter_summary[$i]["Brand Hunted"] * 1.5)  + $hunter_summary[$i]["SKU Hunted"] + (($hunter_summary[$i]["DVC Hunted"] + $hunter_summary[$i]["Hunted Facing Count"]) / 2);
    $hunter_summary[$i]["productivity"] = (int)$total_count;
    $points = $total_count - ((($hunter_summary[$i]["QA Errors"] + $hunter_summary[$i]["System Errors"])  * 5) + $hunter_summary[$i]["Rename Errors"]);
    $hunter_summary[$i]["Points"] = (int)$points;
    if ($total_count == 0) {
        $monthly_accuracy = 0;
        $rename_accuracy = 0;
    } else {
        $monthly_accuracy = round(((($total_count - ($hunter_summary[$i]["QA Errors"] + $hunter_summary[$i]["System Errors"] * 1) )/ $total_count) * 100),2);
        if ($monthly_accuracy == NULL || is_nan($monthly_accuracy)) {
            $monthly_accuracy = 0;
        }
        $rename_accuracy = round(((($total_count - ($hunter_summary[$i]["Rename Errors"] * 1) )/ $total_count) * 100),2);
        if ($rename_accuracy == NULL || is_nan($rename_accuracy)) {
            $rename_accuracy = 0;
        }
    }   
    $hunter_summary[$i]["Accuracy"] = $monthly_accuracy;
    $hunter_summary[$i]["rename_accuracy"] = $rename_accuracy;

}
if ($project_summary["AMER"]["productivity"] != 0 ) {
    $project_summary["AMER"]["accuracy"] = round((($project_summary["AMER"]["productivity"] - $project_summary["AMER"]["error_count"]) / $project_summary["AMER"]["productivity"] * 100),2);
    $project_summary["AMER"]["rename_accuracy"] = round((($project_summary["AMER"]["productivity"] - $project_summary["AMER"]["rename_count"]) / $project_summary["AMER"]["productivity"] * 100),2);
} else {
    $project_summary["AMER"]["accuracy"] = 0; 
    $project_summary["AMER"]["rename_accuracy"] = 0; 
}

if ($project_summary["EMEA"]["productivity"] != 0 ) {
    $project_summary["EMEA"]["accuracy"] = round((($project_summary["EMEA"]["productivity"] - $project_summary["EMEA"]["error_count"]) / $project_summary["EMEA"]["productivity"] * 100),2);
    $project_summary["EMEA"]["rename_accuracy"] = round((($project_summary["EMEA"]["productivity"] - $project_summary["EMEA"]["rename_count"]) / $project_summary["EMEA"]["productivity"] * 100),2);
} else {
    $project_summary["EMEA"]["accuracy"] = 0; 
    $project_summary["EMEA"]["rename_accuracy"] = 0; 
}
if ($project_summary["APAC"]["productivity"] != 0 ) {
    $project_summary["APAC"]["accuracy"] = round((($project_summary["APAC"]["productivity"] - $project_summary["APAC"]["error_count"]) / $project_summary["APAC"]["productivity"] * 100),2);
    $project_summary["APAC"]["rename_accuracy"] = round((($project_summary["APAC"]["productivity"] - $project_summary["APAC"]["rename_count"]) / $project_summary["APAC"]["productivity"] * 100),2);
} else {
    $project_summary["APAC"]["accuracy"] = 0; 
    $project_summary["APAC"]["rename_accuracy"] = 0; 
}
if ($project_summary["DPG"]["productivity"] != 0 ) {
    $project_summary["DPG"]["accuracy"] = round((($project_summary["DPG"]["productivity"] - $project_summary["DPG"]["error_count"]) / $project_summary["DPG"]["productivity"] * 100),2);
    $project_summary["DPG"]["rename_accuracy"] = round((($project_summary["DPG"]["productivity"] - $project_summary["DPG"]["rename_count"]) / $project_summary["DPG"]["productivity"] * 100),2);
} else {
    $project_summary["DPG"]["accuracy"] = 0; 
    $project_summary["DPG"]["rename_accuracy"] = 0; 
}

usort($hunter_summary, "custom_sort");
usort($project_summary, "custom_sort_projects");
// Define the custom sort function
function custom_sort($a,$b) {
    return $a['Points']< $b['Points'];
}
function custom_sort_projects($a,$b) {
    return $a['points']< $b['points'];
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
for($i = 0; $i < count($project_summary); $i++) {
    $project_summary[$i]["Rank"] = $i + 1;
}
$return_arr[] = array("warning"=>$warning, "hunter_summary"=>$hunter_summary, "current_info"=>$hunter_summary[$key], "total"=>count($hunter_summary), "project_summary"=>$project_summary, "error_chart"=>$error_chart);
echo json_encode($return_arr);
?>
