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

$error_message = '';
$selected = $_POST['selected'];
$selected_array = explode(',', $selected);
$count = 0;

foreach ($selected_array as $selected_id) {
    $sql = 'SELECT pt.ticket_escalate, pt.ticket_status FROM project_tickets pt WHERE pt.project_ticket_system_id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id'=>$selected_id]);
    $ticket_info = $stmt->fetchAll(PDO::FETCH_OBJ);
    $current_escalate_status = $ticket_info->ticket_escalate;
    $current_ticket_status = $ticket_info->ticket_status;

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

    $ticket_status = $_POST['ticket_status'];
    if ($ticket_status == "null") {
        $ticket_status = $current_ticket_status;
        $pdo->beginTransaction();
        $sql = 'UPDATE project_tickets pt 
                SET 
                    pt.ticket_escalate = :escalate, 
                    pt.ticket_escalate_date = :escalate_date,
                    pt.ticket_last_mod_account_id = :account_id,
                    pt.ticket_last_mod_date = :mod_date,
                    pt.ticket_completion_date = :close_date
                WHERE 
                      pt.project_ticket_system_id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'escalate' => $_POST['ticket_escalate'],
            'escalate_date' => $escalate_date,
            'account_id' => $_SESSION['id'],
            'mod_date' => $date,
            'close_date' => $close_date,
            'id' => $selected_id
        ]);
        $pdo->commit();
    } else {
        $pdo->beginTransaction();
        $sql = 'UPDATE project_tickets pt 
                SET 
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
            'ticket_status' => $ticket_status,
            'escalate' => $_POST['ticket_escalate'],
            'escalate_date' => $escalate_date,
            'account_id' => $_SESSION['id'],
            'mod_date' => $date,
            'close_date' => $close_date,
            'id' => $selected_id
        ]);
        $pdo->commit();
    }

    $sql = 'SELECT a.account_gid FROM user_db.accounts a WHERE a.account_id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'id' => $_SESSION['id']
    ]);
    $user_gid = $stmt->fetchColumn();

    $update_info[$count] = array(
        'gid' => $user_gid,
        'date' => $date,
        'close_date' => $close_date
    );
    $count++;
}


$return_arr[] = array("update_info" => $update_info, "error_message" => $error_message);
echo json_encode($return_arr);

