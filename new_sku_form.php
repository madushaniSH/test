<?php
/*
    Filename: new_sku_form.php
    Author: Malika Liyanage
    Created: 23/07/2019
    Purpose: Used for entering a new sku to the system
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

$sql = 'SELECT project_trax_category_name FROM project_trax_category';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$trax_catergorys = $stmt->fetchAll(PDO::FETCH_OBJ);

$sql = 'SELECT product_container_type_id, product_container_type_name FROM product_container_type';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$container_types = $stmt->fetchAll(PDO::FETCH_OBJ);

$sql = 'SELECT product_measurement_unit_name FROM product_measurement_unit';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$measurement_units = $stmt->fetchAll(PDO::FETCH_OBJ);

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
      <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>
    <script src="scripts/transition.js"></script>
    <link rel="stylesheet" type="text/css" href="styles/main.css" />
    <link rel="stylesheet" type="text/css" href="styles/sku_form.css" />
    <script src="scripts/sku_form_image_preview.js"></script>
    <script src="scripts/sku_form_tab_open.js"></script>
    <script src="scripts/process_forms.js"></script>
    <script src="scripts/sku_form_dropdowns.js"></script>
    <script src="scripts/sku_form_validate.js"></script>
    <title>Data Operations Department</title>
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
    <form id="sku_form" enctype="multipart/form-data" method="POST"> 
        <div class="top-section form-group jumbotron">
            <button class="btn btn-primary btn-light" type="button"
                onclick="window.location.href='products.php'">Cancel</button>
            <button class="btn btn-primary btn-success" id="submit_sku_form" disabled onclick="validate_form();">Save</button>
        </div>
        <div class="form-row">
            <div class="form-section col-md-6">
                <div class="form-row">
                    <div class="col">
                        <p class="border-bottom my-3">Faces</p>
                        <div class="form-row">
                            <div class="upload-section">
                                <p>Front</p>
                                <div class="image-upload">
                                    <label for="file-input-front">
                                        <img id="preview-front" class="text-center"
                                            src="images\default\system\product\default.jpg" alt="your image" />
                                    </label>
                                    <input type='file' id="file-input-front" onchange="readURL(this);" />
                                </div>
                                <a id="clear-front" class="hide" onclick="clearURL(this);">Clear</a>
                                <span id="front_image_error" class="error-popup-image warning"></span>
                            </div>
                            <div class="upload-section">
                                <p>Top</p>
                                <div class="image-upload">
                                    <label for="file-input-top">
                                        <img id="preview-top" src="images\default\system\product\default.jpg"
                                            alt="your image" />
                                    </label>
                                    <input type='file' id="file-input-top" onchange="readURL(this);" />
                                </div>
                                <a id="clear-top" class="hide" onclick="clearURL(this);">Clear</a>
                                <span id="top_image_error" class="error-popup-image warning"></span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="upload-section">
                                <p>Back</p>
                                <div class="image-upload">
                                    <label for="file-input-back">
                                        <img id="preview-back" src="images\default\system\product\default.jpg"
                                            alt="your image" />
                                    </label>
                                    <input type='file' id="file-input-back" onchange="readURL(this);" />
                                </div>
                                <a id="clear-back" class="hide" onclick="clearURL(this);">Clear</a>
                                <span id="back_image_error" class="error-popup-image warning"></span>
                            </div>
                            <div class="upload-section">
                                <p>Bottom</p>
                                <div class="image-upload">
                                    <label for="file-input-bottom">
                                        <img id="preview-bottom" src="images\default\system\product\default.jpg"
                                            alt="your image" />
                                    </label>
                                    <input type='file' id="file-input-bottom" onchange="readURL(this);" />
                                </div>
                                <a id="clear-bottom" class="hide" onclick="clearURL(this);">Clear</a>
                                <span id="bottom_image_error" class="error-popup-image warning"></span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="upload-section">
                                <p>Side1</p>
                                <div class="image-upload">
                                    <label for="file-input-side1">
                                        <img id="preview-side1" src="images\default\system\product\default.jpg"
                                            alt="your image" />
                                    </label>
                                    <input type='file' id="file-input-side1" onchange="readURL(this);" />
                                </div>
                                <a id="clear-side1" class="hide" onclick="clearURL(this);">Clear</a>
                                <span id="side1_image_error" class="error-popup-image warning"></span>
                            </div>
                            <div class="upload-section">
                                <p>Side2</p>
                                <div class="image-upload">
                                    <label for="file-input-side2">
                                        <img id="preview-side2" src="images\default\system\product\default.jpg"
                                            alt="your image" />
                                    </label>
                                    <input type='file' id="file-input-side2" onchange="readURL(this);" />
                                </div>
                                <a id="clear-side2" class="hide" onclick="clearURL(this);">Clear</a>
                                <span id="side2_image_error" class="error-popup-image warning"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <p class="border-bottom my-3">Product Propeties</p>
                        <div class="form-group">
                            <label for="name">*Name:</label>
                            <input type="text" id="name" name="name" class="form-control">
                            <span id="name_error" class="error-popup"></span>
                            <div id="dupliacte_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="item_code">Item Code:</label>
                            <input type="text" id="item_code" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="product_type">*Product Type:</label>
                            <select name="product_type" id="product_type" class="form-control"
                                onChange="check_option();">
                                <option value=""></option>
                                <option value="empty">Empty</option>
                                <option value="irrelevant">Irrelevant</option>
                                <option value="other">Other</option>
                                <option value="pos">POS</option>
                                <option value="sku">SKU</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="trax_category">Trax Category:</label>
                            <select name="trax_category" id="trax_category" class="form-control">
                                <option value="" selected disabled>Select</option>
<?php
foreach($trax_catergorys as $trax_catergory){
    echo "<option>$trax_catergory->project_trax_category_name</option>";
}
?>
                            </select>
                        </div>
                        <div class="form-group">
                            <div>
                                <label for="brand">*Brand:</label>
                                <button type="button" class="btn btn-outline-success btn-sm btn-sku-form" data-toggle="modal" href="#suggest_brand" onclick="get_manufacturer_list();">Add New</button>
                                <?php require('brand_modal.php'); ?>
                                <?php require('manufacturer_modal.php'); ?>
                            </div>                         
                            <select name="brand" id="brand" class="form-control brand-list">
                            </select>
                            <span id="brand_error" class="error-popup"></span>
                        </div>
                        <div class="form-group">
                            <label for="global_code">Global Code:</label>
                            <input type="text" id="global_code" class="form-control">
                        </div>
                        <div id="sku_only">
                            <p class="border-top my-3">Physical Attributes</p>
                            <div class="form-group">
                                <label for="container_type">*Container Type:</label>
                                <select name="container_type" id="container_type" class="form-control">
                                    <option value="" selected disabled>Select</option>
<?php
foreach($container_types as $container_type){
    echo "<option value=\"$container_type->product_container_type_id\">$container_type->product_container_type_name</option>";
}
?>
                                </select>
                                <span id="container_type_error" class="error-popup"></span>
                            </div>
                            <div class="form-group">
                                <label for="size">Size(#):</label>
                                <input type="text" id="size" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="size">Sub-packages(#):</label>
                                <input type="text" id="size" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="measurement_unit">Measurement Unit:</label>
                                <select name="measurement_unit" id="measurement_unit"class="form-control">
                                    <option value="" selected disabled>Select</option>
<?php
foreach($measurement_units as $measurement_unit){
    echo "<option>$measurement_unit->product_measurement_unit_name</option>";
}
?>                                    
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="size">Units(#):</label>
                                <input type="text" id="size" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="size">Height(mm):</label>
                                <input type="text" id="size" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="size">Width(mm):</label>
                                <input type="text" id="size" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="size">Depth(mm):</label>
                                <input type="text" id="size" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-section col-md-5">
                <div class="tab border-bottom my-3">
                    <button class="tablinks default_open"
                        onclick="return open_form_tab(event, 'reporting')">Reporting</button>
                    <button id="alt_design_link" class="tablinks"
                        onclick="return open_form_tab(event, 'alternative_design')">Alternative Design</button>
                    <button class="tablinks" onclick="return open_form_tab(event, 'palette')">Palette</button>
                </div>
                <div id="reporting" class="tabcontent">
                    <div class="tab border-bottom my-3 sub-tab">
                        <button class="tablinks sub_tablinks default_open"
                            onclick="return open_form_sub_tab(event, 'client_properties')">Client Properties</button>
                        <button class="tablinks sub_tablinks"
                            onclick="return open_form_sub_tab(event, 'substitution')">Substitution</button>
                    </div>
                    <div id="client_properties" class="tabcontent sub_tabcontent">
                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="short_name">Short Name:</label>
                                    <input type="text" id="short_name" class="form-control">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="local_name">Local Name:</label>
                                    <input type="text" id="local_name" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="ean">EAN:</label>
                                    <input type="text" id="ean" class="form-control">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <div>
                                        <label for="client_catergory">*Client Category:</label>
                                        <button type="button" class="btn btn-outline-success btn-sm btn-outline-tab" data-toggle="modal" href="#suggest_client_category">Add New</button>
                                    </div>
                                    <?php require('client_category_modal.php'); ?>
                                    <select name="client_category" id="client_category" class="form-control client-category-list">
                                    </select>
                                    <span id="client_category_error" class="error-popup"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <div>
                                        <label for="client_sub_catergory">Client Subcategory:</label>
                                        <button type="button" class="btn btn-outline-success btn-sm btn-outline-tab-one" data-toggle="modal" href="#suggest_client_sub_category">Add New</button>
                                    </div>
                                    <?php require('client_sub_category_modal.php'); ?>
                                    <select name="client_sub_category" id="client_sub_category" class="form-control client-sub-category-list">                                       
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <p>Additional Attributes</p>
                                <button type="button" class="btn btn-outline-dark" data-toggle="modal" href="#edit_attribute" onclick="get_attribute_list();">EDIT ATTRIBUTES</button>
                                <button type="button" class="btn btn-outline-danger" onclick="clear_attribute_list();" id="clear_attribute" style="display: none;">CLEAR ALL ATTRIBUTES</button>
                                <?php require('edit_attribute_modal.php'); ?>
                            </div>
                        </div>
                        <div class="form-row" id="new_attribute_entry">
                        </div>
                        
                    </div>
                    <div id="substitution" class="tabcontent sub_tabcontent">
                        <div class="form-row">
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="product_sub">This Product Substitutes:</label>
                                    <select name="product_sub" id="product_sub" class="form-control">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="alternative_design" class="tabcontent">
                    <button type="button" class="btn btn-primary" data-toggle="modal"
                        data-target="#alt_design_modal">Add Design</button>
                    <div class="modal fade" id="alt_design_modal" tabindex="-1" role="dialog"
                        aria-labelledby="alt_design_modal" aria_hidden-"true">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="alt_design_modal_title">New Alternative Design</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-row">
                                        <div class="col col-md-2">
                                            <div class="upload-section">
                                                <p>Front</p>
                                                <div class="image-upload alt-design-image-upload">
                                                    <label for="file-input-front-alt">
                                                        <img id="preview-front-alt" class="text-center"
                                                            src="images\default\system\product\default.jpg"
                                                            alt="your image" />
                                                    </label>
                                                    <input type='file' id="file-input-front-alt"
                                                        onchange="readURL(this);" />
                                                </div>
                                                <a id="clear-front-alt" class="hide" onclick="clearURL(this);">Clear</a>
                                            </div>
                                            <div class="upload-section">
                                                <p>Bottom</p>
                                                <div class="image-upload alt-design-image-upload">
                                                    <label for="file-input-bottom-alt">
                                                        <img id="preview-bottom-alt" class="text-center"
                                                            src="images\default\system\product\default.jpg"
                                                            alt="your image" />
                                                    </label>
                                                    <input type='file' id="file-input-bottom-alt"
                                                        onchange="readURL(this);" />
                                                </div>
                                                <a id="clear-bottom-alt" class="hide"
                                                    onclick="clearURL(this);">Clear</a>
                                            </div>
                                        </div>
                                        <div class="col col-md-2">
                                            <div class="upload-section">
                                                <p>Top</p>
                                                <div class="image-upload alt-design-image-upload">
                                                    <label for="file-input-top-alt">
                                                        <img id="preview-top-alt" class="text-center"
                                                            src="images\default\system\product\default.jpg"
                                                            alt="your image" />
                                                    </label>
                                                    <input type='file' id="file-input-top-alt"
                                                        onchange="readURL(this);" />
                                                </div>
                                                <a id="clear-top-alt" class="hide" onclick="clearURL(this);">Clear</a>
                                            </div>
                                            <div class="upload-section">
                                                <p>Side1</p>
                                                <div class="image-upload alt-design-image-upload">
                                                    <label for="file-input-side1-alt">
                                                        <img id="preview-side1-alt" class="text-center"
                                                            src="images\default\system\product\default.jpg"
                                                            alt="your image" />
                                                    </label>
                                                    <input type='file' id="file-input-side1-alt"
                                                        onchange="readURL(this);" />
                                                </div>
                                                <a id="clear-side1-alt" class="hide" onclick="clearURL(this);">Clear</a>
                                            </div>
                                        </div>
                                        <div class="col col-md-2">
                                            <div class="upload-section">
                                                <p>Back</p>
                                                <div class="image-upload alt-design-image-upload">
                                                    <label for="file-input-back-alt">
                                                        <img id="preview-back-alt" class="text-center"
                                                            src="images\default\system\product\default.jpg"
                                                            alt="your image" />
                                                    </label>
                                                    <input type='file' id="file-input-back-alt"
                                                        onchange="readURL(this);" />
                                                </div>
                                                <a id="clear-back-alt" class="hide" onclick="clearURL(this);">Clear</a>
                                            </div>
                                            <div class="upload-section">
                                                <p>Side2</p>
                                                <div class="image-upload alt-design-image-upload">
                                                    <label for="file-input-side2-alt">
                                                        <img id="preview-side2-alt" class="text-center"
                                                            src="images\default\system\product\default.jpg"
                                                            alt="your image" />
                                                    </label>
                                                    <input type='file' id="file-input-side2-alt"
                                                        onchange="readURL(this);" />
                                                </div>
                                                <a id="clear-side2-alt" class="hide" onclick="clearURL(this);">Clear</a>
                                            </div>
                                        </div>
                                        <div class="col col-md-4" id="alt-text-section">
                                            <div class="form-group">
                                                <label for="alt_design_name">*Alternative Design Name:</label>
                                                <input type="text" id="alt_design_name" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="alt_start_date">*Start Date:</label>
                                                <input type="date" id="alt_start_date" class="form-control" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="alt_end_date">End By:</label>
                                                <input type="date" id="alt_end_date" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-success">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="palette" class="tabcontent">
                    <p>Palette Properties</p>
                    <div class="form-row">
                        <div class="col col-md-6">
                            <div class="form-group">
                                <label for="smart_level_one">SMART Level 1:</label>
                                <input type="text" name="smart_level_one" id="smart_level_one" class="form-control" disabled>
                                <span id="smart_level_one_error" class="error-popup"></span>
                            </div>
                        </div>
                        <div class="col col-md-6">
                            <div class="form-group">
                                <label for="smart_level_two">SMART Level 2:</label>
                                <input type="text" name="smart_level_two" id="smart_level_two" class="form-control" disabled>
                                <span id="smart_level_two_error" class="error-popup"></span>
                            </div>
                        </div>
                        <div class="col col-md-6">
                            <div class="form-group">
                                <label for="smart_caption">SMART Caption:</label>
                                <input type="text" name="smart_caption" id="smart_caption" class="form-control">
                                <span id="smart_caption_error" class="error-popup"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </form>
</body>

</html>