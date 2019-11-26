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
    $sql = 'SELECT pt.ticket_escalate FROM project_tickets pt WHERE pt.project_ticket_system_id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id'=>$_POST['ticket_system_id']]);
    $current_escalate_status = $stmt->fetchColumn();

    $now = new DateTime();
    $date = $now->format('Y-m-d H:i:s');
    $escalate_date = $date;
    $close_date = $date;

    if ($current_escalate_status !== $_POST['ticket_escalate']) {
        $escalate_date = NULL;
    }

    if($_POST['ticket_status'] !== 'CLOSED') {
        $close_date = NULL;
    }

    $ticket_comment = $_POST['ticket_comment'];
    if ($ticket_comment == 'null') {
        $ticket_comment = NULL;
    }


    $pdo->beginTransaction();
    $sql = 'UPDATE project_tickets pt 
                SET 
                    pt.ticket_comment = :ticket_comment, 
                    pt.ticket_status = :ticket_status, 
                    pt.ticket_escalate = :escalate, 
                    pt.ticket_escalate_date = :escalate_date,
                    pt.ticket_last_mod_account_id = :account_id,
                    pt.ticket_last_mod_date = :mod_date,
                    pt.ticket_completion_date = :close_date
                WHERE 
                      pt.project_ticket_system_id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'ticket_comment' => $ticket_comment,
        'ticket_status' => $_POST['ticket_status'],
        'escalate' => $_POST['ticket_escalate'],
        'escalate_date' => $escalate_date,
        'account_id' => $_SESSION['id'],
        'mod_date' => $date,
        'close_date' => $close_date,
        'id' => $_POST['ticket_system_id']
    ]);
    $pdo->commit();

    $sql = 'SELECT a.account_gid FROM user_db.accounts a WHERE a.account_id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'id' => $_SESSION['id']
    ]);
    $user_gid = $stmt->fetchColumn();

    $update_info = array(
        'gid' => $user_gid,
        'date' => $date,
        'close_date' => $close_date
    );


} catch (PDOException $e) {
    $ticket_info = $e->getMessage();
}

$return_arr[] = array("update_info" => $update_info);
echo json_encode($return_arr);

