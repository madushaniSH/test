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
foreach ($csvAsArray as $i=>$row) {
    $csvAsArray[$i] = array_combine($keys, $row);
}

$result = array();
$counter = 0;
$total_count = count($csvAsArray);
try {
$pdo->beginTransaction();
for ($i = 0; $i < $total_count; $i++) {
    $found = false;
    $product_name = trim($csvAsArray[$i]["English Product Name"]);
    $client_cat_name = trim($csvAsArray[$i]["Client Category Name"]);
    if ($product_name != '') {
        // checking if client category exists
        $sql = 'SELECT client_category_id, client_category_name FROM client_category WHERE client_category_name = :client_category_name';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['client_category_name'=>$client_cat_name]);
        $client_cat_info = $stmt->fetch(PDO::FETCH_ASSOC);
        $clientCatId = $client_cat_info["client_category_id"];
        $rowCount = $stmt->rowCount(PDO::FETCH_ASSOC);
        if ($rowCount == 0) {
            $sql = 'INSERT INTO client_category (client_category_name) VALUES (:client_category_name)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['client_category_name'=>$client_cat_name]);

            $sql = 'SELECT client_category_id, client_category_name FROM client_category WHERE client_category_name = :client_category_name';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['client_category_name'=>$client_cat_name]);
            $client_cat_info = $stmt->fetch(PDO::FETCH_ASSOC);
            $clientCatId = $client_cat_info["client_category_id"];
        }

        // fetching products with identical product names
        $sql = 'SELECT product_id, product_name FROM products WHERE product_name = :product_name';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['product_name'=>$product_name]);
        $rowCount = $stmt->rowCount(PDO::FETCH_ASSOC);

        if ($rowCount != 0) {
            $found = true;
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            for ($j = 0; $j < count($products); $j++) {
                $sql = 'SELECT product_client_category_id FROM product_client_category WHERE product_id = :product_id';
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['product_id'=>$products[$j]["product_id"]]);
                $rowCount = $stmt->rowCount(PDO::FETCH_ASSOC);
                $productClientCatInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                $productClientCatId = $productClientCatInfo["prodcut_client_category_id"];
                
                if ($rowCount == 0) {
                    $sql = 'INSERT INTO product_client_category (product_id, client_category_id) VALUES (:product_id, :client_category_id)';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['product_id'=>$products[$j]["product_id"], "client_category_id"=>$clientCatId]);
                } else {
                    $sql = 'UPDATE product_client_category SET client_category_id = :client_category_id WHERE product_id = :product_id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['product_id'=>$products[$j]["product_id"], "client_category_id"=>$clientCatId]);
                }
            }
        }
        if (!$found) {
            $result[$counter]["product_name"] = $product_name;
            $counter++;
        }
    }
}
$pdo->commit();
} catch (PDOException $e) {
    $warning = $e->getMessage();
}
$return_arr[] = array('info'=>$warning, "result"=>$result);
echo json_encode($return_arr);
?>