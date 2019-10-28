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
$sql = 'SELECT products.product_id, product_hunt_type AS "Product Hunt Type",probe.probe_id AS "Probe ID", radar_sources.radar_source_link AS "Radar Source Link", reference_info.reference_ean AS "Reference EAN" ,brand.brand_name AS "Brand", radar_hunt.radar_brand AS "Radar Brand", reference_info.reference_brand AS "Reference Brand", products.product_alt_design_name AS "Alternative Design Name", products.product_name AS "English Product Name" , products.product_previous AS "Previous English Product Name", products.product_comment AS "Product Comment" ,products.product_type AS "Product Type", products.product_creation_time AS "Product Creation Time", client_category.client_category_name AS "Category", radar_hunt.radar_category AS "Radar Category", reference_info.reference_category AS "Reference Category",products.product_facing_count AS "Facing Count",products.account_id AS "hunter_gid", products.manufacturer_link AS "Manufacturer Link", products.product_link AS "Product Link",products.product_qa_account_id AS "qa_gid", products.product_qa_datetime AS "QA Time", products.product_qa_status AS "Product Status", products.product_submit_status AS "Product Submit Status"
FROM products
LEFT JOIN probe_product_info
ON products.product_id = probe_product_info.probe_product_info_product_id
LEFT JOIN probe 
ON probe_product_info.probe_product_info_key_id = probe.probe_key_id
LEFT JOIN project_tickets
ON project_tickets.project_ticket_system_id = probe.probe_ticket_id
LEFT JOIN radar_sources
ON products.product_id = radar_sources.radar_product_id
LEFT JOIN radar_hunt
ON radar_sources.radar_hunt_id = radar_hunt.radar_hunt_id
LEFT JOIN ref_product_info 
ON products.product_id = ref_product_info.product_id
LEFT JOIN reference_info
ON ref_product_info.reference_info_id = reference_info.reference_info_id
LEFT JOIN brand
ON probe.brand_id = brand.brand_id
LEFT JOIN client_category
ON probe.client_category_id = client_category.client_category_id
WHERE
products.product_status = 2
AND
(products.product_creation_time >= :start_datetime AND products.product_creation_time <= :end_datetime)
AND
(products.product_hunt_type = "probe" AND probe.probe_ticket_id = :ticket) || (products.product_hunt_type = "radar" AND radar_hunt.radar_ticket_id = :ticket) || (products.product_hunt_type = "reference" AND reference_info.reference_ticket_id = :ticket)';
$stmt = $pdo->prepare($sql);
$stmt->execute(['start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime']), "ticket"=>$_POST['ticket']]);
$hunted_product_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
for ($i = 0; $i < count($hunted_product_info); $i++){
    if ($hunted_product_info[$i]["Probe ID"] == null) {
        $hunted_product_info[$i]["Probe ID"] = '';
    }
    if ($hunted_product_info[$i]["Reference Category"] == null) {
        $hunted_product_info[$i]["Reference Category"] = '';
    }
    if ($hunted_product_info[$i]["Product Comment"] == null) {
        $hunted_product_info[$i]["Product Comment"] = '';
    }
    if ($hunted_product_info[$i]["Reference Brand"] == null) {
        $hunted_product_info[$i]["Reference Brand"] = '';
    }
    if ($hunted_product_info[$i]["Reference EAN"] == null) {
        $hunted_product_info[$i]["Reference EAN"] = '';
    }
    if ($hunted_product_info[$i]["Radar Source Link"] == null) {
        $hunted_product_info[$i]["Radar Source Link"] = '';
    }
    if ($hunted_product_info[$i]["Radar Brand"] == null) {
        $hunted_product_info[$i]["Radar Brand"] = '';
    }
    if ($hunted_product_info[$i]["Radar Category"] == null) {
        $hunted_product_info[$i]["Radar Category"] = '';
    }
    if ($hunted_product_info[$i]["Alternative Design Name"] == null) {
        $hunted_product_info[$i]["Alternative Design Name"] = '';
    }
    if ($hunted_product_info[$i]["Brand"] == null) {
        $hunted_product_info[$i]["Brand"] = '';
    }
    if ($hunted_product_info[$i]["Category"] == null) {
        $hunted_product_info[$i]["Category"] = '';
    }
    if ($hunted_product_info[$i]["Manufacturer Link"] == null) {
        $hunted_product_info[$i]["Manufacturer Link"] = '';
    }
    if ($hunted_product_info[$i]["Product Link"] == null) {
        $hunted_product_info[$i]["Product Link"] = '';
    }
    if ($hunted_product_info[$i]["Previous English Product Name"] == null){
        $hunted_product_info[$i]["Previous English Product Name"] = '';
    }
    $sql = 'SELECT b.account_gid FROM products INNER JOIN user_db.accounts b ON products.account_id = b.account_id WHERE b.account_id = :account_id AND products.product_id = :product_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$hunted_product_info[$i][hunter_gid], 'product_id'=>$hunted_product_info[$i][product_id]]);
    $hunter_gid = $stmt->fetch(PDO::FETCH_OBJ);
    $hunted_product_info[$i]['Hunter GID'] = $hunter_gid->account_gid;
    unset($hunted_product_info[$i][hunter_gid]);
    $sql = 'SELECT b.account_gid FROM products INNER JOIN user_db.accounts b ON products.product_qa_account_id = b.account_id WHERE b.account_id = :account_id AND products.product_id = :product_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id'=>$hunted_product_info[$i][qa_gid], 'product_id'=>$hunted_product_info[$i][product_id]]);
    $qa_gid = $stmt->fetch(PDO::FETCH_OBJ);
    $hunted_product_info[$i]['QA GID'] = $qa_gid->account_gid;
    unset($hunted_product_info[$i][qa_gid]);
    $sql = 'SELECT a.project_error_name FROM product_qa_errors b INNER JOIN project_errors a ON b.error_id = a.project_error_id WHERE b.product_id = :product_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['product_id'=>$hunted_product_info[$i][product_id]]);
    $qa_errors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $error_string = '';
    for ($j = 0; $j < count($qa_errors); $j++) {
        $error_string .= $qa_errors[$j][project_error_name].',';
    }
    if ($error_string != '') {
        $error_string = rtrim($error_string, ",");
    } 
    $hunted_product_info[$i]['QA Errors'] = $error_string;
    
    $sql = 'SELECT project_error_image_location FROM project_error_images WHERE product_id = :product_id LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['product_id'=>$hunted_product_info[$i][product_id]]);
    $row_count = $stmt->rowCount(PDO::FETCH_OBJ);
    $file_info = $stmt->fetch(PDO::FETCH_OBJ);
    $error_image_path = '';
    if ($row_count != 0) {
        $error_image_path = substr($file_info->project_error_image_location, 0, strrpos($file_info->project_error_image_location, '/') );
    } 
    $hunted_product_info[$i]['Error Image Location'] = $error_image_path;
    $sql = 'SELECT product_hunt_type FROM products WHERE product_id = :product_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['product_id'=>$hunted_product_info[$i][product_id]]);
    $type_info = $stmt->fetch(PDO::FETCH_OBJ);

    if ($type_info->product_hunt_type == 'probe') {
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
    }else if ($type_info->product_hunt_type == 'radar') {
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
    } else if ($type_info->product_hunt_type == 'reference') {
        $sql = 'SELECT project_tickets.ticket_id FROM products  
                INNER JOIN ref_product_info
                ON products.product_id = ref_product_info.product_id
                INNER JOIN reference_info
                ON reference_info.reference_info_id = ref_product_info.reference_info_id
                INNER JOIN project_tickets
                ON reference_info.reference_ticket_id = project_tickets.project_ticket_system_id
                WHERE products.product_id = :product_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['product_id'=>$hunted_product_info[$i][product_id]]);
        $ticket_info = $stmt->fetch(PDO::FETCH_OBJ);
        $hunted_product_info[$i]['Ticket ID'] = $ticket_info->ticket_id;
    }
    unset($hunted_product_info[$i][product_id]);
}
$return_arr[] = array("hunted_product_info"=>$hunted_product_info);
echo json_encode($return_arr);
?>