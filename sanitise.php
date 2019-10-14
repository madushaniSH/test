<?php
/*
	Filename: sanitise.php
	Author: Malika Liyanage
	Created: 15/07/2019
	Purpose: Contains sanitise_input function which is used for sanitising
	user input
*/

// function used to sanitize user input
function sanitise_input($data){
	// Removes leading or trailing spaces
	$data = trim($data);
	// Removes backslashes in front of quotes
	$data = stripslashes($data);
	// Convers HTML control characters to &lt;
	$data = htmlspecialchars($data);
	return $data;
}
?>
