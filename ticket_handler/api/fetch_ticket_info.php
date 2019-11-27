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
    $sql = 'SELECT pt.project_ticket_system_id,DATE(pt.ticket_creation_time) as "create_date",pt.ticket_type, pt.ticket_description, pt.ticket_id, pt.ticket_status, a.account_gid, pt.ticket_comment,
            b.account_gid as "mod_gid", pt.ticket_last_mod_date, pt.ticket_escalate, pt.ticket_escalate_date, pt.ticket_completion_date
            from '.$dbname.'.project_tickets pt
                INNER JOIN user_db.accounts a
                    ON a.account_id = pt.account_id
                LEFT OUTER JOIN user_db.accounts b
                    ON b.account_id = pt.ticket_last_mod_account_id
             WHERE 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ticket_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $ticket_info = $e->getMessage();
}

$return_arr[] = array("ticket_info" => $ticket_info);
echo json_encode($return_arr);
