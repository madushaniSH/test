<?php
/*
    Filename: assign_radar.php
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

$sql = 'SELECT radar_queue_id FROM radar_queue WHERE account_id = :account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$probe_info = $stmt->fetch(PDO::FETCH_OBJ);
$last_id = $probe_info->radar_queue_id;
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

if ($row_count == 0) {
    $this_count = 0;
    $iterations = 0;
    $search_term = $_POST["client_cat"];
    $now = new DateTime();
    $datetime = $now->format('Y-m-d H:i:s');
    do {
        $sql = 'UPDATE radar_queue AS upd INNER JOIN (SELECT t1.radar_hunt_key_id FROM radar_queue AS t1 INNER JOIN radar_hunt AS t2 ON t2.radar_hunt_id = t1.radar_hunt_key_id WHERE t1.radar_being_handled = 0 AND t1.account_id IS NULL AND t2.radar_category = :search_term AND t2.radar_ticket_id = :ticket LIMIT 1 ) AS sel ON sel.radar_hunt_key_id = upd.radar_hunt_key_id SET upd.account_id = :account_id, upd.radar_being_handled = 1, upd.assign_datetime = :datetime';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id'],'search_term'=>$search_term, "ticket"=>$_POST['ticket'], 'datetime' => $datetime]);

        $sql = 'SELECT radar_queue_id FROM radar_queue WHERE account_id = :account_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id']]);
        $probe_info = $stmt->fetch(PDO::FETCH_OBJ);
        $last_id = $probe_info->radar_queue_id;
        $this_count = $stmt->rowCount(PDO::FETCH_OBJ);
        $iterations++;
    } while ($this_count == 0 && $iterations < 10);
}
$sql = 'SELECT b.radar_category, b.radar_brand, c.ticket_id FROM radar_queue a INNER JOIN radar_hunt b ON a.radar_hunt_key_id = b.radar_hunt_id INNER JOIN project_tickets c ON b.radar_ticket_id = c.project_ticket_system_id WHERE a.radar_queue_id = :radar_queue_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['radar_queue_id'=>$last_id]);
$probe_info = $stmt->fetch(PDO::FETCH_OBJ);
$return_arr[] = array("ticket"=>$probe_info->ticket_id ,"brand_name" => $probe_info->radar_brand, "client_category_name" => $probe_info->radar_category);
echo json_encode($return_arr);
?>