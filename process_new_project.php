<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
    header('Location: login_auth_one.php');
    exit();
}

// Current settings to connect to the user account database
require('product_connection.php');
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
    echo "<p>Connection to $dbname database failed<br>Reason: " . $e->getMessage() . '</p>';
    exit();
}

$sql = 'SELECT project_id FROM projects WHERE project_name = :project_name';
$stmt = $pdo->prepare($sql);
$stmt->execute(['project_name' => $_POST['project_name']]);
$row_count = $stmt->rowCount();

if ($row_count == 0) {
    $sql = 'INSERT INTO projects (project_name, project_region, project_db_name, project_language) VALUES (:project_name, :project_region, :project_db_name, :project_language)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['project_name' => $_POST['project_name'], 'project_region' => $_POST['project_region'], 'project_db_name' => $_POST['project_database'], 'project_language' => $_POST['project_type']]);

    $sql = 'CREATE DATABASE IF NOT EXISTS ' . $_POST['project_database'] . ' DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $pdo = NULL;
    $dbname = $_POST['project_database'];
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
        echo "<p>Connection to $dbname database failed<br>Reason: " . $e->getMessage() . '</p>';
        exit();
    }

    $sql = 'CREATE TABLE `brand` (
        `brand_id` int(11) NOT NULL  PRIMARY KEY AUTO_INCREMENT,
        `brand_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `client_category` (
        `client_category_id` int(11) NOT NULL  PRIMARY KEY AUTO_INCREMENT,
        `client_category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `probe_status` (
        `probe_status_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `probe_status_name` varchar(255) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = "INSERT INTO `probe_status` (`probe_status_id`, `probe_status_name`) VALUES
    (1, \"Already Added\"),
    (2, \"Hunted\"),
    (3, \"Irrelevant\"),
    (4, \"Blur\"),
    (5, \"Validation Error\"),
    (6, \"Brand Level\"),
    (7, \"Size Can't Find\"),
    (8, \"Count Can't Find\"),
    (9, \"Description Can't Find\"),
    (10, \"Flavour Can't Find\"),
    (11, \"Container Type Can't Find\"),
    (12, \"Sub Brand Can't Find\"),
    (13, \"Brand Not Found\"),
    (14, \"Partially Visible\")";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `project_tickets` (
      `project_ticket_system_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
      `ticket_id` varchar(255) NOT NULL,
      `account_id` int(11) NOT NULL,
      `ticket_creation_time` datetime NOT NULL DEFAULT current_timestamp(),
        ticket_type 
                    ENUM(\'APOC Radar\',\'Radar\',\'Data Health\',\'Type E - SKU Hunt/Data Collection\',\'NA\',\'Internal\') 
                    NOT NULL DEFAULT \'NA\' 
        ,
        `ticket_description` VARCHAR(500) NULL DEFAULT NULL,
        `ticket_status` 
                    ENUM(\'IN PROGRESS\',\'OPEN\',\'CLOSED\',\'DONE\', \'IN PROGRESS / SEND TO EAN\') 
                    NOT NULL DEFAULT \'IN PROGRESS\',
        `ticket_completion_date` DATETIME NULL DEFAULT NULL,
        `ticket_last_mod_account_id` INT NULL DEFAULT NULL,
        `ticket_last_mod_date` DATETIME NULL DEFAULT NULL,
        `ticket_comment` VARCHAR(2550) NULL DEFAULT NULL,            
        `ticket_escalate` BOOLEAN NOT NULL DEFAULT FALSE,
        `ticket_escalate_date` DATETIME NULL DEFAULT NULL,
        CONSTRAINT ' . $dbname . '_ticket_last_mod_account_id
                    FOREIGN KEY (`ticket_last_mod_account_id`) REFERENCES `user_db`.`accounts` (`account_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `probe` (
        `probe_key_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `brand_id` int(11) DEFAULT NULL,
        `client_category_id` int(11) DEFAULT NULL,
        `probe_id` varchar(255) NOT NULL,
        `probe_added_date` datetime NOT NULL DEFAULT current_timestamp(),
        `probe_added_user_id` int(11) NOT NULL,
        `probe_start_datetime` DATETIME NULL DEFAULT NULL,
        `probe_hunter_processed_time` datetime DEFAULT NULL,
        `probe_process_comment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `probe_processed_hunter_id` int(11) DEFAULT NULL,
        `probe_process_remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `probe_status_id` int(11) DEFAULT NULL,
        `probe_ticket_id` int(11) DEFAULT NULL,
         CONSTRAINT  ' . $dbname . '_BRAND_ID FOREIGN KEY (`brand_id`) REFERENCES `brand` (`brand_id`),
         CONSTRAINT  ' . $dbname . '_CLIENT_CATEGORY_ID FOREIGN KEY (`client_category_id`) REFERENCES `client_category` (`client_category_id`),
         CONSTRAINT  ' . $dbname . '_PROBE_HUNTER_ACCOUNT_ID FOREIGN KEY (`probe_processed_hunter_id`) REFERENCES `user_db`.`accounts` (`account_id`),
         CONSTRAINT ' . $dbname . '_PROBE_STATUS FOREIGN KEY (`probe_status_id`) REFERENCES `probe_status` (`probe_status_id`),
         CONSTRAINT ' . $dbname . '_PROBE_PROJECT_TICKET_ID FOREIGN KEY (`probe_ticket_id`) REFERENCES `project_tickets` (`project_ticket_system_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `probe_queue` (
        `probe_queue_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `probe_key_id` int(11) NOT NULL,
        `account_id` int(11) DEFAULT NULL,
        `probe_being_handled` tinyint(1) NOT NULL DEFAULT 0,
        `assign_datetime` DATETIME NULL DEFAULT NULL,
         CONSTRAINT ' . $dbname . '_ACCOUNT_ID FOREIGN KEY (`account_id`) REFERENCES `user_db`.`accounts` (`account_id`),
         CONSTRAINT ' . $dbname . '_PROBE_KEY_ID FOREIGN KEY (`probe_key_id`) REFERENCES `probe` (`probe_key_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `products` (
        `product_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `product_name` varchar(255) NOT NULL,
        `product_previous` varchar(255) DEFAULT NULL,
        `product_qa_previous` varchar(255) DEFAULT NULL,
        `product_type` enum(\'brand\',\'sku\',\'dvc\', \'facing\') NOT NULL,
        `product_status` int(11) DEFAULT NULL,
        `product_alt_design_name` varchar(255) DEFAULT NULL,
        `product_alt_design_previous` varchar(255) DEFAULT NULL,
        `product_alt_design_qa_previous` varchar(255) DEFAULT NULL,
        `product_comment` varchar(255) DEFAULT NULL,
        `product_facing_count` int(11) NOT NULL DEFAULT 0,
        `product_creation_time` datetime NOT NULL DEFAULT current_timestamp(),
        `account_id` int(11) NOT NULL,
        `product_qa_account_id` int(11) DEFAULT NULL,
        `qa_start_datetime` DATETIME NULL DEFAULT NULL,
        `product_qa_datetime` datetime DEFAULT NULL,
        `product_oda_account_id` int(11) DEFAULT NULL,
        `oda_start_datetime` DATETIME NULL DEFAULT NULL,
        `product_oda_datetime` datetime DEFAULT NULL,
        `product_oda_comment` varchar(500) DEFAULT NULL,
        `product_qa_status` enum(\'pending\',\'approved\',\'disapproved\', \'active\', \'rejected\') NOT NULL DEFAULT \'pending\',
        `manufacturer_link` varchar(2083) DEFAULT NULL,
        `product_link` varchar(2083) DEFAULT NULL,
        `product_hunt_type` enum(\'probe\',\'radar\',\'reference\') NOT NULL DEFAULT \'probe\',
        `product_submit_status` enum(\'resubmit\',\'normal\') NOT NULL DEFAULT \'normal\',
         CONSTRAINT ' . $dbname . '_PRODUCT_ACCOUNT_ID FOREIGN KEY (`account_id`) REFERENCES `user_db`.`accounts` (`account_id`),
         CONSTRAINT ' . $dbname . '_PRODUCT_QA_ACCOUNT_ID FOREIGN KEY (`product_qa_account_id`) REFERENCES `user_db`.`accounts` (`account_id`),
         CONSTRAINT ' . $dbname . '_PRODUCT_ODA_ACCOUNT_ID FOREIGN KEY (`product_oda_account_id`) REFERENCES `user_db`.`accounts` (`account_id`),
         CONSTRAINT ' . $dbname . '_PRODUCT_STATUS FOREIGN KEY (`product_status`) REFERENCES `probe_status` (`probe_status_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `probe_product_info` (
        `probe_product_info_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `probe_product_info_key_id` int(11) NOT NULL,
        `probe_product_info_product_id` int(11) NOT NULL,
        `probe_product_info_creation_time` datetime NOT NULL DEFAULT current_timestamp(),
        `probe_product_info_account_id` int(11) NOT NULL,
         CONSTRAINT ' . $dbname . '_probe_product_info_account_id FOREIGN KEY (`probe_product_info_account_id`) REFERENCES `user_db`.`accounts` (`account_id`),
         CONSTRAINT ' . $dbname . '_probe_product_info_key_id FOREIGN KEY (`probe_product_info_key_id`) REFERENCES `probe` (`probe_key_id`),
         CONSTRAINT ' . $dbname . '_probe_product_info_product_id FOREIGN KEY (`probe_product_info_product_id`) REFERENCES `products` (`product_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `probe_qa_queue` (
        `probe_qa_queue_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `product_id` int(11) NOT NULL,
        `account_id` int(11) DEFAULT NULL,
        `probe_being_handled` tinyint(1) NOT NULL DEFAULT 0,
        `assign_datetime` DATETIME NULL DEFAULT NULL,
        CONSTRAINT ' . $dbname . '_PROBE_QA_PRODUCT_ID FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();


    $sql = 'CREATE TABLE `product_weblinks` (
        `product_weblink_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `product_id` int(11) NOT NULL,
        `weblink` varchar(2500) NOT NULL,
        CONSTRAINT ' . $dbname . '_WEBLINK_PRODUCT_ID FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();


    $sql = 'CREATE TABLE `unmatch_reasons` (
        `unmatch_reason_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `unmatch_reason` varchar(255) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = "INSERT INTO `unmatch_reasons` (`unmatch_reason`) VALUES
                (\"Brand not in this category ref. file\"),
                (\"Sub brand doesn't match\"),
                (\"Count doesn't match\"),
                (\"Size doesn't match\"),
                (\"Container type doesn't match\"),
                (\"Flavour/Scent doesn't match\"),
                (\"Description doesn't match\"),
                (\"Sub category doesn't match\"),
                (\"Life stage doesn't match\"),
                (\"Inch size doesn't match\"),
                (\"Form doesn't match\"),
                (\"Breed size doesn't match\"),
                (\"Coffee machines not available in the file for this brand\"),
                (\"Detected as Duplicated Product\"),
                (\"Detected as DVC\"),
                (\"DVC duplicated with existing DVC\"),
                (\"Detected as Substitute\"),
                (\"Existing EAN/Item code suitable for this product\")";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `product_ean_queue` (
        `product_ean_queue_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `product_id` int(11) NOT NULL,
        `account_id` int(11) DEFAULT NULL,
        `product_being_handled` tinyint(1) NOT NULL DEFAULT 0,
        `assign_datetime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT ' . $dbname . '_ODA_EAN_QUEUE_PRODUCT_ID FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `product_ean` (
        `product_ean_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `product_id` int(11) NOT NULL,
        `product_ean` varchar(20) DEFAULT NULL,
        `product_item_code` VARCHAR(24) DEFAULT NULL,
        `additional_comment` varchar(500) DEFAULT NULL,
        `account_id` int(11) default null,
        `ean_assign_datetime` DATETIME NULL DEFAULT NULL,
        `ean_creation_time` datetime NOT NULL DEFAULT current_timestamp(),
        `ean_last_mod_datetime` DATETIME NULL DEFAULT NULL,
        `ean_last_mod_account_id` int(11) default null,
        `unmatch_reason_id` int(11) DEFAULT  NULL,
        `duplicate_product_name` varchar(500) DEFAULT NULL,
        `matched_method` varchar(500) DEFAULT  NULL,
        `chain_product_id` int(11) DEFAULT NULL,
        CONSTRAINT ' . $dbname . '_ODA_EAN_PRODUCT_ID FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
        CONSTRAINT ' . $dbname . '_ODA_EAN_UNMATCH_REASON_ID FOREIGN KEY (`unmatch_reason_id`) REFERENCES `unmatch_reasons` (`unmatch_reason_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();




    $sql = 'CREATE TABLE `project_errors` (
        `project_error_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `project_error_name` varchar(255) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = "INSERT INTO `project_errors` (`project_error_id`, `project_error_name`) VALUES
    (1, 'Poor Image Quality'),
    (2, 'Duplicate SKU'),
    (3, 'Incorrect Image Added'),
    (4, 'Facing Added As SKU'),
    (5, 'DVC Product Added as SKU'),
    (6, 'Wrong Facing Added'),
    (7, 'SKU Not In PNB'),
    (8, 'Duplicate DVC'),
    (9, 'Incorrect DVC Added'),
    (10, 'SKU Product Added as DVC'),
    (11, 'Brand Image Empty'),
    (12, 'Incorrect Brand Added'),
    (13, 'Incorrect Manufacturer'),
    (14, 'Duplicate Brand')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `product_qa_errors` (
        `product_qa_error_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `product_id` int(11) NOT NULL,
        `error_id` int(11) NOT NULL,
        CONSTRAINT ' . $dbname . '_QA_ERROR_ID FOREIGN KEY (`error_id`) REFERENCES `project_errors` (`project_error_id`),
        CONSTRAINT ' . $dbname . '_QA_ERROR_PR_ID FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `project_error_images` (
      `project_error_image_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
      `product_id` int(11) NOT NULL,
      `project_error_image_location` varchar(255) NOT NULL,
       CONSTRAINT ' . $dbname . '_ERROR_IMAGE_PRODUCT_ID FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `reference_info` (
      `reference_info_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
      `reference_recognition_level` varchar(255) DEFAULT NULL,
      `reference_ean` varchar(255) DEFAULT NULL,
      `reference_short_name` varchar(255) DEFAULT NULL,
      `reference_category` varchar(255) DEFAULT NULL,
      `reference_sub_category` varchar(255) DEFAULT NULL,
      `reference_brand` varchar(255) DEFAULT NULL,
      `reference_sub_brand` varchar(255) DEFAULT NULL,
      `reference_manufacturer` varchar(255) DEFAULT NULL,
      `reference_base_size` varchar(255) DEFAULT NULL,
      `reference_size` varchar(255) DEFAULT NULL,
      `reference_measurement_unit` varchar(255) DEFAULT NULL,
      `reference_container_type` varchar(255) DEFAULT NULL,
      `reference_agg_level` varchar(255) DEFAULT NULL,
      `reference_segment` varchar(255) DEFAULT NULL,
      `reference_count_upc2` varchar(255) DEFAULT NULL,
      `reference_flavor_detail` varchar(255) DEFAULT NULL,
      `reference_case_pack` varchar(255) DEFAULT NULL,
      `reference_multi_pack` varchar(255) DEFAULT NULL,
      `reference_added_date` datetime NOT NULL DEFAULT current_timestamp(),
      `reference_added_user_id` int(11) NOT NULL,
        `ref_start_datetime` DATETIME NULL DEFAULT NULL,
      `reference_hunter_processed_time` datetime DEFAULT NULL,
      `reference_process_comment` varchar(255) DEFAULT NULL,
      `reference_process_remark` varchar(255) DEFAULT NULL,
      `reference_processed_hunter_id` int(11) DEFAULT NULL,
      `reference_status_id` int(11) DEFAULT NULL,
      `reference_ticket_id` int(11) DEFAULT NULL,
       CONSTRAINT  ' . $dbname . '_REF_ADDED_USER_ID FOREIGN KEY (`reference_added_user_id`) REFERENCES `user_db`.`accounts` (`account_id`),
       CONSTRAINT  ' . $dbname . '_REF_PROCESSED_HUNTER_ID FOREIGN KEY (`reference_processed_hunter_id`) REFERENCES `user_db`.`accounts` (`account_id`),
       CONSTRAINT  ' . $dbname . '_REF_STATUS_ID FOREIGN KEY (`reference_status_id`) REFERENCES `probe_status` (`probe_status_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `reference_queue` (
      `reference_queue_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
      `reference_info_key_id` int(11) NOT NULL,
      `account_id` int(11) DEFAULT NULL,
      `reference_being_handled` tinyint(1) NOT NULL DEFAULT 0,
      `assign_datetime` DATETIME NULL DEFAULT NULL,
       CONSTRAINT ' . $dbname . '_REFERENCE_QUEUE_ACCOUNT_ID FOREIGN KEY (`account_id`) REFERENCES `user_db`.`accounts` (`account_id`),
       CONSTRAINT ' . $dbname . '_REFERENCE_QUEUE_REFERENCE_INFO_KEY_ID FOREIGN KEY (`reference_info_key_id`) REFERENCES `reference_info` (`reference_info_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `radar_hunt` (
  `radar_hunt_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `radar_category` varchar(255) NOT NULL,
  `radar_brand` varchar(255) NOT NULL,
  `radar_added_user_id` int(11) NOT NULL,
  `radar_added_date` datetime NOT NULL DEFAULT current_timestamp(),
   `radar_ticket_id` int(11) NOT NULL,
   `radar_hunter_id` int(11) DEFAULT NULL,
   `radar_start_datetime` DATETIME NULL DEFAULT NULL,
  `radar_processed_time` datetime DEFAULT NULL,
   CONSTRAINT ' . $dbname . '_RADAR_ACCOUNT_ID FOREIGN KEY (`radar_added_user_id`) REFERENCES `user_db`.`accounts` (`account_id`),
   CONSTRAINT ' . $dbname . '_RADAR_TICKET_ID FOREIGN KEY (`radar_ticket_id`) REFERENCES `project_tickets` (`project_ticket_system_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `radar_queue` (
  `radar_queue_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `radar_hunt_key_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `radar_being_handled` tinyint(1) NOT NULL DEFAULT 0,
  `assign_datetime` DATETIME NULL DEFAULT NULL,
   CONSTRAINT ' . $dbname . '_RADAR_QUEUE_ACCOUNT_ID FOREIGN KEY (`account_id`) REFERENCES `user_db`.`accounts` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `radar_sources` (
  `radar_source_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `radar_hunt_id` int(11) NOT NULL,
  `radar_status_id` int(11) NOT NULL,
  `radar_source_link` varchar(2083) NOT NULL,
  `radar_comment` varchar(255) DEFAULT NULL,
  `radar_product_id` int(11) DEFAULT NULL,
  `account_id` int(11) NOT NULL,
  `creation_time` datetime NOT NULL DEFAULT current_timestamp(),
 CONSTRAINT ' . $dbname . '_RADAR_HUNT_ID FOREIGN KEY (`radar_hunt_id`) REFERENCES `radar_hunt` (`radar_hunt_id`),
 CONSTRAINT ' . $dbname . '_RADAR_PRODUCT_ID FOREIGN KEY (`radar_product_id`) REFERENCES `products` (`product_id`),
 CONSTRAINT ' . $dbname . '_RADAR_STATUS FOREIGN KEY (`radar_status_id`) REFERENCES `probe_status` (`probe_status_id`),
 CONSTRAINT ' . $dbname . '_RADAR_ACCOUNT FOREIGN KEY (`account_id`) REFERENCES `user_db`.`accounts` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `ref_product_info` (
  `ref_product_info_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `reference_info_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  CONSTRAINT ' . $dbname . '_REFERENCE_INFO_PRODUCT_ID FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  CONSTRAINT ' . $dbname . '_REFERENCE_INFO_REF_ID FOREIGN KEY (`reference_info_id`) REFERENCES `reference_info` (`reference_info_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `product_client_category` (
    `product_client_category_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `product_id` int(11) NOT NULL,
    `client_category_id` int(11) NOT NULL,
    CONSTRAINT ' . $dbname . '_PRODCUT_CLIENT_CAT_CLIENT_CAT_ID FOREIGN KEY (`client_category_id`) REFERENCES `client_category` (`client_category_id`),
    CONSTRAINT ' . $dbname . '_PRODCUT_CLIENT_CAT_PRODUCT_ID FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `oda_queue` (
  `oda_queue_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `qa_being_handled` tinyint(1) NOT NULL DEFAULT \'0\'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $sql = 'CREATE TABLE `product_oda_errors` (
  `product_oda_error_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `error_id` int(11) NOT NULL,
   CONSTRAINT ' . $dbname . '_ODA_ERROR_ID FOREIGN KEY (`error_id`) REFERENCES `project_errors` (`project_error_id`),
   CONSTRAINT ' . $dbname . '_ODA_ERROR_PR_ID FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    echo "<span class=\"success-popup\">Project Created</span>";
} else {
    echo "<span class=\"error-popup\">Project with that name already exists</span>    ";
}

?>