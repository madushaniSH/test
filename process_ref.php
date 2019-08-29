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

$total_count = count($csvAsArray);
for ($i = 0; $i < $total_count; $i++) {
    $user_id = $_SESSION['id'];

    if ($csvAsArray[$i]["Recognition Level"] != ''){
        $recognition_level = $csvAsArray[$i]["Recognition Level"];
    } else {
        $recognition_level = NULL;
    }
    
    if ($csvAsArray[$i]["EAN"] != ''){
        $ean = $csvAsArray[$i]["EAN"];
    } else {
        $ean = NULL;
    }

    if ($csvAsArray[$i]["Short Name"] != ''){
        $short_name = $csvAsArray[$i]["Short Name"];
    } else {
        $short_name = NULL;
    }

    if ($csvAsArray[$i]["Category"] != ''){
        $category = $csvAsArray[$i]["Category"];
    } else {
        $category = NULL;
    }

    if ($csvAsArray[$i]["Sub Category"] != ''){
        $sub_category = $csvAsArray[$i]["Sub Category"];
    } else {
        $sub_category = NULL;
    }

    if ($csvAsArray[$i]["Brand"] != ''){
        $brand = $csvAsArray[$i]["Brand"];
    } else {
        $brand = NULL;
    }

    if ($csvAsArray[$i]["Sub Brand"] != ''){
        $sub_brand = $csvAsArray[$i]["Sub Brand"];
    } else {
        $sub_brand = NULL;
    }

    if ($csvAsArray[$i]["Manufacturer"] != ''){
        $manufactuer = $csvAsArray[$i]["Manufacturer"];
    } else {
        $manufactuer = NULL;
    }

    if ($csvAsArray[$i]["Base Size"] != ''){
        $base_size = $csvAsArray[$i]["Base Size"];
    } else {
        $base_size = NULL;
    }

    if ($csvAsArray[$i]["Size"] != ''){
        $size = $csvAsArray[$i]["Size"];
    } else {
        $size = NULL;
    }

    if ($csvAsArray[$i]["Measurement Unit"] != ''){
        $measurement_unit = $csvAsArray[$i]["Measurement Unit"];
    } else {
        $measurement_unit = NULL;
    }

    if ($csvAsArray[$i]["Container Type"] != ''){
        $container_type = $csvAsArray[$i]["Container Type"];
    } else {
        $container_type = NULL;
    }

    if ($csvAsArray[$i]["Agg Level"] != ''){
        $agg_level = $csvAsArray[$i]["Agg Level"];
    } else {
        $agg_level = NULL;
    }

    if ($csvAsArray[$i]["Segment"] != ''){
        $segment = $csvAsArray[$i]["Segment"];
    } else {
        $segment = NULL;
    }

    if ($csvAsArray[$i]["Count of UPC2"] != ''){
        $count_upc2 = $csvAsArray[$i]["Count of UPC2"];
    } else {
        $count_upc2 = NULL;
    }

    if ($csvAsArray[$i]["Flavor Detail (attribute)"] != ''){
        $flavor_detail = $csvAsArray[$i]["Flavor Detail (attribute)"];
    } else {
        $flavor_detail = NULL;
    }

    if ($csvAsArray[$i]["Case Pack (attribute)"] != ''){
        $case_pack = $csvAsArray[$i]["Case Pack (attribute)"];
    } else {
        $case_pack = NULL;
    }

    if ($csvAsArray[$i]["Multi Pack (attribute)"] != ''){
        $multi_pack = $csvAsArray[$i]["Multi Pack (attribute)"];
    } else {
        $multi_pack = NULL;
    }

    $sql = "INSERT INTO `reference_info`(reference_recognition_level, reference_ean, reference_short_name, reference_category, reference_sub_category, reference_brand, reference_sub_brand, reference_manufacturer, reference_base_size, reference_size, reference_measurement_unit, reference_container_type, reference_agg_level, reference_segment, reference_count_upc2, reference_flavor_detail, reference_case_pack, reference_multi_pack, reference_added_user_id) VALUES (:reference_recognition_level, :reference_ean, :reference_short_name, :reference_category, :reference_sub_category, :reference_brand, :reference_sub_brand, :reference_manufacturer, :reference_base_size, :reference_size, :reference_measurement_unit, :reference_container_type, :reference_agg_level, :reference_segment, :reference_count_upc2, :reference_flavor_detail, :reference_case_pack, :reference_multi_pack, :reference_added_user_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['reference_recognition_level'=>$recognition_level, 'reference_ean'=>$ean, 'reference_short_name'=>$short_name, 'reference_category'=>$category, 'reference_sub_category'=>$sub_category, 'reference_brand'=>$brand, 'reference_sub_brand'=>$sub_brand, 'reference_manufacturer'=>$manufactuer, 'reference_base_size'=>$base_size, 'reference_size'=>$size, 'reference_measurement_unit'=>$measurement_unit, 'reference_container_type'=>$container_type, 'reference_agg_level'=>$agg_level, 'reference_segment'=>$segment, 'reference_count_upc2'=>$count_upc2, 'reference_flavor_detail'=>$flavor_detail, 'reference_case_pack'=>$case_pack, 'reference_multi_pack'=>$multi_pack, 'reference_added_user_id'=>$user_id]);
    $last_id = (int)$pdo->lastInsertId();

    $sql = 'INSERT INTO reference_queue (reference_info_key_id) VALUES (:reference_info_key_id)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['reference_info_key_id'=>$last_id]);
}
?>