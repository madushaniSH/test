<?php
/*
    Filename: attribute_list.php
    Author: Malika Liyanage
    Created: 02/08/2019
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

$sql = 'SELECT attribute_id, attribute_name FROM attribute';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$attribute_rows = $stmt->fetchAll(PDO::FETCH_OBJ);

foreach($attribute_rows as $attribute_row){
    echo "
    <div class=\"form-check\">
    <input class=\"form-check-input\" type=\"checkbox\" value=\"$attribute_row->attribute_id\" id=\"$attribute_row->attribute_name\">
    <label class=\"form-check-label\" for=\"$attribute_row->attribute_name\">
    $attribute_row->attribute_name
    </label>
    </div>";
}
?>