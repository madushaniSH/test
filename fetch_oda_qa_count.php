<?php
/*
    Filename: fetch_oda_count.php
    Author: Malika Liyanage
*/
function fetch_count_oda_queue($pdo, $product_type, $ticket_query_string, $product_type_query_string, $client_category_string, $optional_query = '1') {
    try {
        $sql = 'SELECT COUNT(*) FROM oda_queue oq
        INNER  JOIN products p ON oq.product_id = p.product_id
        LEFT OUTER JOIN probe_product_info ppi on p.product_id = ppi.probe_product_info_product_id 
        LEFT OUTER JOIN probe pr ON ppi.probe_product_info_key_id = pr.probe_key_id 
        LEFT  JOIN product_client_category pcc on p.product_id = pcc.product_id
        LEFT OUTER JOIN ref_product_info rpi on p.product_id = rpi.product_id
        LEFT OUTER JOIN reference_info ri on rpi.reference_info_id = ri.reference_info_id
        LEFT OUTER JOIN  radar_sources rs on p.product_id = rs.radar_product_id
        LEFT OUTER JOIN  radar_hunt rh ON rs.radar_hunt_id = rh.radar_hunt_id
        WHERE
        p.product_type = :product_type AND (' . $ticket_query_string . '
        ) AND (' . $product_type_query_string . '
        ) AND ' . $client_category_string . '
        AND p.product_qa_status = "approved" AND ' . $optional_query;
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['product_type' => $product_type]);
        $product_count = $stmt->fetchColumn();
    } catch (PDOException $e) {
        $product_count = $e->getMessage();
    }
    return $product_count;
}

function make_optional_query ($account_id, $brand_name, $flag = "true", $is_dvc = "false") {
    if ($flag === "true") {
        $search_term = '"'.$brand_name.' %"';
    } else {
        $search_term = '"'.$brand_name.'"';
    }
    if ($is_dvc == "true") {
        $search_string = ' p.product_alt_design_name LIKE '.$search_term;
    } else {
        $search_string = ' p.product_name LIKE '.$search_term;
    }
    $query_string = ' (oq.qa_being_handled = 0 or oq.account_id = '.(int)$account_id.') AND'.$search_string;
    return $query_string;
}

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
$client_category_string = '';
$ref_flag = false;

if ($_POST['reference_qa'] === 'false') {
    $ref_flag = false;
    $product_type_query_string = ' p.product_hunt_type = "probe" OR product_hunt_type = "radar"';
} else if ($_POST['reference_qa'] === 'true') {
    $product_type_query_string = ' product_hunt_type = "reference"';
    $ref_flag = true;
}

if ((int)$_POST['client_cat'] === 0) {
    $client_category_string = ' pcc.client_category_id IS NULL';
} else {
    $client_category_string = ' pcc.client_category_id = '.(int)$_POST['client_cat'];
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


$brand_count = fetch_count_oda_queue($pdo, "brand", $ticket_query_string, $product_type_query_string, $client_category_string);
$sku_count = fetch_count_oda_queue($pdo, "sku", $ticket_query_string, $product_type_query_string, $client_category_string);
$dvc_count = fetch_count_oda_queue($pdo, "dvc", $ticket_query_string, $product_type_query_string, $client_category_string);
$facing_count = fetch_count_oda_queue($pdo, "facing", $ticket_query_string, $product_type_query_string, $client_category_string);

$account_id = $_SESSION['id'];
$sku_brand_name = $_POST['sku_brand_name'];
$dvc_brand_name = $_POST['sku_dvc_name'];
$facing_brand_name = $_POST['sku_facing_name'];

$sku_optional_query = make_optional_query($account_id, $sku_brand_name);
$dvc_optional_query = make_optional_query($account_id, $dvc_brand_name, $_POST['dvc_flag'], "true");
$facing_optional_query = make_optional_query($account_id, $facing_brand_name);

$brand_filtered_count = $brand_count;
$sku_filtered_count = fetch_count_oda_queue($pdo, "sku", $ticket_query_string, $product_type_query_string, $client_category_string, $sku_optional_query);
$dvc_filtered_count = fetch_count_oda_queue($pdo, "dvc", $ticket_query_string, $product_type_query_string, $client_category_string, $dvc_optional_query);
$facing_fitered_count = fetch_count_oda_queue($pdo, "facing", $ticket_query_string, $product_type_query_string, $client_category_string, $facing_optional_query);

$sql = 'SELECT oq.oda_queue_id, p.product_type FROM oda_queue oq INNER JOIN products p ON p.product_id = oq.product_id WHERE oq.account_id = :account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$account_id]);
$queue_info = $stmt->fetch(PDO::FETCH_OBJ);
$rowCount = $stmt->rowCount(PDO::FETCH_OBJ);

$return_arr[] = array("processing_probe_row" => $rowCount, "product_type" => $queue_info->product_type,"brand_count"=>$brand_count, "sku_count"=>$sku_count, "dvc_count" =>$dvc_count, "facing_count"=>$facing_count, "brand_filtered_count"=>$brand_filtered_count, "sku_filtered_count"=>$sku_filtered_count, "dvc_filtered_count"=>$dvc_filtered_count, "facing_filtered_count"=>$facing_fitered_count);
echo json_encode($return_arr);

