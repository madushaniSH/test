<?php
/*
    Filename: assign_qa_product.php
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
$dbname = $_POST['project_name'];
$_SESSION['current_database'] = $dbname;
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
    $client_category_string = ' 1';
} else {
    $client_category_string = ' pcc.client_category_id = '.(int)$_POST['client_cat'];
}

// making ticket string to be used in select query
foreach($ticket_array as $ticket) {
    if ($ticket_query_string == '') {
        if (!$ref_flag) {
            $ticket_query_string .= ' pr.probe_ticket_id = ' . (int)$ticket . ' OR rh.radar_ticket_id = ' . (int)$ticket;
        } else {
            $ticket_query_string .= ' ri.reference_ticket_id = ' . (int)$ticket;
        }
    } else {
        if (!$ref_flag) {
            $ticket_query_string .= ' OR pr.probe_ticket_id = ' . (int)$ticket . ' OR rh.radar_ticket_id = ' . (int)$ticket;
        } else {
            $ticket_query_string .= ' OR ri.reference_ticket_id = ' . (int)$ticket;
        }
    }
}

$sql = 'SELECT oq.oda_queue_id FROM oda_queue oq WHERE oq.account_id = :account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$queue_info = $stmt->fetch(PDO::FETCH_OBJ);
$last_id = $queue_info->oda_queue_id;
$row_count = $stmt->rowCount();

try {
    if ($row_count == 0) {
        $search_term = '';
        if ($_POST['product_type'] == 'sku') {
            $search_term .= $_POST['sku_brand_name'] . ' ';
        }
        if ($_POST['product_type'] == 'dvc') {
            $search_term .= $_POST['sku_dvc_name'];
        }
        if ($_POST['product_type'] == 'facing') {
            $search_term .= $_POST['sku_facing_name'] . ' ';
        }
        $search_term .= '%';
        $sql = 'UPDATE oda_queue AS upd INNER JOIN 
            (SELECT  oq.product_id FROM oda_queue oq
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
            AND (oq.qa_being_handled = 0 OR oq.account_id = :account_id)
            AND p.product_qa_status = "approved"
            AND (p.product_name LIKE :search_term OR (p.product_type = "dvc" AND p.product_alt_design_name LIKE :search_term))
            LIMIT 1) AS sel
        ON sel.product_id = upd.product_id SET upd.account_id = :account_id, upd.qa_being_handled = 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id' => (int)$_SESSION['id'], "search_term"=>$search_term, "product_type"=>$_POST['product_type']]);

        $sql = 'SELECT oq.oda_queue_id FROM oda_queue oq WHERE oq.account_id = :account_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id' => $_SESSION['id']]);
        $queue_info = $stmt->fetch(PDO::FETCH_OBJ);
        $last_id = $queue_info->oda_queue_id;
    }
} catch (PDOException $e) {
    $warning = $e->getMessage();
}

$return_arr[] = array("probe_info"=>$warning, "radar_info"=>$radar_info, "ref_info"=>$ref_info);
echo json_encode($return_arr);
