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
// Current settings to connect to the user account database
require('../../user_db_connection.php');
$dbname = $_POST['db_name'];
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

$ticket_string = trim($_POST['ticket']);
$ticket_array = explode(",", $ticket_string);
$ticket_query_string = '';

foreach ($ticket_array as $ticket) {
    if ($ticket_query_string == '') {
        $ticket_query_string .= 'pt.ticket_id = "'.$ticket.'"';
    } else {
        $ticket_query_string .= ' OR pt.ticket_id = "' . $ticket.'"';
    }
    $ticket_query_string .= ' OR pt.ticket_id IS NULL';
}

$sql = '
SELECT p.product_id, pt.ticket_id, pt.ticket_status ,DATE(p.product_creation_time) as "product_creation_time", SUBSTRING_INDEX(p.product_name, \' \', 1 ) AS "brand_name" ,p.product_name, p.product_previous, p.product_qa_previous ,p.product_alt_design_name, p.product_alt_design_previous, p.product_alt_design_qa_previous , p.product_type,
       p.product_qa_status, p.product_hunt_type, p.product_qa_datetime, p.product_oda_datetime, p.product_oda_comment,oq.qa_being_handled, p.product_link ,p2.probe_id, ri.reference_ean, rs.radar_source_link, IF (oq.account_id = :account_id, 1, 0) AS assigned_user, IF(cc.client_category_name IS NULL, "NA", cc.client_category_name) AS "client_category_name", pe.product_ean_id, pe.product_ean, pe.product_item_code, pe.additional_comment, pe.duplicate_product_name, ur.unmatch_reason, pe.matched_method, IF(cc.client_category_name IS NULL, "NA", cc.client_category_name) AS "client_category_name",
       pe.ean_creation_time, a.account_gid, a.account_first_name, p.product_facing_count, pe.ean_last_mod_datetime,ae.account_gid as "ean_gid",ae.account_first_name as "ean_fname",b.account_first_name as "mod_name", b.account_gid as "mod_gid"
    FROM products p
    LEFT OUTER JOIN product_ean pe ON pe.product_id = p.product_id
    LEFT OUTER JOIN unmatch_reasons ur ON ur.unmatch_reason_id = pe.unmatch_reason_id
    LEFT OUTER JOIN oda_queue oq ON oq.product_id = p.product_id
    LEFT OUTER JOIN probe_product_info ppi on p.product_id = ppi.probe_product_info_product_id
    LEFT OUTER JOIN probe p2 on ppi.probe_product_info_key_id = p2.probe_key_id
    LEFT OUTER JOIN radar_sources rs on p.product_id = rs.radar_product_id
    LEFT OUTER JOIN radar_hunt rh on rs.radar_hunt_id = rh.radar_hunt_id
    LEFT OUTER JOIN ref_product_info rpi on p.product_id = rpi.product_id
    LEFT OUTER JOIN reference_info ri on rpi.reference_info_id = ri.reference_info_id
    LEFT OUTER JOIN product_client_category pcc ON pcc.product_id = p.product_id
    LEFT OUTER JOIN client_category cc on pcc.client_category_id = cc.client_category_id    
    LEFT OUTER JOIN project_tickets pt on 
        p2.probe_ticket_id = pt.project_ticket_system_id
        OR
        pt.project_ticket_system_id = rh.radar_ticket_id
        OR 
        pt.project_ticket_system_id = ri.reference_ticket_id
    LEFT OUTER JOIN user_db.accounts a ON a.account_id = p.product_oda_account_id
    LEFT OUTER JOIN user_db.accounts ae ON ae.account_id = pe.account_id
    LEFT OUTER JOIN user_db.accounts b ON b.account_id = pe.ean_last_mod_account_id
WHERE ('.$ticket_query_string.') OR  (oq.qa_being_handled = 1 AND oq.account_id = :account_id)
ORDER BY oq.qa_being_handled DESC ,p.product_creation_time DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id' => $_SESSION['id']]);
$product_array = $stmt->fetchAll(PDO::FETCH_ASSOC);

for($i = 0; $i < count($product_array); $i++) {
    $sql = 'SELECT  p.product_name FROM product_ean pe INNER JOIN products p on pe.chain_product_id = p.product_id WHERE pe.product_id = :product_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['product_id'=>$product_array[$i][product_id]]);
    $product_info = $stmt->fetch(PDO::FETCH_ASSOC);
    $product_array[$i]['chain_name'] = $product_info['product_name'];

    $sql = 'SELECT a.project_error_name FROM product_qa_errors b INNER JOIN project_errors a ON b.error_id = a.project_error_id WHERE b.product_id = :product_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['product_id'=>$product_array[$i][product_id]]);
    $qa_errors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $error_string = '';
    for ($j = 0; $j < count($qa_errors); $j++) {
        $error_string .= $qa_errors[$j][project_error_name].',';
    }
    if ($error_string != '') {
        $error_string = rtrim($error_string, ",");
    }
    $product_array[$i]['qa_error'] = $error_string;

    $sql = 'SELECT a.project_error_name FROM product_oda_errors b INNER JOIN project_errors a ON b.error_id = a.project_error_id WHERE b.product_id = :product_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['product_id'=>$product_array[$i][product_id]]);
    $qa_errors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $error_string = '';
    for ($j = 0; $j < count($qa_errors); $j++) {
        $error_string .= $qa_errors[$j][project_error_name].',';
    }
    if ($error_string != '') {
        $error_string = rtrim($error_string, ",");
    }
    $product_array[$i]['oda_error'] = $error_string;

    $sql = 'SELECT weblink FROM product_weblinks pw WHERE pw.product_id = :product_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['product_id'=>$product_array[$i][product_id]]);
    $weblinks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $weblink_string = '';
    for ($j = 0; $j < count($weblinks); $j++) {
        $weblink_string .= $weblinks[$j][weblink].',';
    }
    if ($weblink_string != '') {
        $weblink_string = rtrim($weblink_string, ",");
    }
    $product_array[$i]['weblink'] = $weblink_string;
}

$sql = 'SELECT oda_queue_id FROM oda_queue WHERE account_id = :account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id' => $_SESSION['id']]);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

$return_arr[] = array("product_info" => $product_array, "row_count" => $row_count);
echo json_encode($return_arr);
