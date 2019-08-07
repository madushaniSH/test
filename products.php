<?php
/*
    Filename: products.php
    Author: Malika Liyanage
    Created: 22/07/2019
    Purpose: Used for displaying the product grid to the user

    Additional Notes:
    - To make the grid the DataTables jQuery Library was used to add the sort and search functionalities
    - Link: https://datatables.net/
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

// SQL to get all the products in the product table
$sql = 'SELECT * FROM product';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
$total_row_count = $stmt->rowCount(PDO::FETCH_OBJ);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='icon' href='favicon.ico' type='image/x-icon' />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <script src="scripts/transition.js"></script>
    <link rel="stylesheet" type="text/css" href="styles/main.css" />
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" />
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script type="text/javascript">
        (function () {
            var css = document.createElement('link');
            css.href = 'https://use.fontawesome.com/releases/v5.1.0/css/all.css';
            css.rel = 'stylesheet';
            css.type = 'text/css';
            document.getElementsByTagName('head')[0].appendChild(css);
        })();
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>
    <script src="scripts/sku_form_image_preview.js"></script>
    <script src="scripts/process_forms.js"></script>
    <script type="text/javascript">
        // releases the hold on the $ identifier
        $.noConflict();
        jQuery(document).ready(function () {
            $('#product_table thead tr').clone(true).appendTo('#product_table thead');
            $('#product_table thead tr:eq(1) th').each(function (i) {
                // ignores the first row which is images which dont need to have a sort option
                if (i != 0) {
                    var title = $(this).text();
                    // add the list option fitering method to the last column Global Status
                    if (i == 6) {
                        $(this).removeClass("this_class");
                        $(this).addClass("search-filter");
                    } else {
                        // creates the input search boxes
                        $(this).html('<input type="text" placeholder="Search ' + title + '" />');

                        // processes the users input on the search boxes
                        $('input', this).on('keyup change', function () {
                            if (table.column(i).search() !== this.value) {
                                table
                                    .column(i)
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    }
                }
            });

            // configures the table options
            var table = $('#product_table').DataTable({
                "bLengthChange": false,
                "paging": false,
                "info": false,
                "order": [],
                orderCellsTop: true,
                // removes the sort option from the elements with the class no-sort
                "columnDefs": [{
                    "targets": 'no-sort',
                    "orderable": false,
                }],
                initComplete: function () {
                    this.api().columns('.this_class').every(function () {
                        var column = this;
                        var select = $('<select><option value=""></option></select>')
                            .appendTo('#product_table thead .search-filter')
                            .on('change', function () {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );

                                column
                                    .search(val ? '^' + val + '$' : '', true, false)
                                    .draw();
                            });

                        column.data().unique().sort().each(function (d, j) {
                            select.append('<option value="' + d + '">' + d +
                                '</option>')
                        });
                    });
                }
            })
        });
    </script>
    <title>Products and Brands</title>
</head>

<body>
    <svg id="fader"></svg>
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <a href="index.php" class="navbar-brand">Data Operations</a>
            </div>
        </div>
    </nav>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="dropdown">
                <i class="fas fa-plus dropdown-toggle" id="dropdown_form_list" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu" aria-labelledby="dropdown_form_list">
                    <a href="new_sku_form.php" class="dropdown-item">Suggest Product</a>
                    <a data-toggle="modal" class="dropdown-item" data-target="#suggest_manufacturer">Suggest
                        Manufacturer</a>
                    <a data-toggle="modal" class="dropdown-item" data-target="#suggest_client_category">Suggest Client
                        Category</a>
                    <a data-toggle="modal" class="dropdown-item" data-target="#suggest_client_sub_category">Suggest
                        Client Sub Category</a>
                    <a data-toggle="modal" class="dropdown-item" data-target="#suggest_brand" onclick="get_manufacturer_list();">Suggest Brand</a>
                </div>
            </div>
        </div>
    </div>
    <?php require('client_category_modal.php'); ?>
    <?php require('client_sub_category_modal.php'); ?>
    <?php require('brand_modal.php'); ?>
    <?php require('manufacturer_modal.php'); ?>
    <div class="tableFixHead">
    <table class="table table-bordered" id="product_table">
        <thead class="thead-dark">
            <tr>
                <th class="no-sort" scope="col">Image</th>
                <th scope="col">Product Name</th>
                <th scope="col">Item Code</th>
                <th scope="col">Client Category Name</th>
                <th scope="col">Manufacturer Name</th>
                <th scope="col">Brand Name</th>
                <th class="this_class" scope="col">Global Status</th>
            </tr>
        </thead>
        <tbody>
<?php
foreach($rows as $row){
    $get_image_sql = 'SELECT product_image.product_image_location FROM product_image INNER JOIN product ON product.product_image_id = product_image.product_image_id WHERE product.product_id = :id';
    $stmt = $pdo->prepare($get_image_sql);
    $stmt->execute(['id'=>$row->product_id]);
    $image_information = $stmt->fetch(PDO::FETCH_OBJ);

    $client_catergory_sql = 'SELECT client_category.client_category_name FROM client_category INNER JOIN product ON product.client_category_id = client_category.client_category_id WHERE product.product_id = :id';
    $stmt = $pdo->prepare($client_catergory_sql);
    $stmt->execute(['id'=>$row->product_id]);
    $client_catergory_information = $stmt->fetch(PDO::FETCH_OBJ);
    
    $brand_sql = 'SELECT brand.brand_id, brand.brand_name FROM brand INNER JOIN product ON product.brand_id = brand.brand_id WHERE product.product_id = :id';
    $stmt = $pdo->prepare($brand_sql);
    $stmt->execute(['id'=>$row->product_id]);
    $brand_information = $stmt->fetch(PDO::FETCH_OBJ);

    $manufacturer_sql = 'SELECT manufacturer.manufacturer_name FROM manufacturer INNER JOIN brand ON brand.manufacturer_id = manufacturer.manufacturer_id WHERE brand.brand_id = :id';
    $stmt = $pdo->prepare($manufacturer_sql);
    $stmt->execute(['id'=>$brand_information->brand_id]);
    $manufacturer_information = $stmt->fetch(PDO::FETCH_OBJ);

    echo "
        <tr>
            <td><a href=\"$image_information->product_image_location\" target=\"_blank\"><img src=\"$image_information->product_image_location\" class=\"grid-image\"></a></td>
            <td>$row->product_name</td>
            <td>$row->product_item_code</td>
            <td>$client_catergory_information->client_category_name</td>
            <td>$brand_information->brand_name</td>
            <td>$manufacturer_information->manufacturer_name</td>
            <td>$row->product_global_status</td>
        </tr>
    ";
}
?>
        </tbody>
    </table>
    </div>
</body>

</html>