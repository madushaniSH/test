<?php
/*
    Filename: assign_qa_product.php
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
$dbname = 'project_db';
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

$sql = 'SELECT project_db_name FROM projects WHERE 1';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$project_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

for ($i = 0; $i < count($project_info); $i++) {
    $dbname = $project_info[$i]["project_db_name"];
    $dsn = 'mysql:host='.$host.';dbname='.$dbname;
    $pdo = new PDO($dsn, $user, $pwd);
    echo "<p>".$dbname."</p>";
    try {
        $sql = 'CREATE TABLE IF NOT EXISTS '. $dbname.'.`project_tickets` (
    `project_ticket_system_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `ticket_id` varchar(255) NOT NULL,
    `account_id` int(11) NOT NULL,
    `ticket_creation_time` datetime NOT NULL DEFAULT current_timestamp(),
    `is_probe_hunt` tinyint(1) NOT NULL DEFAULT 0,
    `is_ref_hunt` tinyint(1) NOT NULL DEFAULT 0
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    } catch(PDOException $e) {
        echo $e->getMessage();//Remove or change message in production code
    }

    $sql = "INSERT INTO project_tickets (ticket_id, account_id) VALUES ('N/A', 1)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $last_id = (int)$pdo->lastInsertId();

    $sql = "ALTER TABLE `probe` ADD `probe_ticket_id` int(11) DEFAULT NULL";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = "ALTER TABLE `probe` ADD KEY `PROBE_PROJECT_TICKET_ID` (`probe_ticket_id`)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'ALTER TABLE `probe` ADD CONSTRAINT '.$dbname.'_PROBE_PROJECT_TICKET_ID FOREIGN KEY (`probe_ticket_id`) REFERENCES `project_tickets` (`project_ticket_system_id`)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'UPDATE probe SET probe_ticket_id = :ticket WHERE probe_ticket_id IS NULL';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket'=>$last_id]);

}

?>