<?php
/*
    Filename: fetch_products_oda.php
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

// making ticket string to be used in select query
foreach($ticket_array as $ticket) {
    if ($ticket_query_string == '') {
        $ticket_query_string .= ' pr.probe_ticket_id = ' . (int)$ticket.' OR rh.radar_ticket_id = ' . (int)$ticket .' OR ri.reference_ticket_id = ' . (int)$ticket;
    } else {
        $ticket_query_string .= ' OR pr.probe_ticket_id = ' . (int)$ticket.' OR rh.radar_ticket_id = ' . (int)$ticket .' OR ri.reference_ticket_id = ' . (int)$ticket;
    }
}

$sql = 'SELECT p.product_id , p.product_type AS "Product Type", p.product_name AS "Product Name", p.product_alt_design_name AS "Product Alt Design Name", p.product_hunt_type AS "Product Hunt Type", "" AS "Ticket ID" ,cc.client_category_name AS "Client Category Name",p.product_creation_time AS "Product Creation Time" , a.account_gid AS "ODA GID", p.product_oda_datetime AS "ODA Datetime", "" AS "Product ODA Errors" , p.product_oda_comment AS "ODA Comment",p.product_qa_status AS "Product Status" FROM products p
        LEFT  JOIN product_client_category pcc on p.product_id = pcc.product_id
        LEFT JOIN client_category cc ON pcc.client_category_id = cc.client_category_id
    LEFT OUTER JOIN probe_product_info ppi on p.product_id = ppi.probe_product_info_product_id 
        LEFT OUTER JOIN probe pr ON ppi.probe_product_info_key_id = pr.probe_key_id 
        LEFT OUTER JOIN ref_product_info rpi on p.product_id = rpi.product_id
        LEFT OUTER JOIN reference_info ri on rpi.reference_info_id = ri.reference_info_id
        LEFT OUTER JOIN  radar_sources rs on p.product_id = rs.radar_product_id
        LEFT OUTER JOIN  radar_hunt rh ON rs.radar_hunt_id = rh.radar_hunt_id
        LEFT OUTER JOIN project_tickets pt on pr.probe_ticket_id = pt.project_ticket_system_id
        LEFT OUTER JOIN user_db.accounts a ON p.product_oda_account_id = a.account_id
        WHERE
        ('.$ticket_query_string.') AND (p.product_qa_status != "pending" AND p.product_qa_status != "disapproved")
        AND (p.product_qa_datetime >= :start_datetime AND p.product_qa_datetime <= :end_datetime)
';
$stmt = $pdo->prepare($sql);
$stmt->execute(['start_datetime'=>$_POST['start_datetime'], 'end_datetime'=>$_POST['end_datetime']]);
$product_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

for ($i = 0; $i < count($product_details); $i++) {
    $sql = 'SELECT a.project_error_name FROM product_oda_errors b INNER JOIN project_errors a ON b.error_id = a.project_error_id WHERE b.product_id = :product_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['product_id' => $product_details[$i][product_id]]);
    $qa_errors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $error_string = '';
    for ($j = 0; $j < count($qa_errors); $j++) {
        $error_string .= $qa_errors[$j][project_error_name] . ',';
    }
    if ($error_string != '') {
        $error_string = rtrim($error_string, ",");
    }
    $product_details[$i]['Product ODA Errors'] = $error_string;

    if ($product_details[$i]['Product Hunt Type'] == 'probe') {
        $sql = 'SELECT project_tickets.ticket_id FROM products  
                INNER JOIN probe_product_info
                ON products.product_id = probe_product_info.probe_product_info_product_id
                INNER JOIN probe 
                ON probe_product_info.probe_product_info_key_id = probe.probe_key_id
                INNER JOIN project_tickets
                ON probe.probe_ticket_id = project_tickets.project_ticket_system_id
                WHERE products.product_id = :product_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['product_id'=>$hunted_product_info[$i][product_id]]);
        $ticket_info = $stmt->fetch(PDO::FETCH_OBJ);
        $hunted_product_info[$i]['Ticket ID'] = $ticket_info->ticket_id;
    }else if ($product_details[$i]['Product Hunt Type'] == 'radar') {
        $sql = 'SELECT project_tickets.ticket_id FROM products  
                INNER JOIN radar_sources
                ON products.product_id = radar_sources.radar_product_id
                INNER JOIN radar_hunt
                ON radar_sources.radar_hunt_id = radar_hunt.radar_hunt_id
                INNER JOIN project_tickets
                ON radar_hunt.radar_ticket_id = project_tickets.project_ticket_system_id
                WHERE products.product_id = :product_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['product_id'=>$hunted_product_info[$i][product_id]]);
        $ticket_info = $stmt->fetch(PDO::FETCH_OBJ);
        $hunted_product_info[$i]['Ticket ID'] = $ticket_info->ticket_id;
    } else if ($product_details[$i]['Product Hunt Type'] == 'reference') {
        $sql = 'SELECT project_tickets.ticket_id FROM products  
                INNER JOIN ref_product_info
                ON products.product_id = ref_product_info.product_id
                INNER JOIN reference_info
                ON reference_info.reference_info_id = ref_product_info.reference_info_id
                INNER JOIN project_tickets
                ON reference_info.reference_ticket_id = project_tickets.project_ticket_system_id
                WHERE products.product_id = :product_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['product_id'=>$product_details[$i][product_id]]);
        $ticket_info = $stmt->fetch(PDO::FETCH_OBJ);
        $product_details[$i]['Ticket ID'] = $ticket_info->ticket_id;
    }

    unset($product_details[$i][product_id]);
}

$return_arr[] = array("product_details"=>$product_details);
echo json_encode($return_arr);
