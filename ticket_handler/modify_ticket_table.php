<?php
session_start();
// Current settings to connect to the user account database
require('../user_db_connection.php');
$dbname = 'project_db';
// Setting up the DSN
$dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
/*
    Attempts to connect to the databse, if no connection was estabishled
    kills the script
*/
$user = 'root';
$pwd = '$$ma12qwqwSr4';
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
$sql = 'SELECT project_db_name FROM projects WHERE 1';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$project_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
for ($i = 0; $i < count($project_info); $i++) {
    $dbname = $project_info[$i]["project_db_name"];
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
    $pdo = new PDO($dsn, $user, $pwd);
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    echo "<p>" . $dbname . "</p>";
    try {
        $sql = 'ALTER TABLE project_tickets
                DROP is_probe_hunt,
                DROP is_ref_hunt,
                ADD ticket_type 
                    ENUM(\'APOC Radar\',\'Radar\',\'Data Health\',\'Type E - SKU Hunt/Data Collection\',\'NA\') 
                    NOT NULL DEFAULT \'NA\' 
                    AFTER `ticket_creation_time`,
                ADD `ticket_description` VARCHAR(500) NULL DEFAULT NULL,
                ADD `ticket_status` 
                    ENUM(\'IN PROGRESS\',\'OPEN\',\'CLOSED\',\'DONE\', \'IN PROGRESS / SEND TO EAN\') 
                    NOT NULL DEFAULT \'IN PROGRESS\',
                ADD `ticket_completion_date` DATETIME NULL DEFAULT NULL,
                ADD `ticket_last_mod_account_id` INT NULL DEFAULT NULL,
                ADD `ticket_last_mod_date` DATETIME NULL DEFAULT NULL,
                ADD `ticket_comment` VARCHAR(2550) NULL DEFAULT NULL,            
                ADD `ticket_escalate` BOOLEAN NOT NULL DEFAULT FALSE,
                ADD `ticket_escalate_date` DATETIME NULL DEFAULT NULL
                ';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $sql = 'ALTER TABLE project_tickets
                ADD CONSTRAINT '.$dbname.'_ticket_last_mod_account_id
                    FOREIGN KEY (`ticket_last_mod_account_id`) REFERENCES `user_db`.`accounts` (`account_id`)
                    ';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    } catch (PDOException $e) {
        $warning = $e->getMessage();
    }
    echo $warning;
}
