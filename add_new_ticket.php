<?php
/*
    Filename: add_new_ticket.php
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

$error = "";
$sql = "SELECT ticket_id FROM project_tickets WHERE ticket_id = :ticket_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['ticket_id'=>$_POST['ticket_id']]);
$row_count = $stmt->rowCount(PDO::FETCH_OBJ);

if ($row_count == 0) {
    try {
        $sql = 'INSERT INTO project_tickets (ticket_id, account_id) VALUES (:ticket_id, :account_id)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['ticket_id'=>$_POST['ticket_id'], 'account_id'=>$_SESSION['id']]);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    $error = "Duplicate Ticket ID";
}
$return_arr[] = array("error"=>$error);
echo json_encode($return_arr);
?>