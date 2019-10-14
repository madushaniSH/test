<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
	header('Location: login_auth_one.php');
	exit();
}

// Current settings to connect to the user account database
require('product_connection.php');
$dsn = 'mysql:host='.$host.';dbname='.$_POST['project_name'];

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
    echo "<p>Connection to database failed<br>Reason: ".$e->getMessage().'</p>';
    exit();
}
echo "<option value=\"\"selected disabled>Select</option>";
$sql = 'SELECT probe_status_id, probe_status_name FROM probe_status';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$probe_status_rows = $stmt->fetchAll(PDO::FETCH_OBJ);
foreach ($probe_status_rows as $probe_status_row) {
    echo "<option value=\"$probe_status_row->probe_status_id\">$probe_status_row->probe_status_name</option>";
}
?>
