/*
    Filename: new_sku_form.php
    Author: Malika Liyanage
    Created: 24/07/2019
    Purpose: Used for switching tabs in the sku form
*/

// function for handling the tabs in the form in new_sku_form.php
function open_form_tab(evt, tab_name) {
    // all the variables which are needed
    /*
        i - used for keep track of increments in the for loop
        tab_content - stores all the elements from the document with a class name of tabcontent
        tab_links - stores all the elements from the document with a class name of tablinks
    */
    var i, tab_content, tab_links;

    // hides all the elements with a class name of tabcontent
    tab_content = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tab_content.length; i++) {
        tab_content[i].style.display = "none";
    }

    // removes the class "active" from the elements with a class name of "tablinks"
    tab_links = document.getElementsByClassName("tablinks");
    for (i = 0; i < tab_links.length; i++) {
        tab_links[i].className = tab_links[i].className.replace(" active", "");
    }

    // shows the current tab and adds an "active" class to it
    document.getElementById(tab_name).style.display = "block";
    evt.currentTarget.className += " active";
    return false;
}

// handles the displaying of the sub tabs in the form
function open_form_sub_tab(evt, sub_tab_name) {
    var i, sub_tab_content, sub_tab_links;
    // hides all the elements with a class name of tabcontent
    sub_tab_content = document.getElementsByClassName("sub_tabcontent");
    for (i = 0; i < sub_tab_content.length; i++) {
        sub_tab_content[i].style.display = "none";
    }

    // removes the class "active" from the elements with a class name of "tablinks"
    sub_tab_links = document.getElementsByClassName("sub_tablinks");
    for (i = 0; i < sub_tab_links.length; i++) {
        sub_tab_links[i].className = sub_tab_links[i].className.replace(" active", "");
    }

    // shows the current tab and adds an "active" class to it
    document.getElementById(sub_tab_name).style.display = "block";
    evt.currentTarget.className += " active";
    return false;
}

// function used to set the date inputs in the alternative design form
function set_alt_design_dates() {
    var today = new Date();
    // sets the start date for the alt design as today's date
    document.getElementById('alt_start_date').valueAsDate = today;
}

// function called when there is a chane in the Product Type option in the form
function check_option() {
    var product_type = document.getElementById("product_type");
    var sku_section = document.getElementById("sku_only");
    var alt_design_link = document.getElementById("alt_design_link");

    // displays the options for sku if selected 
    if (product_type.value != 'sku') {
        sku_section.style.display = "none";
    } else {
        sku_section.style.display = "block";
    }

    // displays the alternative design tab if sku or pos is selected
    if (product_type.value == 'sku' || product_type.value == 'pos') {
        alt_design_link.style.display = "block";
        set_alt_design_dates();
    } else {
        alt_design_link.style.display = "none";
    }
}

function init() {

    // displays the default tab when the page is loaded
    var default_open = document.getElementsByClassName("default_open");
    for (var i = 0; i < default_open.length; i++) {
        default_open[i].click();
    }
}

window.addEventListener("load", init);