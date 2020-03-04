<?php
/*
    Author: Malika Liyanage
*/
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: ../login_auth_one.php');
    exit();
} else {
    if(!($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'ODA Supervisor' || $_SESSION['role'] === 'ODA')){
        header('Location: ../index.php');
        exit();
    }
}
// Current settings to connect to the user account database
require('../../user_db_connection.php');
$dbname = $_POST['project_name'];
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

$product_info[] = array();
$title_string = '';
$radar_source = '';
$ref_info = array();

$sql = 'SELECT oda_queue_id FROM oda_queue WHERE account_id = :account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id' => $_SESSION['id']]);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);
$already_assigned = 0;

if ($row_count == 0) {
    $now = new DateTime();
    $datetime = $now->format('Y-m-d H:i:s');

    $sql = 'UPDATE oda_queue oq SET oq.account_id = :account_id, oq.qa_being_handled = 1, oq.assign_datetime = :datetime WHERE oq.product_id = :product_id AND oq.account_id IS NULL';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id' => $_SESSION['id'], 'product_id' => $_POST['product_id'], 'datetime' => $datetime]);

    $sql = 'SELECT oda_queue_id FROM oda_queue WHERE account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id' => $_SESSION['id']]);
    $row_count = $stmt->rowCount(PDO::FETCH_OBJ);
} else {
    $already_assigned = 1;
}

if ($row_count === 1) {
    $sql = '
SELECT 
    p.product_id,
    p.product_name,
    p.product_alt_design_name,
    p.product_facing_count,
    p.product_hunt_type,
    p.product_link,
    p.manufacturer_link
FROM
    products p
        INNER JOIN
    oda_queue oq ON p.product_id = oq.product_id
WHERE
	oq.account_id = :account_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['account_id' => $_SESSION['id']]);
    $product_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($product_info[0][product_hunt_type] === "probe") {
        $sql = '
            SELECT b.brand_name, cc.client_category_name, p.probe_id as "source" FROM probe p 
                INNER JOIN probe_product_info ppi on p.probe_key_id = ppi.probe_product_info_key_id
                INNER JOIN products p2 on ppi.probe_product_info_product_id = p2.product_id
                LEFT OUTER JOIN brand b on p.brand_id = b.brand_id
                LEFT OUTER JOIN client_category cc on p.client_category_id = cc.client_category_id
            WHERE
                p2.product_id= :product_id
            ';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['product_id' => $product_info[0][product_id]]);
        $source_info = $stmt->fetch(PDO::FETCH_ASSOC);
    }else if ($product_info[0][product_hunt_type] === "radar") {
        $sql = '
            SELECT rh.radar_brand as "brand_name", rh.radar_category as "client_category_name", rs.radar_source_link as "source" FROM radar_sources rs 
                INNER JOIN radar_hunt rh on rs.radar_hunt_id = rh.radar_hunt_id
                INNER JOIN products p on rs.radar_product_id = p.product_id
            WHERE
                p.product_id= :product_id
            ';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['product_id' => $product_info[0][product_id]]);
        $source_info = $stmt->fetch(PDO::FETCH_ASSOC);

        $radar_source = $source_info[source];
        $source_info[source] = '';
    } else if ($product_info[0][product_hunt_type] === "reference") {
        $sql = '
            SELECT ri.reference_brand as "brand_name", ri.reference_ean as "client_category_name", "none" as "source" FROM reference_info ri 
                INNER JOIN ref_product_info rpi on ri.reference_info_id = rpi.reference_info_id
                INNER JOIN products p on rpi.product_id = p.product_id
            WHERE
                p.product_id= :product_id
            ';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['product_id' => $product_info[0][product_id]]);
        $source_info = $stmt->fetch(PDO::FETCH_ASSOC);
        $source_info[source] = '';

        $sql = '
            SELECT ri.reference_recognition_level, ri.reference_short_name, ri.reference_sub_brand, 
                   ri.reference_manufacturer, ri.reference_category, ri.reference_sub_category,
                   ri.reference_base_size, ri.reference_size, ri.reference_measurement_unit, ri.reference_container_type,
                   ri.reference_agg_level, ri.reference_segment, ri.reference_count_upc2, ri.reference_flavor_detail,
                   ri.reference_case_pack, ri.reference_multi_pack FROM reference_info ri
                INNER JOIN ref_product_info rpi on ri.reference_info_id = rpi.reference_info_id
                INNER JOIN products p on rpi.product_id = p.product_id
            WHERE
                p.product_id= :product_id
            ';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['product_id' => $product_info[0][product_id]]);
        $ref_info = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    $title_string = $source_info[brand_name] . ' ' . $source_info[client_category_name] . ' ' . $source_info[source];
}

$return_arr[] = array("row_count" => $row_count, "already_assigned" => $already_assigned,
    "product_info" => $product_info, "radar_source" => $radar_source, "title_string" => $title_string,
    "ref_info" => $ref_info);
echo json_encode($return_arr);
?>