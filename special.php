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

/*
$hunter_summary = array();
$sql = 'SELECT project_name, project_db_name, project_region FROM projects WHERE project_region = "EMEA"';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$project_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
$max_size = 0;
for ($i = 0; $i < count($project_array); $i++) {
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
            $hunter_summary[$max_size]["name"] = $user_information->name;
            $hunter_summary[$max_size]["account_gid"] = $this_project_hunters[$j]["account_gid"];
            $hunter_summary[$max_size]["project_count"] = 0;
            $hunter_summary[$max_size]["rename_error_count"] = 0;
            $project_count = $hunter_summary[$max_size]["project_count"];
            $hunter_summary[$max_size]["projects"][$project_count] = $dbname;
            $hunter_summary[$max_size]["project_region"][$project_count] = $project_array[$i]["project_region"];
            $hunter_summary[$max_size]["project_count"]++;
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
        $sql = 'SELECT COUNT(a.product_id) 
        FROM '.$dbname.'.`product_qa_errors` a 
        INNER JOIN '.$dbname.'.project_errors b ON a.`error_id` = b.project_error_id 
        INNER JOIN '.$dbname.'.products c ON c.product_id = a.product_id 
        WHERE b.project_error_name LIKE "%Name%" OR b.project_error_name LIKE "%Naming%" AND c.account_id = :account_id
        AND (c.product_qa_datetime >= :start_datetime AND c.product_qa_datetime <= :end_datetime)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$hunter_summary[$i][probe_processed_hunter_id], 'start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
        $error_count = $stmt->fetchColumn();
        $hunter_summary[$i]["rename_error_count"] += $error_count;
    }
    unset($hunter_summary[$i][probe_processed_hunter_id]);
    unset($hunter_summary[$i][project_count]);
    unset($hunter_summary[$i][projects]);
    unset($hunter_summary[$i][project_region]);
}
*/
/*
$sql = 'SELECT project_name, project_db_name, project_region FROM projects';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$project_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
$count = 0;

foreach ($project_info as $project) {
    $dbname = $project["project_db_name"];
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
    $pdo = new PDO($dsn, $user, $pwd);

    $sql = 'SELECT p.product_id, p.product_name, p.product_hunt_type, p.product_facing_count, p.product_creation_time , p.product_qa_status FROM products p WHERE p.product_type = "facing"';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $product_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($product_info as $product) {
        $summary[$count] = array(
            'Project Name' => $project['project_name'],
            'Region' => $project['project_region'],
            'Product Name' => $product['product_name'],
            'Facing Count' => $product['product_facing_count'],
            'Product Creation Date' => $product['product_creation_time'],
            'Product Status' => $product['product_qa_status']
        );

        $count++;
    }
}
*/
$sql = 'SELECT project_name, project_db_name, project_region FROM projects';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$project_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
$count = 0;

