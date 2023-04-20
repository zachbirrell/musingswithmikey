<?php # 
// This page is for activating a newly registered user

session_start(); // resume the session.

$page_title = 'Activate Your Account'; // set the page title in the header.
include('includes/header.html');

// if x and y don't exist, redirect user
if (isset($_GET['x'], $_GET['y'])
	&& filter_var($_GET['x'], FILTER_VALIDATE_EMAIL)
	&& (strlen($_GET['y']) == 32 ) ) {
	// update the database
	require_once('mysqli_connect.php'); 
	$q = "UPDATE users SET active=NULL WHERE 
		(email='" . mysqli_real_escape_string($dbc, $_GET['x']) . "' AND 
		active='" . mysqli_real_escape_string($dbc, $_GET['y']) . "') 
		LIMIT 1";
	$r = mysqli_query($dbc, $q) or die("Query: $q\n<br>MySQL Error: " . mysqli_error($dbc)); // execute the query. Stops php execution if an error occurs.
	
	// print customized message
	if (mysqli_affected_rows($dbc) == 1) {
		echo "<h1>Your account is now active. You may now log in.</h1>";
	}
	else {
		// echo '<p class="error">Your account could not be activated. Please re-check the link or contact the system administrator.</p>';
		$_SESSION['error_index'] = 4;
        require('includes/login_functions.inc.php');
        redirect_user('error.php');
	}
	
	mysqli_close($dbc);
	
}  else { // redirect
	require('includes/login_functions.inc.php');
    redirect_user();
	exit();
}  // end of main if 
		
// include('includes/footer.html');
// ?>
	