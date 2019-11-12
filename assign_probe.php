<?php
/*
    Filename: assign_probe.php
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

$sql = 'SELECT probe_queue_id FROM probe_queue WHERE account_id = :account_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['account_id'=>$_SESSION['id']]);
$probe_info = $stmt->fetch(PDO::FETCH_OBJ);
$last_id = $probe_info->probe_queue_id;
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

if ($row_count == 0) {
    $this_count = 0;
    $iterations = 0;
    do {
        if ($_POST['client_cat'] == 0 && $_POST['brand'] == 0) {
            $sql = 'UPDATE probe_queue AS upd INNER JOIN (SELECT t1.probe_key_id FROM probe_queue AS t1 INNER JOIN probe AS t2 ON t2.probe_key_id = t1.probe_key_id WHERE t1.probe_being_handled = 0 AND t1.account_id IS NULL AND t2.probe_ticket_id = :ticket AND t2.client_category_id IS NULL AND t2.brand_id IS NULL LIMIT 1) AS sel ON sel.probe_key_id = upd.probe_key_id SET upd.account_id = :account_id, upd.probe_being_handled = 1';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['account_id'=>$_SESSION['id'],'ticket'=>$_POST['ticket']]);
        } else if ($_POST['client_cat'] == 0 && $_POST['brand'] != 0 ) {
            $sql = 'UPDATE probe_queue AS upd INNER JOIN (SELECT t1.probe_key_id FROM probe_queue AS t1 INNER JOIN probe AS t2 ON t2.probe_key_id = t1.probe_key_id WHERE t1.probe_being_handled = 0 AND t1.account_id IS NULL AND t2.probe_ticket_id = :ticket AND t2.client_category_id IS NULL AND t2.brand_id = :brand_id LIMIT 1) AS sel ON sel.probe_key_id = upd.probe_key_id SET upd.account_id = :account_id, upd.probe_being_handled = 1';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['account_id'=>$_SESSION['id'],'ticket'=>$_POST['ticket'], 'brand_id'=>(int)$_POST['brand']]);
        } else if ($_POST['brand'] == 0 && $_POST['client_cat'] != 0) {
            $sql = 'UPDATE probe_queue AS upd INNER JOIN (SELECT t1.probe_key_id FROM probe_queue AS t1 INNER JOIN probe AS t2 ON t2.probe_key_id = t1.probe_key_id WHERE t1.probe_being_handled = 0 AND t1.account_id IS NULL AND t2.probe_ticket_id = :ticket AND t2.client_category_id = :client_id AND t2.brand_id IS NULL LIMIT 1) AS sel ON sel.probe_key_id = upd.probe_key_id SET upd.account_id = :account_id, upd.probe_being_handled = 1';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['account_id'=>$_SESSION['id'],'ticket'=>$_POST['ticket'], 'client_id'=>(int)$_POST['client_cat']]);
        } else {
            $sql = 'UPDATE probe_queue AS upd INNER JOIN (SELECT t1.probe_key_id FROM probe_queue AS t1 INNER JOIN probe AS t2 ON t2.probe_key_id = t1.probe_key_id WHERE t1.probe_being_handled = 0 AND t1.account_id IS NULL AND t2.probe_ticket_id = :ticket AND t2.client_category_id = :client_id AND t2.brand_id = :brand_id LIMIT 1) AS sel ON sel.probe_key_id = upd.probe_key_id SET upd.account_id = :account_id, upd.probe_being_handled = 1';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['account_id'=>$_SESSION['id'],'ticket'=>$_POST['ticket'], 'client_id'=>(int)$_POST['client_cat'], 'brand_id'=>(int)$_POST['brand']]);
        
        }

        $sql = 'SELECT probe_queue_id FROM probe_queue WHERE account_id = :account_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['account_id'=>$_SESSION['id']]);
        $probe_info = $stmt->fetch(PDO::FETCH_OBJ);
        $last_id = $probe_info->probe_queue_id;
        $this_count = $stmt->rowCount(PDO::FETCH_OBJ);
        $iterations++;
    } while ($this_count == 0  && $iterations < 10);
}
$sql = 'SELECT brand.brand_name, client_category.client_category_name, probe.probe_id, project_tickets.ticket_id FROM  probe_queue LEFT JOIN probe ON probe_queue.probe_key_id = probe.probe_key_id  INNER JOIN project_tickets ON probe.probe_ticket_id = project_tickets.project_ticket_system_id LEFT JOIN brand ON probe.brand_id = brand.brand_id LEFT JOIN client_category ON probe.client_category_id  = client_category.client_category_id WHERE probe_queue_id = :probe_queue_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['probe_queue_id'=>$last_id]);
$probe_info = $stmt->fetch(PDO::FETCH_OBJ);
$return_arr[] = array("brand_name" => $probe_info->brand_name, "client_category_name" => $probe_info->client_category_name , "probe_id"=>$probe_info->probe_id, "ticket"=>$probe_info->ticket_id);
echo json_encode($return_arr);
?>
