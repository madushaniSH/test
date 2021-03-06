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
// Current settings to connect to the user account database
require('../../user_db_connection.php');
$dbname = $_POST['db_name'];
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

$status_array =  explode(',', $_POST['status_array']);

for ($i = 0; $i < count($status_array); $i++){
    if ($i == 0) {
        $status_string .= 'pt.ticket_status = "'.$status_array[$i].'"';
    } else {
        $status_string .= ' OR pt.ticket_status = "'.$status_array[$i].'"';
    }
}
$sql = 'SELECT pt.ticket_id FROM project_tickets pt WHERE '.$status_string;
$stmt = $pdo->prepare($sql);
$stmt->execute();
$ticket_array = $stmt->fetchAll(PDO::FETCH_ASSOC);

$return_arr[] = array("ticket_info" => $ticket_array);
echo json_encode($return_arr);
