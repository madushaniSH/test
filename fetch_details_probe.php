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

$sql = 'SELECT probe.probe_id AS "Probe ID",  project_tickets.ticket_id  AS "Ticket ID", probe.probe_process_comment AS "Comment", probe_status.probe_status_name AS "Probe Status", probe.probe_hunter_processed_time  AS "Probe Processed Time",a.account_gid AS "Hunter GID"
FROM probe
INNER JOIN project_tickets
ON project_tickets.project_ticket_system_id = probe.probe_ticket_id
LEFT JOIN probe_status
ON probe.probe_status_id = probe_status.probe_status_id
LEFT JOIN user_db.accounts a
ON probe.probe_processed_hunter_id = a.account_id
WHERE
(probe.probe_ticket_id = :ticket) AND ((probe.probe_hunter_processed_time >= :start_datetime AND probe.probe_hunter_processed_time <= :end_datetime) OR probe.probe_hunter_processed_time IS NULL)';
$stmt = $pdo->prepare($sql);
$stmt->execute(['start_datetime'=>strval($_POST['start_datetime']), 'end_datetime'=>strval($_POST['end_datetime']), "ticket"=>$_POST['ticket']]);
$probe_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
for ($i = 0; $i < count($probe_details); $i++){
    if ($probe_details[$i]["Comment"] == null) {
        $probe_details[$i]["Comment"] = '';
    }
    if ($probe_details[$i]["Hunter GID"] == null) {
        $probe_details[$i]["Hunter GID"] = '';
    }
    if ($probe_details[$i]["Probe Status"] == null) {
        $probe_details[$i]["Probe Status"] = '';
    }
    if ($probe_details[$i]["Probe Processed Time"] == null) {
        $probe_details[$i]["Probe Processed Time"] = '';
    }
}

$return_arr[] = array("probe_details"=>$probe_details);
echo json_encode($return_arr);
?>