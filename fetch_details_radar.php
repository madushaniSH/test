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

 $sql = 'SELECT radar_sources.radar_source_link AS "radar source link",  project_tickets.ticket_id  AS "Ticket ID", radar_sources.radar_comment AS "Comment", probe_status.probe_status_name AS "Radar Status", radar_hunt.radar_processed_time  AS "Radar Processed Time",a.account_gid AS "Hunter GID"
        FROM radar_hunt 
        LEFT JOIN radar_sources 
        ON radar_hunt.radar_hunt_id=radar_sources.radar_hunt_id
        INNER JOIN project_tickets
        ON project_tickets.project_ticket_system_id = radar_hunt.radar_ticket_id
        LEFT JOIN probe_status
        ON radar_sources.radar_status_id = probe_status.probe_status_id
        LEFT JOIN user_db.accounts a
        ON radar_hunt.radar_hunter_id = a.account_id
        WHERE
        (radar_hunt.radar_processed_time >= :start_datetime AND radar_hunt.radar_processed_time <= :end_datetime) OR radar_hunt.radar_processed_time IS NULL';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime'])]);
    $radar_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
    for ($i = 0; $i < count($radar_details); $i++){
    if ($radar_details[$i]["Comment"] == null) {
        $radar_details[$i]["Comment"] = '';
    }
    if ($radar_details[$i]["Hunter GID"] == null) {
        $radar_details[$i]["Hunter GID"] = '';
    }
    if ($radar_details[$i]["Radar Status"] == null) {
        $radar_details[$i]["Radar Status"] = '';
    }
    if ($radar_details[$i]["Radar Processed Time"] == null) {
        $radar_details[$i]["Radar Processed Time"] = '';
    }
   }
    //End radar hunt details export//
try {
$sql = 'SELECT reference_info.reference_ean AS "Reference EAN", project_tickets.ticket_id AS "Ticket ID", reference_info.reference_process_comment AS "Reference Comment", probe_status.probe_status_name AS "Reference Status", reference_info.reference_hunter_processed_time AS "Reference Processed Time", a.account_gid AS "Hunter GID"
FROM reference_info
INNER JOIN project_tickets
ON project_tickets.project_ticket_system_id = reference_info.reference_ticket_id
LEFT JOIN probe_status
ON probe_status.probe_status_id = reference_info.reference_status_id
LEFT JOIN user_db.accounts a
ON reference_info.reference_processed_hunter_id = a.account_id
WHERE
(reference_info.reference_ticket_id = :ticket) AND ((reference_info.reference_hunter_processed_time >= :start_datetime AND reference_info.reference_hunter_processed_time <= :end_datetime) OR reference_info.reference_processed_hunter_id IS NULL)';
$stmt = $pdo->prepare($sql);
$stmt->execute(['start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime']), "ticket"=>$_POST['ticket']]);
$reference_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $warning = $e->getMessage();
}

$return_arr[] = array("radar_details"=>$radar_details, "reference_details"=>$reference_details, "warning"=>$warning);
echo json_encode($return_arr);
?>