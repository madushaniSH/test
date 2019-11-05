<?php
/*
    Filename: fetch_oda_count.php
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
$dbname = trim($_POST['project_name']);
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

// Selected tickets are passed in a comma separated string, which are then split and put into an array
$ticket_string = trim($_POST['ticket']);
$ticket_array = explode(",", $ticket_string);
$ticket_query_string = '';
$product_type_query_string = '';
$ref_flag = false;

if ($_POST['reference_qa'] === 'false') {
    $ref_flag = false;
    $product_type_query_string = ' p.product_hunt_type = "probe" OR product_hunt_type = "radar"';
} else if ($_POST['reference_qa'] === 'true') {
    $product_type_query_string = ' product_hunt_type = "reference"';
    $ref_flag = true;
}

// making ticket string to be used in select query
foreach($ticket_array as $ticket) {
    if ($ticket_query_string == '') {
        if (!$ref_flag) {
            $ticket_query_string .= ' pr.probe_ticket_id = ' . (int)$ticket.' OR rh.radar_ticket_id = ' . (int)$ticket;
        } else {
            $ticket_query_string .= ' ri.reference_ticket_id = ' . (int)$ticket;
        }
    } else {
        if (!$ref_flag) {
            $ticket_query_string .= ' OR pr.probe_ticket_id = ' . (int)$ticket.' OR rh.radar_ticket_id = ' . (int)$ticket;
        } else {
            $ticket_query_string .= ' OR ri.reference_ticket_id = ' . (int)$ticket;
        }
    }
}

try {
    $sql = 'SELECT COUNT(*) FROM products p
        LEFT OUTER JOIN probe_product_info ppi on p.product_id = ppi.probe_product_info_product_id 
        LEFT OUTER JOIN probe pr ON ppi.probe_product_info_key_id = pr.probe_key_id 
        LEFT  JOIN product_client_category pcc on p.product_id = pcc.product_id
        LEFT OUTER JOIN ref_product_info rpi on p.product_id = rpi.product_id
        LEFT OUTER JOIN reference_info ri on rpi.reference_info_id = ri.reference_info_id
        LEFT OUTER JOIN  radar_sources rs on p.product_id = rs.radar_product_id
        LEFT OUTER JOIN  radar_hunt rh ON rs.radar_hunt_id = rh.radar_hunt_id
        WHERE
        p.product_type = "brand" AND (' . $ticket_query_string . '
        ) AND (' . $product_type_query_string . '
        ) AND  pcc.client_category_id = :client_category
        AND p.product_qa_status = "approved"';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['client_category' => (int)$_POST['client_cat']]);
    $brand_count = $stmt->fetchColumn();

    $sql = 'SELECT COUNT(*) FROM products p
        LEFT OUTER JOIN probe_product_info ppi on p.product_id = ppi.probe_product_info_product_id 
        LEFT OUTER JOIN probe pr ON ppi.probe_product_info_key_id = pr.probe_key_id 
        LEFT  JOIN product_client_category pcc on p.product_id = pcc.product_id
        LEFT JOIN ref_product_info rpi on p.product_id = rpi.product_id
        LEFT OUTER JOIN reference_info ri on rpi.reference_info_id = ri.reference_info_id
        LEFT OUTER JOIN  radar_sources rs on p.product_id = rs.radar_product_id
        LEFT OUTER JOIN  radar_hunt rh ON rs.radar_hunt_id = rh.radar_hunt_id
        WHERE
        p.product_type = "sku" AND (' . $ticket_query_string . '
        ) AND (' . $product_type_query_string . '
        ) AND  pcc.client_category_id = :client_category
        AND p.product_qa_status = "approved"';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['client_category' => (int)$_POST['client_cat']]);
    $sku_count = $stmt->fetchColumn();

    $sql = 'SELECT COUNT(*) FROM products p
        LEFT OUTER JOIN probe_product_info ppi on p.product_id = ppi.probe_product_info_product_id 
        LEFT OUTER JOIN probe pr ON ppi.probe_product_info_key_id = pr.probe_key_id 
        LEFT  JOIN product_client_category pcc on p.product_id = pcc.product_id
        LEFT OUTER JOIN ref_product_info rpi on p.product_id = rpi.product_id
        LEFT OUTER JOIN reference_info ri on rpi.reference_info_id = ri.reference_info_id
        LEFT OUTER JOIN  radar_sources rs on p.product_id = rs.radar_product_id
        LEFT OUTER JOIN  radar_hunt rh ON rs.radar_hunt_id = rh.radar_hunt_id
        WHERE
        p.product_type = "dvc" AND (' . $ticket_query_string . '
        ) AND (' . $product_type_query_string . '
        ) AND  pcc.client_category_id = :client_category
        AND p.product_qa_status = "approved"';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['client_category' => (int)$_POST['client_cat']]);
    $dvc_count = $stmt->fetchColumn();


} catch (PDOException $e) {
    $warning = $e->getMessage();
}

$return_arr[] = array("brand_count"=>$brand_count, "sku_count"=>$sku_count, "dvc_count" =>$dvc_count);
echo json_encode($return_arr);

