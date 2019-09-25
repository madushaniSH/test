<?php header('Content-type: text/plain; charset=utf-8'); ?>
<?php
/*
    Filename: upload_probe.php
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
$dbname = $_POST['db_name'];
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

$tmpName = $_FILES['csv']['tmp_name'];
$csvAsArray = array_map('str_getcsv', file($tmpName,FILE_SKIP_EMPTY_LINES));
$csvAsArray = mb_convert_encoding($csvAsArray, "UTF-8", "ISO-8859-15");
$keys = array_shift($csvAsArray);
foreach ($csvAsArray as $i=>$row) {
    $csvAsArray[$i] = array_combine($keys, $row);
}
$total_count = count($csvAsArray);
$skipped_count = 0;
$proccessed_rows = 0;

$pdo->beginTransaction();
for ($i = 0; $i < $total_count; $i++) {
    $brand_missing = false;
    $category_missing = false;
    $user_id = $_SESSION['id'];

    if ($csvAsArray[$i]["category"] != ''){
        $client_category = $csvAsArray[$i]["category"];
    } else {
        $category_missing = true;
    }
    
    if ($csvAsArray[$i]["brand"] != ''){
        $brand = $csvAsArray[$i]["brand"];
    } else {
        $brand_missing = true;
    }

    if (!$brand_missing && !$category_missing) {
        $sql = 'INSERT INTO radar_hunt (radar_category, radar_brand, radar_added_user_id, radar_ticket_id) VALUES (:radar_category, :radar_brand, :radar_added_user_id, :radar_ticket_id)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['radar_category'=>$client_category, 'radar_brand'=>$brand, 'radar_added_user_id'=>$user_id, 'radar_ticket_id'=>$_POST['ticket_name']]);
        $last_id = (int)$pdo->lastInsertId();

        $sql = 'INSERT INTO radar_queue (radar_hunt_key_id) VALUES (:radar_hunt_key_id)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['radar_hunt_key_id'=>$last_id]);
        $proccessed_rows++;
    }

    if ($brand_missing || $category_missing) {
        $skipped_count++;
    }
}
$pdo->commit();
$return_arr[] = array("proccessed_rows" => $proccessed_rows, "skipped_count"=>$skipped_count);
echo json_encode($return_arr);
?>