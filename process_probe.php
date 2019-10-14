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
$keys = array_shift($csvAsArray);
$csvAsArray = mb_convert_encoding($csvAsArray, "UTF-8", "ISO-8859-15");
foreach ($csvAsArray as $i=>$row) {
    $csvAsArray[$i] = array_combine($keys, $row);
}

$total_count = count($csvAsArray);
$pdo->beginTransaction();
for ($i = 0; $i < $total_count; $i++) {
    if ($csvAsArray[$i]["probes_with_high_other_percent"] != '') {
        $brand = $csvAsArray[$i]["brand"];
        $category = $csvAsArray[$i]["category"];
        $un_list = explode (",", $csvAsArray[$i]["probes_with_high_other_percent"]);
        $probe_list = array_map('trim',$un_list);
        
        if ($brand != '') {
            $sql = 'SELECT brand_id FROM brand WHERE brand_name = :brand_name';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['brand_name'=>$brand]);
            $brand_info = $stmt->fetch(PDO::FETCH_OBJ);
            $brand_row_count = $stmt->rowCount(PDO::FETCH_OBJ);
            if ($brand_row_count == 0) {
                $sql = 'INSERT INTO brand (brand_name) VALUES (:brand_name)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['brand_name'=>$brand]);

                $sql = 'SELECT brand_id FROM brand WHERE brand_name = :brand_name';
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['brand_name'=>$brand]);
                $brand_info = $stmt->fetch(PDO::FETCH_OBJ);
            }
            $brand_id = $brand_info->brand_id;
        } else {
            $brand_id = null;
        }

        if ($category != '') {
            $sql = 'SELECT client_category_id FROM client_category WHERE client_category_name = :client_category_name';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['client_category_name'=>$category]);
            $category_info = $stmt->fetch(PDO::FETCH_OBJ);
            $category_row_cout = $stmt->rowCount(PDO::FETCH_OBJ);

            if ($category_row_cout == 0) {
                $sql = 'INSERT INTO client_category (client_category_name) VALUES (:client_category_name)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['client_category_name'=>$category]);

                $sql = 'SELECT client_category_id FROM client_category WHERE client_category_id = :client_category_id';
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['client_category_id'=>$category]);
                $category_info = $stmt->fetch(PDO::FETCH_OBJ);
            }
            $category_id = $category_info->client_category_id;
        } else {
            $category_id = null;
        }

        for ($j = 0; $j < min(10,count($probe_list)); $j++) {
            if ($probe_list[$j] != '') {
                $sql = "INSERT INTO probe (brand_id, client_category_id, probe_id, probe_added_user_id, probe_ticket_id) VALUES (:brand_id, :client_category_id, :probe_id, :probe_added_user_id, :probe_ticket_id)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['brand_id'=>$brand_id, 'client_category_id'=>$category_id, 'probe_id'=>$probe_list[$j], 'probe_added_user_id'=>$_SESSION['id'], 'probe_ticket_id'=>$_POST['ticket_name']]);
                $last_id = (int)$pdo->lastInsertId();

                $sql = 'INSERT INTO probe_queue (probe_key_id) VALUES (:probe_key_id)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['probe_key_id'=>$last_id]);
            }
        }
    }
}
$pdo->commit();
?>