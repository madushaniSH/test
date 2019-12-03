<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
    header('Location: login_auth_one.php');
    exit();
}
// Current settings to connect to the user account database
require('../../user_db_connection.php');

function fetchTicketFacingCount ($pdo, $ticket_id) {
    $sql = 'SELECT SUM(prod.product_facing_count)
        FROM products prod
            LEFT OUTER JOIN probe_product_info ppi 
                ON prod.product_id = ppi.probe_product_info_product_id
            LEFT OUTER JOIN probe p 
                ON ppi.probe_product_info_key_id = p.probe_key_id
            LEFT OUTER JOIN radar_sources rs 
                ON prod.product_id = rs.radar_product_id
            LEFT OUTER JOIN radar_hunt rh 
                ON rs.radar_hunt_id = rh.radar_hunt_id
            LEFT OUTER JOIN ref_product_info rpi 
                ON prod.product_id = rpi.product_id
            LEFT OUTER JOIN reference_info ri 
                ON rpi.reference_info_id = ri.reference_info_id
            LEFT OUTER JOIN project_tickets pt 
                ON 
                    p.probe_ticket_id = pt.project_ticket_system_id
                OR 
                    rh.radar_ticket_id = pt.project_ticket_system_id
                OR
                    ri.reference_ticket_id = pt.project_ticket_system_id
            WHERE
                (p.probe_ticket_id = :id OR rh.radar_ticket_id = :id OR ri.reference_ticket_id = :id)
              AND
                (prod.product_qa_status = "approved" OR prod.product_qa_status = "rejected" or prod.product_qa_status = "active")
                ';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'id' => $ticket_id
    ]);
    $count = $stmt->fetchColumn();
    $count == NULL ? $return_count = 0 : $return_count = $count;
    return $return_count;
}

function fetchTicketProductCount ($pdo, $ticket_id, $product_type) {
    if ($product_type !== "facing") {
        $sql = 'SELECT COUNT(*)
        FROM products prod
            LEFT OUTER JOIN probe_product_info ppi 
                ON prod.product_id = ppi.probe_product_info_product_id
            LEFT OUTER JOIN probe p 
                ON ppi.probe_product_info_key_id = p.probe_key_id
            LEFT OUTER JOIN radar_sources rs 
                ON prod.product_id = rs.radar_product_id
            LEFT OUTER JOIN radar_hunt rh 
                ON rs.radar_hunt_id = rh.radar_hunt_id
            LEFT OUTER JOIN ref_product_info rpi 
                ON prod.product_id = rpi.product_id
            LEFT OUTER JOIN reference_info ri 
                ON rpi.reference_info_id = ri.reference_info_id
            LEFT OUTER JOIN project_tickets pt 
                ON 
                    p.probe_ticket_id = pt.project_ticket_system_id
                OR 
                    rh.radar_ticket_id = pt.project_ticket_system_id
                OR
                    ri.reference_ticket_id = pt.project_ticket_system_id
            WHERE
                (p.probe_ticket_id = :id OR rh.radar_ticket_id = :id OR ri.reference_ticket_id = :id)
              AND
                (prod.product_qa_status = "approved" OR prod.product_qa_status = "rejected" or prod.product_qa_status = "active")
              AND
                (prod.product_type = :product_type)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $ticket_id,
            'product_type' => $product_type
        ]);
        $count = $stmt->fetchColumn();
        $count == NULL ? $return_count = 0 : $return_count = $count;
    } else {
        $return_count = fetchTicketFacingCount($pdo, $ticket_id);
    }
    return $return_count;
}

$project_array =  explode(',', $_POST['project_array']);
$status_array =  explode(',', $_POST['status_array']);
$status_string = "";

for ($i = 0; $i < count($status_array); $i++){
    if ($i == 0) {
        $status_string .= '(pt.ticket_status = "'.$status_array[$i].'"';
    } else {
        $status_string .= ' OR (pt.ticket_status = "'.$status_array[$i].'"';
    }
    $status_string .= ')';
}

$return_info = array();
$count = 0;

for ($i = 0; $i < count($project_array); $i++) {
    $dbname = $project_array[$i];
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
    $pdo = new PDO($dsn, $user, $pwd);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    try {
        $sql = '
SELECT
    pt.project_ticket_system_id,
    DATE(pt.ticket_creation_time) AS "create_date",
    pt.ticket_type,
    pt.ticket_description,
    pt.ticket_id,
    pt.ticket_status,
    a.account_first_name AS "account_gid",
    pt.ticket_comment,
    b.account_first_name AS "mod_gid",
    pt.ticket_last_mod_date,
    pt.ticket_escalate,
    pt.ticket_escalate_date,
    pt.ticket_completion_date
FROM
    project_tickets pt
INNER JOIN
    user_db.accounts a
ON
    a.account_id = pt.account_id
LEFT OUTER JOIN
    user_db.accounts b
ON
    b.account_id = pt.ticket_last_mod_account_id
WHERE 
  '.$status_string.'
GROUP BY
    1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $ticket_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

        for ($j = 0; $j < count($ticket_info); $j++) {
            $id = $ticket_info[$j]["project_ticket_system_id"];
            $ticket_info[$j]["sku_count"] = fetchTicketProductCount($pdo, $id, "sku");
            $ticket_info[$j]["brand_count"] = fetchTicketProductCount($pdo, $id, "brand");
            $ticket_info[$j]["dvc_count"] = fetchTicketProductCount($pdo, $id, "dvc");
            $ticket_info[$j]["facing_count"] = fetchTicketProductCount($pdo, $id, "facing");
            $ticket_info[$j]["project_name"] = $dbname;
            $ticket_info[$j]["index"] = $count;
            $count++;
        }


    } catch (PDOException $e) {
        $ticket_info = $e->getMessage();
    }
    $pdo = NULL;

    if (count($ticket_info) > 0) {
        $return_info = array_merge($ticket_info, $return_info);
    }
}


$return_arr[] = array("ticket_info" => $return_info);
echo json_encode($return_arr);

