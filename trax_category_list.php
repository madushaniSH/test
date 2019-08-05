<?php
/*
    Filename: trax_category_list.php
    Author: Malika Liyanage
*/

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
	header('Location: login_auth_one.php');
	exit();
}

// Current settings to connect to the user account database
require('product_connection.php');

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

$sql = 'SELECT project_trax_category_id, project_trax_category_name FROM project_trax_category';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$trax_catergory_rows = $stmt->fetchAll(PDO::FETCH_OBJ);


echo "<option value=\"\" selected disabled>Select</option>";
foreach($trax_catergory_rows as $trax_catergory_row){
    echo "<option value=\"$trax_catergory_row->project_trax_category_id\">$trax_catergory_row->project_trax_category_name</option>";
}
?>