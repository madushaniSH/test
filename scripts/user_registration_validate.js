/*
    Filename: user_registration_validate.js
    Author: Malika Liyanage
    Created: 17/07/2019
    Purpose: Script used for validating the registration form on add_new_user.php
*/

/*Used for turning on/off validation for debugging */
var is_debug = false;

// function is used to check if the passed string is empty
function is_empty(str) {
    return !str.replace(/\s+/, '').length;
}

/* Sets the passed element's css display propety to block*/
function show_element(passed_element) {
    passed_element.style.display = "block";
}

/* Sets the passed element's css display propety to none*/
function hide_element(passed_element) {
    passed_element.style.display = "none";
}

/* Function is used to validate the registration form*/
function validate_form() {
    var is_valid_form = true;
    if (!is_debug) {
        var first_name = $.trim(document.getElementById("first_name").value);
        var last_name = $.trim(document.getElementById("last_name").value);
        var gid = $.trim(document.getElementById("gid").value);
        var nic = document.getElementById("nic").value;
        var username = document.getElementById("username").value;
        var password = document.getElementById("pwd").value;
        var confirm_password = document.getElementById("confirm_pwd").value;

        var first_name_error = document.getElementById("first_name_error");
        var first_name_error = document.getElementById("first_name_error");
        var gid_error = document.getElementById("gid_error");
        var nic_error = document.getElementById("nic_error");
        var username_error = document.getElementById("username_error");
        var password_error = document.getElementById("password_error");
        var confirm_password_error = document.getElementById("confirm_password_error");

        /*Performs validation on the first name */
        if (is_empty(first_name)) {
            is_valid_form = false;
            show_element(first_name_error);
            first_name_error.innerHTML = "First Name cannot be empty";
        } else if (first_name.match(/^(?=.{1,50}$)[a-z]+(?:['_.\s][a-z]+)*$/i)) {
            is_valid_form = true;
            hide_element(first_name_error);
        } else {
            is_valid_form = false;
            show_element(first_name_error);
            first_name_error.innerHTML = "Invalid First Name entered";
        }

        /*Performs validation on the last name */
        if (is_empty(last_name)) {
            is_valid_form = false;
            show_element(last_name_error);
            last_name_error.innerHTML = "Last Name cannot be empty";
        } else if (last_name.match(/^(?=.{1,50}$)[a-z]+(?:['_.\s][a-z]+)*$/i)) {
            hide_element(last_name_error);
        } else {
            is_valid_form = false;
            show_element(last_name_error);
            last_name_error.innerHTML = "Invalid Last Name entered";
        }

        /*Performs validation of the GID */
        if (is_empty(gid)) {
            is_valid_form = false;
            show_element(gid_error);
            gid_error.innerHTML = "GID cannot be empty";
        } else if (gid[0] == 'G') {
            hide_element(gid_error);
        } else {
            is_valid_form = false;
            show_element(gid_error);
            gid_error.innerHTML = "GID must begin with G";
        }

        /*Performs validation of the NIC */
        if (is_empty(nic)) {
            is_valid_form = false;
            show_element(nic_error);
            nic_error.innerHTML = "NIC cannot be empty";
        } else {
            hide_element(nic_error);
        }

        /*Performs validation of the username */
        if (is_empty(username)) {
            is_valid_form = false;
            show_element(username_error);
            username_error.innerHTML = "Username cannot be empty";
        } else {
            hide_element(username_error);
        }

        /*Performs validation of the password */
        if (is_empty(password)) {
            is_valid_form = false;
            show_element(password_error);
            password_error.innerHTML = "Password cannot be empty";
        } else {
            if (password.length >= 6) {
                hide_element(password_error);
            } else {
                is_valid_form = false;
                show_element(password_error);
                password_error.innerHTML = "Password must be at least 6 characters long";
            }
        }

        /*Performs validations of the confirm password */
        if (is_empty(confirm_password)) {
            is_valid_form = false;
            show_element(confirm_password_error);
            confirm_password_error.innerHTML = "Confirm Password cannot be empty";
        } else {
            if (confirm_password.length >= 6) {
                if (!is_empty(password)) {
                    if (password != confirm_password) {
                        is_valid_form = false;
                        show_element(confirm_password_error);
                        confirm_password_error.innerHTML = "Passwords must match";
                    } else {
                        hide_element(confirm_password_error);
                    }
                }
            } else {
                is_valid_form = false;
                show_element(confirm_password_error);
                confirm_password_error.innerHTML = "Password must be at least 6 characters long";
            }
        }
    }
    /* 
        Returns true if no errors were found in the form
    */
    return is_valid_form;
}

function init() {
    // if the current page in the window is add_new_user.php
    if (window.location.href.match("add_new_user.php") != null) {
        var formElement = document.getElementById("register-form");
        // Validates form on submission wont submit to server if errors are detected
        formElement.onsubmit = validate_form;
    }
}

window.addEventListener("load", init);