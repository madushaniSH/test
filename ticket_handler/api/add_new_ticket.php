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

$sql = 'SELECT * FROM project_tickets pt WHERE pt.ticket_id = :id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['id'=>$_POST['ticket_id']]);
$row_count = $stmt->rowCount();
$error_message = '';

if ($row_count === 0) {
    $ticket_description = trim($_POST['ticket_description']);
    if ($ticket_description === '') $ticket_description = NULL;

    $ticket_comment = trim($_POST['ticket_comment']);
    if ($ticket_comment === '') $ticket_comment = NULL;

    $now = new DateTime();
    $date = $now->format('Y-m-d H:i:s');
    $create_date = $now->format('Y-m-d');
    $escalate_date = $date;
    $close_date = $date;

    if($_POST['ticket_escalate'] !== '1') $escalate_date = NULL;
    if($_POST['ticket_status'] !== 'CLOSED') $close_date = NULL;

    $pdo->beginTransaction();
    try {
        $sql = 'INSERT INTO project_tickets 
                (ticket_id, ticket_type, ticket_status, ticket_description, ticket_comment, account_id,
                    ticket_creation_time, ticket_escalate, ticket_escalate_date, ticket_completion_date,
                    ticket_last_mod_account_id, ticket_last_mod_date)
            VALUES 
                (:id, :type, :ticket_status, :ticket_description, :ticket_comment, :account_id, 
                    :create_date, :escalate, :escalate_date, :close_date, :mod_id, :mod_date)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $_POST['ticket_id'],
            'type' => $_POST['ticket_type'],
            'ticket_status' => $_POST['ticket_status'],
            'ticket_description' => $ticket_description,
            'ticket_comment' => $ticket_comment,
            'account_id' => $_SESSION['id'],
            'create_date' => $date,
            'escalate' => $_POST['ticket_escalate'],
            'escalate_date' => $escalate_date,
            'close_date' => $close_date,
            'mod_id' => $_SESSION['id'],
            'mod_date' => $date,
        ]);
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
    }
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
        'create_date' => $create_date,
        'close_date' => $close_date
    );

} else {
    $error_message = 'Duplicate Ticket';
}

$return_arr[] = array("error_message" =>$error_message, "update_info" => $update_info);
echo json_encode($return_arr);