foreach ($project_info as $project) {
    $dbname = $project["project_db_name"];
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
    $pdo = new PDO($dsn, $user, $pwd);
    $report[$count] = array(
        "project_name" => $project["project_name"],
        "probes" => 0,
        "radar" => 0,
        "ref" => 0,
        "probe_brand" => 0,
        "probe_sku" => 0,
        "radar_brand" => 0,
        "radar_sku" => 0,
        "ref_brand" => 0,
        "ref_sku" => 0,
        "probe_alloc_hunters" => 0,
        "radar_alloc_hunters" => 0,
        "ref_alloc_hunters" => 0,
        "days_taken_probe" => 0,
        "days_taken_radar" => 0,
        "days_taken_ref" => 0,
    );

    $sql = 'SELECT COUNT(*) FROM probe p WHERE p.probe_hunter_processed_time >= :start_datetime AND p.probe_hunter_processed_time <= :end_datetime';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => $_POST['start_datetime'], 'end_datetime' => $_POST['end_datetime']]);
    $report[$count]["probes"] = $stmt->fetchColumn();

    $sql = 'SELECT COUNT(*) FROM radar_sources rs WHERE rs.creation_time >= :start_datetime AND rs.creation_time <= :end_datetime';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => $_POST['start_datetime'], 'end_datetime' => $_POST['end_datetime']]);
    $report[$count]["radar"] = $stmt->fetchColumn();

    $sql = 'SELECT COUNT(*) FROM reference_info ri WHERE ri.reference_hunter_processed_time >= :start_datetime AND ri.reference_hunter_processed_time <= :end_datetime';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => $_POST['start_datetime'], 'end_datetime' => $_POST['end_datetime']]);
    $report[$count]["ref"] = $stmt->fetchColumn();

    $sql = '
    SELECT
        SUM(CASE WHEN p.product_hunt_type = "probe" AND p.product_type = "sku" THEN 1 ELSE 0 END) as "probe_sku",
    	SUM(CASE WHEN p.product_hunt_type = "probe" AND p.product_type = "brand" THEN 1 ELSE 0 END) as "probe_brand",
        SUM(CASE WHEN p.product_hunt_type = "radar" AND p.product_type = "brand" THEN 1 ELSE 0 END) as "radar_brand",
        SUM(CASE WHEN p.product_hunt_type = "radar" AND p.product_type = "sku" THEN 1 ELSE 0 END) as "radar_sku",
        SUM(CASE WHEN p.product_hunt_type = "reference" AND p.product_type = "brand" THEN 1 ELSE 0 END) as "ref_brand",
        SUM(CASE WHEN p.product_hunt_type = "reference" AND p.product_type = "sku" THEN 1 ELSE 0 END) as "ref_sku"
    FROM
	    products p
    WHERE
        p.product_creation_time >= :start_datetime AND p.product_creation_time <= :end_datetime
    ';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => $_POST['start_datetime'], 'end_datetime' => $_POST['end_datetime']]);
    $prod_info = $stmt->fetch(PDO::FETCH_ASSOC);

    $report[$count]["probe_brand"] = $prod_info["probe_brand"];
    $report[$count]["probe_sku"] = $prod_info["probe_sku"];
    $report[$count]["radar_brand"] = $prod_info["radar_brand"];
    $report[$count]["radar_sku"] = $prod_info["radar_sku"];
    $report[$count]["ref_brand"] = $prod_info["ref_brand"];
    $report[$count]["ref_sku"] = $prod_info["ref_sku"];

    $sql = '
    SELECT 
        COUNT(DISTINCT (id))
    FROM
        (SELECT 
            p.probe_processed_hunter_id AS  "id", COUNT(*) AS "count"
        FROM
            probe p
        WHERE
            p.probe_hunter_processed_time >= :start_datetime AND p.probe_hunter_processed_time <= :end_datetime
        GROUP BY 1) t
    WHERE
        id IS NOT NULL';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => $_POST['start_datetime'], 'end_datetime' => $_POST['end_datetime']]);
    $report[$count]["probe_alloc_hunters"] = $stmt->fetchColumn();

    $sql = '
    SELECT 
        COUNT(DISTINCT (id))
    FROM
        (SELECT 
            rs.account_id AS  "id", COUNT(*) AS "count"
        FROM
            radar_sources rs
        WHERE
            rs.creation_time >= :start_datetime AND rs.creation_time <= :end_datetime
        GROUP BY 1) t
    WHERE
        id IS NOT NULL';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => $_POST['start_datetime'], 'end_datetime' => $_POST['end_datetime']]);
    $report[$count]["radar_alloc_hunters"] = $stmt->fetchColumn();

    $sql = '
    SELECT 
        COUNT(DISTINCT (id))
    FROM
        (SELECT 
            ri.reference_processed_hunter_id AS  "id"
        FROM
            reference_info ri
        WHERE
            ri.reference_hunter_processed_time >= :start_datetime AND ri.reference_hunter_processed_time <= :end_datetime
        GROUP BY 1) t
    WHERE
        id IS NOT NULL
    ';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => $_post['start_datetime'], 'end_datetime' => $_post['end_datetime']]);
    $report[$count]["ref_alloc_hunters"] = $stmt->fetchcolumn();

    $sql = '
    SELECT SUM(days) FROM (
        SELECT
		    p.probe_ticket_id,
            (5 * (DATEDIFF(MAX(p.probe_hunter_processed_time), MIN(p.probe_hunter_processed_time)) DIV 7) + 
            MID(\'0123444401233334012222340111123400001234000123440\', 7 * WEEKDAY(MIN(p.probe_hunter_processed_time)) + WEEKDAY(MAX(p.probe_hunter_processed_time)) + 1, 1)) AS "days"
        FROM
            probe p
        WHERE
            p.probe_hunter_processed_time >= :start_datetime AND p.probe_hunter_processed_time <= :end_datetime
	    GROUP BY 1
    ) t ';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => $_POST['start_datetime'], 'end_datetime' => $_POST['end_datetime']]);
    $report[$count]["days_taken_probe"] = $stmt->fetchcolumn();

    $sql = '
    SELECT SUM(days) FROM (
        SELECT
		    rh.radar_ticket_id,
            (5 * (DATEDIFF(MAX(rs.creation_time), MIN(rs.creation_time)) DIV 7) + 
            MID(\'0123444401233334012222340111123400001234000123440\', 7 * WEEKDAY(MIN(rs.creation_time)) + WEEKDAY(MAX(rs.creation_time)) + 1, 1)) AS "days"
        FROM
            radar_sources rs
            INNER JOIN radar_hunt rh on rs.radar_hunt_id = rh.radar_hunt_id
        WHERE
            rs.creation_time >= :start_datetime AND rs.creation_time <= :end_datetime
	    GROUP BY 1
    ) t ';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => $_POST['start_datetime'], 'end_datetime' => $_POST['end_datetime']]);
    $report[$count]["days_taken_radar"] = $stmt->fetchcolumn();

    $sql = '
    SELECT SUM(days) FROM (
        SELECT
		    ri.reference_ticket_id,
            (5 * (DATEDIFF(MAX(ri.reference_hunter_processed_time), MIN(ri.reference_hunter_processed_time)) DIV 7) + 
            MID(\'0123444401233334012222340111123400001234000123440\', 7 * WEEKDAY(MIN(ri.reference_hunter_processed_time)) + WEEKDAY(MAX(ri.reference_hunter_processed_time)) + 1, 1)) AS "days"
        FROM
            reference_info ri
        WHERE
            ri.reference_hunter_processed_time >= :start_datetime AND ri.reference_hunter_processed_time <= :end_datetime
	    GROUP BY 1
    ) t ';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => $_POST['start_datetime'], 'end_datetime' => $_POST['end_datetime']]);
    $report[$count]["days_taken_ref"] = $stmt->fetchcolumn();


    $count++;
}

/*
$project_info_radar = $project_info;
$project_info_ref = $project_info;
$project_info_dates = $project_info;
for ($i = 0; $i < count($project_info); $i++) {
    $dbname = $project_info[$i]["project_db_name"];
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
    $pdo = new PDO($dsn, $user, $pwd);

    $sql = 'SELECT COUNT(DISTINCT(probe_processed_hunter_id)) FROM probe WHERE probe_hunter_processed_time >= :start_datetime AND probe_hunter_processed_time <= :end_datetime';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => $_POST['start_datetime'], 'end_datetime' => $_POST['end_datetime']]);
    $hunter_count = $stmt->fetchColumn();
    $project_info[$i]["Hunter Count"] = $hunter_count;

    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="brand" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2 AND products.product_hunt_type = "probe"';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
    $brand_count = $stmt->fetchColumn();
    $project_info[$i]["Brands Hunted"] = $brand_count;
    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="sku" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2 AND products.product_hunt_type = "probe"';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
    $sku_count = $stmt->fetchColumn();
    $project_info[$i]["SKUs Hunted"] = $sku_count;
    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="dvc" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2 AND products.product_hunt_type = "probe"';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
    $dvc_count = $stmt->fetchColumn();
    $project_info[$i]["DVCs Hunted"] = $dvc_count;
    $sql = 'SELECT SUM(product_facing_count) FROM products WHERE (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2 AND products.product_hunt_type = "probe"';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
    $facing_count = $stmt->fetchColumn();
    if ($facing_count == null) {
        $facing_count = 0;
    }
    $project_info[$i]["Facing Count"] = $facing_count;

    $sql = 'SELECT COUNT(*) FROM probe WHERE (probe.probe_hunter_processed_time >= :start_datetime AND probe.probe_hunter_processed_time <= :end_datetime)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
    $checked_count = $stmt->fetchColumn();
    $project_info[$i]["Checked Probe Count"] = $checked_count;

    $sql = 'SELECT COUNT(*) FROM probe WHERE (probe.probe_hunter_processed_time >= :start_datetime AND probe.probe_hunter_processed_time <= :end_datetime) AND probe.probe_status_id = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
    $checked_count = $stmt->fetchColumn();
    $project_info[$i]["Hunted Probe Count"] = $checked_count;

    $sql = 'SELECT COUNT(DISTINCT DATE(probe_hunter_processed_time)) FROM probe WHERE probe_hunter_processed_time >= :start_datetime AND probe_hunter_processed_time <= :end_datetime';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => $_POST['start_datetime'], 'end_datetime' => $_POST['end_datetime']]);
    $date_count = $stmt->fetchColumn();
    $project_info[$i]["Days Worked"] = $date_count;

    $sql = 'SELECT COUNT(DISTINCT(probe_ticket_id)) FROM probe WHERE probe_hunter_processed_time >= :start_datetime AND probe_hunter_processed_time <= :end_datetime';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => $_POST['start_datetime'], 'end_datetime' => $_POST['end_datetime']]);
    $date_count = $stmt->fetchColumn();
    $project_info[$i]["Tickets"] = $date_count;

    unset($project_info[$i]["project_db_name"]);
}


for ($i = 0; $i < count($project_info_radar); $i++) {
    $dbname = $project_info_radar[$i]["project_db_name"];
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
    $pdo = new PDO($dsn, $user, $pwd);

    $sql = 'SELECT COUNT(DISTINCT(account_id)) FROM radar_sources WHERE creation_time >= :start_datetime AND creation_time <= :end_datetime';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => $_POST['start_datetime'], 'end_datetime' => $_POST['end_datetime']]);
    $hunter_count = $stmt->fetchColumn();
    $project_info_radar[$i]["Hunter Count"] = $hunter_count;

    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="brand" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2 AND products.product_hunt_type = "radar"';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
    $brand_count = $stmt->fetchColumn();
    $project_info_radar[$i]["Brands Hunted"] = $brand_count;

    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="sku" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2 AND products.product_hunt_type = "radar"';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
    $sku_count = $stmt->fetchColumn();
    $project_info_radar[$i]["SKUs Hunted"] = $sku_count;

    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="dvc" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2 AND products.product_hunt_type = "radar"';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
    $dvc_count = $stmt->fetchColumn();
    $project_info_radar[$i]["DVCs Hunted"] = $dvc_count;

    $sql = 'SELECT SUM(product_facing_count) FROM products WHERE (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2 AND products.product_hunt_type = "radar"';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
    $facing_count = $stmt->fetchColumn();
    if ($facing_count == null) {
        $facing_count = 0;
    }
    $project_info_radar[$i]["Facing Count"] = $facing_count;

    $sql = 'SELECT COUNT(*) FROM radar_sources WHERE (radar_sources.creation_time >= :start_datetime AND radar_sources.creation_time <= :end_datetime)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
    $checked_count = $stmt->fetchColumn();
    $project_info_radar[$i]["Checked Suggestion Count"] = $checked_count;

    $sql = 'SELECT COUNT(*) FROM radar_sources WHERE (radar_sources.creation_time >= :start_datetime AND radar_sources.creation_time <= :end_datetime) AND radar_sources.radar_status_id = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
    $checked_count = $stmt->fetchColumn();
    $project_info_radar[$i]["Hunted Suggestion Count"] = $checked_count;

    $sql = 'SELECT COUNT(DISTINCT DATE(creation_time)) FROM radar_sources WHERE creation_time >= :start_datetime AND creation_time <= :end_datetime';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => $_POST['start_datetime'], 'end_datetime' => $_POST['end_datetime']]);
    $date_count = $stmt->fetchColumn();
    $project_info_radar[$i]["Days Worked"] = $date_count;

    $sql = 'SELECT COUNT(DISTINCT(radar_ticket_id)) FROM radar_hunt WHERE radar_processed_time >= :start_datetime AND radar_processed_time <= :end_datetime';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => $_POST['start_datetime'], 'end_datetime' => $_POST['end_datetime']]);
    $date_count = $stmt->fetchColumn();
    $project_info_radar[$i]["Tickets"] = $date_count;

    unset($project_info_radar[$i]["project_db_name"]);
}

for ($i = 0; $i < count($project_info_ref); $i++) {
    $dbname = $project_info_ref[$i]["project_db_name"];
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
    $pdo = new PDO($dsn, $user, $pwd);

    $sql = 'SELECT COUNT(DISTINCT(reference_processed_hunter_id)) FROM reference_info WHERE reference_hunter_processed_time >= :start_datetime AND reference_hunter_processed_time <= :end_datetime';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => $_POST['start_datetime'], 'end_datetime' => $_POST['end_datetime']]);
    $hunter_count = $stmt->fetchColumn();
    $project_info_ref[$i]["Hunter Count"] = $hunter_count;

    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="brand" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2 AND products.product_hunt_type = "reference"';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
    $brand_count = $stmt->fetchColumn();
    $project_info_ref[$i]["Brands Hunted"] = $brand_count;

    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="sku" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2 AND products.product_hunt_type = "reference"';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
    $sku_count = $stmt->fetchColumn();
    $project_info_ref[$i]["SKUs Hunted"] = $sku_count;

    $sql = 'SELECT COUNT(*) FROM products WHERE product_type ="dvc" AND (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2 AND products.product_hunt_type = "reference"';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
    $dvc_count = $stmt->fetchColumn();
    $project_info_ref[$i]["DVCs Hunted"] = $dvc_count;

    $sql = 'SELECT SUM(product_facing_count) FROM products WHERE (products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime) AND products.product_status = 2 AND products.product_hunt_type = "reference"';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
    $facing_count = $stmt->fetchColumn();
    if ($facing_count == null) {
        $facing_count = 0;
    }
    $project_info_ref[$i]["Facing Count"] = $facing_count;

    $sql = 'SELECT COUNT(*) FROM reference_info WHERE (reference_hunter_processed_time >= :start_datetime AND reference_hunter_processed_time <= :end_datetime)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
    $checked_count = $stmt->fetchColumn();
    $project_info_ref[$i]["Checked Reference Count"] = $checked_count;

    $sql = 'SELECT COUNT(*) FROM reference_info WHERE (reference_hunter_processed_time >= :start_datetime AND reference_hunter_processed_time <= :end_datetime) AND reference_status_id = 2';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => strval($_POST['start_datetime']), 'end_datetime' => strval($_POST['end_datetime'])]);
    $checked_count = $stmt->fetchColumn();
    $project_info_ref[$i]["Hunted Reference Count"] = $checked_count;

    $sql = 'SELECT COUNT(DISTINCT DATE(reference_hunter_processed_time)) FROM reference_info WHERE reference_hunter_processed_time >= :start_datetime AND reference_hunter_processed_time <= :end_datetime';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => $_POST['start_datetime'], 'end_datetime' => $_POST['end_datetime']]);
    $date_count = $stmt->fetchColumn();
    $project_info_ref[$i]["Days Worked"] = $date_count;

    $sql = 'SELECT COUNT(DISTINCT(reference_ticket_id)) FROM reference_info WHERE reference_hunter_processed_time >= :start_datetime AND reference_hunter_processed_time <= :end_datetime';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime' => $_POST['start_datetime'], 'end_datetime' => $_POST['end_datetime']]);
    $date_count = $stmt->fetchColumn();
    $project_info_ref[$i]["Tickets"] = $date_count;

    unset($project_info_ref[$i]["project_db_name"]);
}

$begin = new DateTime(strval($_POST['start_datetime']));
$end = new DateTime(strval($_POST['end_datetime']));
$end_date_for_loop = $end->modify('+1 day');

$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($begin, $interval, $end_date_for_loop);

$count = 0;
$summary = array();
foreach ($period as $dt) {
    $date = $dt->format("Y-m-d");
    $summary[$count] = array(
        "Date" => array(),
        "AMER" => 'NA',
        "EMEA" => 'NA',
        "APAC" => 'NA',
        "DPG" => 'NA',
    );
    $date_project_summary = array(
        "AMER" => array(
            "name" => "AMER",
            "probe" => 0,
            "radar" => 0,
            "ref" => 0
        ),
        "EMEA" => array(
            "name" => "EMEA",
            "probe" => 0,
            "radar" => 0,
            "ref" => 0
        ),
        "DPG" => array(
            "name" => "DPG",
            "probe" => 0,
            "radar" => 0,
            "ref" => 0
        ),
        "APAC" => array(
            "name" => "APAC",
            "probe" => 0,
            "radar" => 0,
            "ref" => 0
        )
    );

    $summary[$count]["Date"] = $date;
    for ($i = 0; $i < count($project_info_dates); $i++) {
        $dbname = $project_info_dates[$i]["project_db_name"];
        $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
        $pdo = new PDO($dsn, $user, $pwd);

        $sql = 'SELECT COUNT(DISTINCT(probe_processed_hunter_id)) FROM probe WHERE DATE(probe_hunter_processed_time) = :date ';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['date' => $date]);
        $hunter_count = $stmt->fetchColumn();

        if ($hunter_count > 0) {
            switch ($project_info_dates[$i]["project_region"]) {
                case "AMER":
                    $date_project_summary["AMER"]["probe"] += $hunter_count;
                    break;
                case "EMEA":
                    $date_project_summary["EMEA"]["probe"] += $hunter_count;
                    break;
                case "DPG":
                    $date_project_summary["DPG"]["probe"] += $hunter_count;
                    break;
                case "APAC":
                    $date_project_summary["APAC"]["probe"] += $hunter_count;
                    break;
            }
        }

        $sql = 'SELECT COUNT(DISTINCT(account_id)) FROM radar_sources WHERE DATE(creation_time) = :date';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['date' => $date]);
        $hunter_count = $stmt->fetchColumn();

        if ($hunter_count > 0) {
            if ($hunter_count > 0) {
                switch ($project_info_dates[$i]["project_region"]) {
                    case "AMER":
                        $date_project_summary["AMER"]["radar"] += $hunter_count;
                        break;
                    case "EMEA":
                        $date_project_summary["EMEA"]["radar"] += $hunter_count;
                        break;
                    case "DPG":
                        $date_project_summary["DPG"]["radar"] += $hunter_count;
                        break;
                    case "APAC":
                        $date_project_summary["APAC"]["radar"] += $hunter_count;
                        break;
                }
            }
        }

        $sql = 'SELECT COUNT(DISTINCT(reference_processed_hunter_id)) FROM reference_info WHERE DATE(reference_hunter_processed_time) = :date';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['date' => $date]);
        $hunter_count = $stmt->fetchColumn();

        if ($hunter_count > 0) {
            if ($hunter_count > 0) {
                switch ($project_info_dates[$i]["project_region"]) {
                    case "AMER":
                        $date_project_summary["AMER"]["ref"] += $hunter_count;
                        break;
                    case "EMEA":
                        $date_project_summary["EMEA"]["ref"] += $hunter_count;
                        break;
                    case "DPG":
                        $date_project_summary["DPG"]["ref"] += $hunter_count;
                        break;
                    case "APAC":
                        $date_project_summary["APAC"]["ref"] += $hunter_count;
                        break;
                }
            }
        }
    }
    foreach ($date_project_summary as $region) {
        $out_string = '';
        if ($region["probe"] > 0) {
            $out_string .= 'probe';
        }
        if ($region["radar"] > 0) {
            if ($out_string != '') {
                $out_string .= '/';
            }
            $out_string .= 'radar';
        }

        if ($region["ref"] > 0) {
            if ($out_string != '') {
                $out_string .= '/';
            }
            $out_string .= 'reference';
        }

        if ($out_string == '') {
            $out_string = 'NA';
        }

        switch ($region["name"]) {
            case "AMER":
                $summary[$count]["AMER"] = $out_string;
                break;
            case "EMEA":
                $summary[$count]["EMEA"] = $out_string;
                break;
            case "DPG":
                $summary[$count]["DPG"] = $out_string;
                break;
            case "APAC":
                $summary[$count]["APAC"] = $out_string;
                break;
        }
    }
    $count++;
}
*/

$return_arr[] = array("hunter_summary" => $project_info, "project_info_radar" => $project_info_radar, "project_info_ref" => $project_info_ref, "date" => $summary, "report" => $report);
echo json_encode($return_arr);
?>
