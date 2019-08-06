<?php
/*
    Filename: add_new_user.php
    Author: Malika Liyanage
    Created: 17/07/2019
    Purpose: Form for adding new users
*/

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['logged_in'])) {
	header('Location: login_auth_one.php');
	exit();
}else{
    if(!($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Supervisor')){
        header('Location: index.php');
	    exit();
    }
}

// Current settings to connect to the user account database
require('user_db_connection.php');

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='icon' href='favicon.ico' type='image/x-icon' />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="scripts/user_registration_validate.js"></script>
    <script src="scripts/transition.js"></script>
    <link rel="stylesheet" type="text/css" href="styles/main.css" />
    <link rel="stylesheet" type="text/css" href="styles/new_user.css" />
    <title>Add New User</title>
</head>
<body class="register-page">
<svg id="fader"></svg>
<nav class="navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="index.php" class="navbar-brand">Data Operations</a>
        </div>
    </div>
</nav>
<form action="process_registration.php" method="POST" id="register-form">
    <div class="tab">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="first_name">*First Name</label>
                <input type="text" class="form-control" placeholder="First Name" id="first_name" name="first_name">
                <span id="first_name_error" class="error-popup"></span>    
            </div>
            <div class="form-group col-md-6">
                <label for="last_name">*Last Name</label>
                <input type="text" class="form-control" placeholder="Last Name" id="last_name" name="last_name">
                <span id="last_name_error" class="error-popup"></span>    
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="nic">*Enter NIC Number</label>
                <input type="text" class="form-control" placeholder="NIC" id="nic" name="nic">
                <span id="nic_error" class="error-popup"></span>    
            </div>
        </div>
        <!--Stuff not linked to db -->
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="contact_number">*Contact Number</label>
                <input type="text" class="form-control" placeholder="Contact Number" id="contact_number" name="contact_number">
                <span id="contact_number_error" class="error-popup"></span>    
            </div>
            <div class="form-group col-md-6">
                <label for="home_contact_number">Home Contact Number</label>
                <input type="text" class="form-control" placeholder="Home Contact Number" id="home_contact_number" name="home_contact_number">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="home_address">*Home Address</label>
                <input type="text" class="form-control" placeholder="Home Address" id="home_address" name="home_address">
                <span id="home_address_error" class="error-popup"></span>    
            </div>
            <div class="form-group col-md-6">
                <label for="present_address">Present Address</label>
                <input type="text" class="form-control" placeholder="Present Address" id="present_address" name="present_address">
                <span id="present_address_error" class="error-popup"></span>    
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="dob">*Date of Birth</label>
                <input type="date" class="form-control" id="dob" placeholder="Date of Birth" name="dob">
                <span id="dob_error" class="error-popup"></span>    
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <p>Transport Method</p>
                <div class="radio">
                  <label><input type="radio" name="account_transport_method" value="own" checked>Own</label>
                </div>
                <div class="radio">
                  <label><input type="radio" name="account_transport_method" value="office">Office Transport</label>
                </div>        
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="designation">*Designation:</label>
                <select name="designation" id="designation" class="form-control">
                    <option value=""selected disabled>Select</option>
<?php
$sql = 'SELECT designation_id, designation_name FROM designations WHERE designation_id != 1';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$designation_rows = $stmt->fetchAll(PDO::FETCH_OBJ);

foreach($designation_rows as $designation_row){
    echo "<option value=\"$designation_row->designation_id\">$designation_row->designation_name</option>";
}
?>                    
                </select> 
                <span id="designation_error" class="error-popup"></span>    
            </div>
        </div>
    </div>
    <div class="tab">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="bank_name">Bank Name</label>
                <input type="text" class="form-control" placeholder="Bank Name" id="bank_name" name="bank_name">   
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="bank_branch">Bank Branch</label>
                <input type="text" class="form-control" placeholder="Bank Branch" id="bank_branch" name="bank_branch">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="bank_account_number">Bank Account Number</label>
                <input type="text" class="form-control" placeholder="Bank Account Number" id="bank_account_number" name="bank_account_number">  
            </div>
        </div>
    </div>
    <!---->
    <div class="tab">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="gid">*Enter GID</label>
                <input type="text" class="form-control" id="gid" placeholder="GID" name="gid">
                <span id="gid_error" class="error-popup"></span>    
            </div>
        </div>
        <div class="form-group">
            <label for="personal_email">Personal Email</label>
            <input type="email" class="form-control" id="personal_email" placeholder="Personal Email" name="personal_email">
            <span id="personal_email_error" class="error-popup"></span>    
        </div>
        <div class="form-group">
            <label for="username">*Enter work email address (this will be used as your username)</label>
            <input type="email" class="form-control" id="username" placeholder="Work Email" name="username">
            <span id="username_error" class="error-popup"></span>    
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="pwd">Enter Password</label>
                <input type="password" class="form-control" id="pwd" placeholder="Password" name="pwd">
                <span id="password_error" class="error-popup"></span>
            </div>
            <div class="form-group col-md-4">
                <label for="confirm_pwd">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_pwd" placeholder="Confirm Password" name="confirm_pwd">
                <span id="confirm_password_error" class="error-popup"></span>
            </div>
        </div>
        <button type="submit" class="btn btn-success"">Submit</button>
        <a href="index.php">Back to Dashboard</a>
        </div>
    </div>
    <div style="overflow:auto;">
        <div style="float:right;">
        <button type="button" id="prevBtn"  class="btn btn-danger" onclick="nextPrev(-1)">Previous</button>
        <button type="button" id="nextBtn"  class="btn btn-primary" onclick="nextPrev(1)">Next</button>
        </div>
    </div>
    <!-- Circles which indicates the steps of the form: -->
    <div style="text-align:center;margin-top:40px;">
      <span class="step"></span>
      <span class="step"></span>
      <span class="step"></span>
    </div>
<script>
var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the current tab

function showTab(n) {
  // This function will display the specified tab of the form ...
  var x = document.getElementsByClassName("tab");
  x[n].style.display = "block";
  // ... and fix the Previous/Next buttons:
  if (n == 0) {
    document.getElementById("prevBtn").style.display = "none";
  } else {
    document.getElementById("prevBtn").style.display = "inline";
  }
  if (n == (x.length - 1)) {
    document.getElementById("nextBtn").style.display = "none";
  } else {
    document.getElementById("nextBtn").style.display = "inline";
    document.getElementById("nextBtn").innerHTML = "Next";
  }
  // ... and run a function that displays the correct step indicator:
  fixStepIndicator(n)
}

function nextPrev(n) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("tab");
  // Exit the function if any field in the current tab is invalid:
  // Hide the current tab:
  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  // Otherwise, display the correct tab:
  showTab(currentTab);
}


function fixStepIndicator(n) {
  // This function removes the "active" class of all steps...
  var i, x = document.getElementsByClassName("step");
  for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
  }
  //... and adds the "active" class to the current step:
  x[n].className += " active";
}
</script>
</form>
</body>
</html>