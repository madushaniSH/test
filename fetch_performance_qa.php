<?php
/*
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

$sql = "SELECT a.account_id, a.account_gid as 'QA GID', a.account_first_name as 'QA Name' FROM user_db.accounts a INNER JOIN user_db.account_designations ad ON a.account_id = ad.account_id WHERE ad.designation_id = 4";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$qaSummary = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = 'SELECT project_db_name FROM `project_db`.projects WHERE 1';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$project_array = $stmt->fetchAll(PDO::FETCH_ASSOC);

for ($i = 0; $i < count($qaSummary); ++$i) {
    $qaSummary[$i]["Total Checked Count"] = 0;
    $qaSummary[$i]["Approved Count"] = 0;
    $qaSummary[$i]["Disapproved Count"] = 0;
    $qaSummary[$i]["Rename Count"] = 0;
    $qaSummary[$i]["Error Count"] = 0;
    $qaSummary[$i]["ODA Rejected"] = 0;

    for ($j = 0; $j < count($project_array); ++$j) {
        $dbname = $project_array[$j]["project_db_name"];
        $sql = '
    SELECT COUNT(*) AS "total",
    SUM(
        CASE WHEN(
            p.product_qa_status = "approved" OR p.product_qa_status = "active" OR p.product_qa_status = "rejected"
        ) THEN 1 ELSE 0
    	END
	) AS "approved",
       SUM(
        CASE WHEN(
            p.product_qa_status = "disapproved"
        ) THEN 1 ELSE 0
    	END
	) AS "disapproved",
     SUM(
        CASE WHEN(
            p.product_previous IS NOT NULL OR p.product_alt_design_previous IS NOT NULL
        ) THEN 1 ELSE 0
    	END
	) AS "re",
     SUM(
        CASE WHEN(
            p.product_qa_status = "rejected"
        ) THEN 1 ELSE 0
    	END
	) AS "rejected",
   SUM(
        CASE WHEN(
            pqe.product_id IS NOT NULL
        ) THEN 1 ELSE 0
    	END
	) AS "error"
FROM
    ' . $dbname . '.products p
LEFT OUTER JOIN 
	' . $dbname . '.product_qa_errors pqe ON pqe.product_id = p.product_id
WHERE
    p.product_qa_account_id = :account_id AND p.product_qa_datetime >= :start_datetime AND p.product_qa_datetime <= :end_datetime';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["account_id" => $qaSummary[$i]["account_id"], "start_datetime" => $_POST['start_datetime'], "end_datetime" => $_POST['end_datetime']]);
        $qaInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        $qaSummary[$i]["Total Checked Count"] += $qaInfo["total"];
        $qaSummary[$i]["Approved Count"] += $qaInfo["approved"];
        $qaSummary[$i]["Disapproved Count"] += $qaInfo["disapproved"];
        $qaSummary[$i]["Rename Count"] += $qaInfo["re"];
        $qaSummary[$i]["Error Count"] += $qaInfo["error"];
        $qaSummary[$i]["ODA Rejected"] += $qaInfo["rejected"];

    }
}


$return_arr[] = array("qaSummary" => $qaSummary);
echo json_encode($return_arr);
?>
