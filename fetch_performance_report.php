<?php
/*
Author: Malika Liyanage
*/
set_time_limit(0);
ignore_user_abort(1);
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
$dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
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
    echo "<p>Connection to database failed<br>Reason: " . $e->getMessage() . '</p>';
    exit();
}
// getting the list of projects from the database
$sql = 'SELECT project_db_name, project_region, project_language FROM `project_db`.projects WHERE 1';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$project_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
$begin = new DateTime(strval($_POST['start_datetime']));
$end = new DateTime(strval($_POST['end_datetime']));
$end_date_for_loop = $end->modify('+1 day');
$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($begin, $interval, $end_date_for_loop);
// this will be used to store the report which will be sent back
$summary = array();
$count = 0;
// selecting the list of hunters from the user database
$sql = 'SELECT a.account_id ,a.account_gid, CONCAT(a.account_first_name, " ", a.account_last_name) as "name" 
    FROM user_db.accounts a 
        INNER JOIN user_db.account_designations b 
            ON a.account_id = b.account_id
    WHERE
        b.designation_id = 3';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$hunter_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
// closing pdo connection
$pdo = NULL;
foreach ($hunter_array as $hunter) {
    // initializing report fields
    $summary[$count] = array (
        "GID" => $hunter[account_gid],
        "Hunter Name" => $hunter[name],
        "QA Accuracy" => 0,
        "Naming Accuracy" => 0,
        "Average Accuracy" => 0,
        "Region" => '',
        "# of Days with Product Count between 30 & 35" => 0,
        "# of Days with Product Count with 35 & above" => 0,
        "Checked Probes" => 0,
        "Checked Radar Links" => 0,
        "Checked References" => 0,
        "# Of Worked Days" => 0,
        "Total Productivity" => 0,
        "AMER" => 0,
        "APAC" => 0,
        "DPG" => 0,
        "EMEA" => 0,
        "total_count" => 0,
        "total_error_count" => 0,
        "rename_count" => 0,
    );
    $date_count = 0;
    foreach ($period as $dt) {
        $date = $dt->format("Y-m-d");
        $date_productivity = 0;
        $date_errors = 0;
        $date_rename_errors = 0;
        $processed_count = 0;
        foreach ($project_array as $project) {
            $dbname = $project[project_db_name];
            $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
            $pdo = new PDO($dsn, $user, $pwd);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this_project_weight = 1;
            $this_project_productivity = 0;
            $this_project_errors = 0;
            $this_project_rename_errors = 0;
            if ($date_count == 0) {
                $sql = 'SELECT COUNT(*) FROM probe WHERE probe.probe_processed_hunter_id = :id AND (probe.probe_hunter_processed_time >= :start_datetime AND probe.probe_hunter_processed_time <= :end_datetime)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['id' => $hunter[account_id], 'start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
                $probe_count = $stmt->fetchColumn();
                $sql = 'SELECT COUNT(*) FROM radar_sources WHERE radar_sources.account_id = :id AND (radar_sources.creation_time >= :start_datetime AND radar_sources.creation_time <= :end_datetime)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['id' => $hunter[account_id], 'start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
                $radar_count = $stmt->fetchColumn();
                $sql = 'SELECT COUNT(*) FROM reference_info WHERE reference_info.reference_processed_hunter_id = :id AND (reference_info.reference_hunter_processed_time >= :start_datetime AND reference_info.reference_hunter_processed_time <= :end_datetime)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['id' => $hunter[account_id], 'start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
                $ref_count = $stmt->fetchColumn();
                $summary[$count]['Checked Probes'] += $probe_count;
                $summary[$count]['Checked Radar Links'] += $radar_count;
                $summary[$count]['Checked References'] += $ref_count;
            }

            $sql = 'SELECT COUNT(*) FROM probe WHERE probe.probe_processed_hunter_id = :id AND DATE(probe.probe_hunter_processed_time) = :date';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id'=>$hunter[account_id],'date'=>$date]);
            $processed_count += $stmt->fetchColumn();

            $sql = 'SELECT COUNT(*) FROM radar_sources WHERE radar_sources.account_id = :id AND DATE(radar_sources.creation_time) = :date';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id'=>$hunter[account_id],'date'=>$date]);
            $processed_count += $stmt->fetchColumn();

            $sql = 'SELECT COUNT(*) FROM reference_info WHERE reference_info.reference_processed_hunter_id = :id AND DATE(reference_info.reference_hunter_processed_time) = :date ';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id'=>$hunter[account_id],'date'=>$date]);
            $processed_count += $stmt->fetchColumn();


            $sql = 'SELECT COUNT(*) FROM products p WHERE p.account_id = :id AND (p.product_creation_time >= :start_datetime AND p.product_creation_time <= :end_datetime)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id'=>$hunter[account_id],'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
            $product_count = $stmt->fetchColumn();
            if ($product_count > 0) {
                if ($project["project_language"] == "english") {
                    $this_project_weight = 1;
                } else if ($project["project_language"] == "non_english") {
                    $this_project_weight = 2;
                }
                $sql = 'SELECT COUNT(*) FROM ' . $dbname . '.products a WHERE a.product_type ="brand" AND a.account_id = :account_id AND (DATE(a.product_creation_time) = :date) AND a.product_status = 2';
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['account_id' => $hunter[account_id], 'date' => $date]);
                $brand_count = $stmt->fetchColumn();
                if ($brand_count == NULL) {
                    $brand_count = 0;
                }
                $sql = 'SELECT COUNT(*) FROM ' . $dbname . '.products a WHERE a.product_type ="sku" AND a.account_id = :account_id AND (DATE(a.product_creation_time) = :date) AND a.product_status = 2';
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['account_id' => $hunter[account_id], 'date' => $date]);
                $sku_count = $stmt->fetchColumn();
                if ($sku_count == NULL) {
                    $sku_count = 0;
                }
                $sql = 'SELECT COUNT(*) FROM ' . $dbname . '.products a WHERE a.product_type ="dvc" AND a.account_id = :account_id AND (DATE(a.product_creation_time) = :date) AND a.product_status = 2';
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['account_id' => $hunter[account_id], 'date' => $date]);
                $dvc_count = $stmt->fetchColumn();
                if ($dvc_count == NULL) {
                    $dvc_count = 0;
                }
                $sql = 'SELECT SUM(a.product_facing_count) FROM ' . $dbname . '.products a WHERE a.account_id = :account_id AND (DATE(a.product_creation_time) = :date) AND a.product_status = 2';
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['account_id' => $hunter[account_id], 'date' => $date]);
                $facing_count = $stmt->fetchColumn();
                if ($facing_count == NULL) {
                    $facing_count = 0;
                }
                $sql = "SELECT COUNT(a.product_id) FROM " . $dbname . ".products a INNER JOIN " . $dbname . ".product_qa_errors b ON a.product_id = b.product_id WHERE a.account_id = :account_id AND DATE(a.product_qa_datetime) = :date AND a.product_status = 2";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['account_id' => $hunter[account_id], 'date' => $date]);
                $error_count = $stmt->fetchColumn();
                if ($error_count == NULL) {
                    $error_count = 0;
                }
                $sql = 'SELECT COUNT(*) FROM ' . $dbname . '.products a WHERE a.account_id = :account_id AND a.product_qa_status = "disapproved" AND a.product_qa_account_id IS NULL AND (DATE(a.product_creation_time) = :date) AND a.product_status = 2';
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['account_id' => $hunter[account_id], 'date' => $date]);
                $system_errors = $stmt->fetchColumn();
                if ($system_errors == NULL) {
                    $system_errors = 0;
                }
                $sql = "SELECT COUNT(*) FROM ".$dbname.".products a WHERE ( a.product_type = 'sku' OR a.product_type = 'brand' ) AND a.account_id = :account_id AND (a.product_previous IS NOT NULL) AND (DATE(a.product_qa_datetime)= :date)  AND a.product_status = 2";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['account_id' => $hunter[account_id], 'date' => $date]);
                $rename_count = $stmt->fetchColumn();
                if ($rename_count == NULL) {
                    $rename_count = 0;
                }
                $this_project_productivity = (($brand_count * 1.5) + ($sku_count) + (($dvc_count + $facing_count) / 2)) * $this_project_weight;
                $this_project_errors = $error_count + $system_errors;
                $this_project_rename_errors = $rename_count;
                $date_productivity += $this_project_productivity;
                $date_errors += $this_project_errors;
                $date_rename_errors += $this_project_rename_errors;
                // this will be used for determining the hunters region
                if ($this_project_productivity > 0) {
                    switch ($project['project_region']) {
                        case 'AMER':
                            $summary[$count]['AMER']++;
                            break;
                        case 'EMEA':
                            $summary[$count]['EMEA']++;
                            break;
                        case 'APAC':
                            $summary[$count]['APAC']++;
                            break;
                        case 'DPG':
                            $summary[$count]['DPG']++;
                            break;
                    }
                }
            }
            $pdo = NULL;
        }
        $date_count++;
        if ($date_productivity > 0 || $processed_count > 0) {
            $summary[$count]['# Of Worked Days']++;
        }
        if ($date_productivity >= 30 && $date_productivity < 35) {
            $summary[$count]['# of Days with Product Count between 30 & 35']++;
        }
        if ($date_productivity >= 35) {
            $summary[$count]['# of Days with Product Count with 35 & above']++;
        }
        $date_array = array(
            $date => $date_productivity,
        );
        // adding the dates column to the reports
        $summary[$count] = array_merge($summary[$count], $date_array);
        $summary[$count]['total_count'] += $date_productivity;
        $summary[$count]['total_error_count'] += $date_errors;
        $summary[$count]['rename_count'] += $date_rename_errors;
    }
    $summary[$count]['QA Accuracy'] = round((($summary[$count]['total_count'] - $summary[$count]['total_error_count']) / $summary[$count]['total_count'] * 100) ,2) .' %';
    $summary[$count]['Naming Accuracy'] = round((($summary[$count]['total_count'] - $summary[$count]['rename_count']) / $summary[$count]['total_count'] * 100) ,2) .' %';
    $summary[$count]['Average Accuracy'] = ( $summary[$count]['QA Accuracy'] + $summary[$count]['Naming Accuracy'] )/ 2;
    $max = $summary[$count]['AMER'];
    $region = 'AMER';
    if ($max < $summary[$count]['EMEA']) {
        $region = 'EMEA';
    } else if ($max < $summary[$count]['DPG']) {
        $region = 'DPG';
    } else if ($max < $summary[$count]['APAC']) {
        $region = 'APAC';
    }
    $summary[$count]['Total Productivity'] = $summary[$count]['total_count'];
    $summary[$count]['Region'] = $region;
    unset($summary[$count]['AMER']);
    unset($summary[$count]['EMEA']);
    unset($summary[$count]['DPG']);
    unset($summary[$count]['APAC']);
    unset($summary[$count]['total_count']);
    unset($summary[$count]['total_error_count']);
    unset($summary[$count]['rename_count']);
    $count++;
}
$return_arr[] = array("report" => $summary);
echo json_encode($return_arr);