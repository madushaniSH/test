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
$sql = 'SELECT project_name, project_db_name FROM `project_db`.projects WHERE 1';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$project_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
$count_projects = count($project_array);
$count = 0;
$search_term = '"%' . trim($_POST['product_name']) . '%"';
$exact_match_index = -1;
$product_info = array();

for ($i = 0; $i < $count_projects; $i++) {
    $dbname = $project_array[$i]["project_db_name"];
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
    $pdo = new PDO($dsn, $user, $pwd);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 'SELECT p.product_id ,p.product_name, p.product_alt_design_name, p.product_hunt_type,p.account_id, p.product_qa_account_id, p.product_qa_status FROM ' . $dbname . '.products p WHERE p.product_name LIKE ' . $search_term.' 
    OR p.product_alt_design_name LIKE '.$search_term;
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $product_info_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($product_info_result) > 0) {
        for ($j = 0; $j < count($product_info_result); $j++) {
            $product_info[$count] = array(
                'project_name' => '',
                'ticket_id' => '',
                'product_name' => '',
                'alt_product_name' => '',
                'product_hunt_type' => '',
                'hunter_gid' => '',
                'qa_gid' => '',
                'status' => '',
            );
            $product_info[$count]['project_name'] = $project_array[$i][project_name];
            $product_info[$count]['product_name'] = $product_info_result[$j]["product_name"];
            $product_info[$count]['alt_product_name'] = $product_info_result[$j]["alt_product_name"];
            $product_info[$count]['product_hunt_type'] = $product_info_result[$j]["product_hunt_type"];
            $product_info[$count]['status'] = $product_info_result[$j]["product_qa_status"];

            $sql = "SELECT pt.ticket_id FROM products p 
                    LEFT OUTER JOIN probe_product_info ppi on p.product_id = ppi.probe_product_info_product_id
                    LEFT OUTER JOIN probe p2 on ppi.probe_product_info_key_id = p2.probe_key_id
                    LEFT OUTER JOIN radar_sources rs on p.product_id = rs.radar_product_id
                    LEFT OUTER JOIN radar_hunt rh on rs.radar_hunt_id = rh.radar_hunt_id
                    LEFT OUTER JOIN ref_product_info rpi on p.product_id = rpi.product_id
                    LEFT OUTER JOIN reference_info ri on rpi.reference_info_id = ri.reference_info_id
                    LEFT OUTER JOIN project_tickets pt ON p2.probe_ticket_id = pt.project_ticket_system_id
                    OR pt.project_ticket_system_id = rh.radar_ticket_id
                    OR pt.project_ticket_system_id = ri.reference_ticket_id
                    WHERE
                    p.product_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id'=>$product_info_result[$j]['product_id']]);
            $ticket = $stmt->fetchColumn();
            $product_info[$count]['ticket_id'] = $ticket;

            $sql = 'SELECT a.account_gid FROM user_db.accounts a WHERE a.account_id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id'=>$product_info_result[$j]['account_id']]);
            $account_gid = $stmt->fetchColumn();
            $product_info[$count]['hunter_gid'] = $account_gid;

            $sql = 'SELECT a.account_gid FROM user_db.accounts a WHERE a.account_id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id'=>$product_info_result[$j]['product_qa_account_id']]);
            $account_gid = $stmt->fetchColumn();
            if ($account_gid != null) {
                $product_info[$count]['qa_gid'] = $account_gid;
            }

            $count++;
        }
    }

    if ($exact_match_index != -1) {
        break;
    }
}
$return_arr[] = array('return_details' => $product_info);
echo json_encode($return_arr);
