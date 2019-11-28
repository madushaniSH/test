<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
    header('Location: login_auth_one.php');
    exit();
}
// Current settings to connect to the user account database
require('../../user_db_connection.php');
$dbname = $_POST['project_name'];
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
    pt.ticket_completion_date,
    SUM(
        CASE WHEN prod.product_type = \'brand\' THEN 1 ELSE 0 END
    ) AS brand_count,
    SUM(
        CASE WHEN prod.product_type = \'sku\' THEN 1 ELSE 0 END
    ) AS sku_count,
    SUM(
        CASE WHEN prod.product_type = \'dvc\' THEN 1 ELSE 0 END
    ) AS dvc_count,
    SUM(
        CASE WHEN prod.product_type = \'facing\' THEN 1 ELSE 0 END
    ) AS facing_count
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
LEFT OUTER JOIN
    probe p
ON
    p.probe_ticket_id = pt.project_ticket_system_id
LEFT OUTER JOIN
    probe_product_info ppi
ON
    ppi.probe_product_info_key_id = p.probe_key_id
LEFT OUTER JOIN
    radar_hunt rh
ON
    rh.radar_ticket_id = pt.project_ticket_system_id
LEFT OUTER JOIN
    radar_sources rs
ON
    rs.radar_hunt_id = rh.radar_hunt_id
LEFT OUTER JOIN
    reference_info ri
ON
    ri.reference_ticket_id = pt.project_ticket_system_id
LEFT OUTER JOIN
    ref_product_info rpi
ON
    rpi.reference_info_id = ri.reference_info_id
LEFT OUTER JOIN
    products prod
ON
    prod.product_id = ppi.probe_product_info_product_id OR prod.product_id = rs.radar_product_id OR prod.product_id = rpi.product_id
WHERE
    (
        (
            prod.product_qa_status = "approved" OR(
                prod.product_qa_status = "rejected" OR prod.product_qa_status = "active"
            )
        ) OR 1
    ) AND(
        DATE(pt.ticket_creation_time) >= :start_date AND DATE(pt.ticket_creation_time) <= :end_date
    )
GROUP BY
    1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'start_date' => $_POST['start_date'],
        'end_date' => $_POST['end_date']
    ]);
    $ticket_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $ticket_info = $e->getMessage();
}

$return_arr[] = array("ticket_info" => $ticket_info);
echo json_encode($return_arr);

