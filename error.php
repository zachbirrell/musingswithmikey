<?php 

    session_start();

	$page_title = 'Error';
	include('includes/header.html');

	if ($_SESSION['error_index'] == 0) {
		// An error hasn't occurred, the user has loaded the webpage from the address bar. Redirect back to the homepage.
		require('includes/login_functions.inc.php');
        redirect_user();
	 }

?>

<link rel="stylesheet" href="css/index.css" type="text/css">
<div class='flexbox-container-center'>
	<div id='error-container'>
		<h1>An Error Has Occurred!</h1>
		<?php
		
			$error_msg = '';

			switch ($_SESSION['error_index']) {
				case 1:
					$error_msg = 'You don\'t have the proper permissions to access that page.';
					break;
				case 2:
					$error_msg = 'You must be signed in to access this page. Please login to/create an account, and try again.';
					break;
				case 3:
					$error_msg = 'You\'re already logged in.';
					break;
				case 4:
					$error_msg = 'Your account could not be activated. Please re-check the link or contact the system administrator.';
					break;
				case 5:
					$error_msg = 'That email address cannot be used for registration. Please use a different one instead.';
					break;
				case 6:
					$error_msg = 'The requested video has been set to private.';
					break;
				case 7:
					$error_msg = 'The requested blog has been set to private.';
					break;
				default:
					$error_msg = 'This page has been accessed in error.';
					break;
			}
			
			echo '<p id="error-message">' . $error_msg . '</p>';

			// reset the error index back to 0.
			$_SESSION['error_index'] = 0;
		?>
		<p id="counter">Proceeding to homepage in 3 seconds...</p>
	</div>
</div>

<script src="javascript/loggedin_counter.js"></script>