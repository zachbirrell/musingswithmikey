<?php # Script 9.3 - register.php
// This script performs an INSERT query to add a record to the users table.

session_start();

// $page_title = 'Register';
// include('includes/header.html');

// Check for form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	require('mysqli_connect.php'); // Connect to the db

	$errors = []; // Initialize an error array.

    $subscribe_to_rss = '0';

	// Check for an email address:
	if (empty($_POST['email'])) {
		$errors[] = 'You forgot to enter your email address.';
	} else {

		$pattern = '/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/';

		if (preg_match($pattern, trim($_POST['email']))) {
			$e = mysqli_real_escape_string($dbc, $_POST['email']);
		} else {
			$errors[] = 'Email is not correctly formatted.';
		}

	}

	$q = "SELECT * FROM users WHERE email = '$e'";
	$r = @mysqli_query($dbc, $q); // Run the query.

	if (mysqli_num_rows($r) >= 1) {
		$errors[] = 'That email address cannot be used for registration. Please use a different one instead.'; 
	}

    	// Check for a password and match against the confirmed password:
	if (!empty($_POST['pass1'])) {
		if ($_POST['pass1'] != $_POST['pass2']) {
			$errors[] = 'Your password did not match the confirmed password.';
		} else {
			$p = mysqli_real_escape_string($dbc, trim($_POST['pass1']));
		}
	} else {
		$errors[] = 'You forgot to enter your password.';
	}

    // Check for username:
	if (empty($_POST['username'])) {
		$errors[] = 'You forgot to enter your user name.';
	} else {
		$un = mysqli_real_escape_string($dbc, trim($_POST['username']));
	}

	$q = "SELECT * FROM users WHERE username = '$un'";
	$r = @mysqli_query($dbc, $q); // Run the query.

	if (mysqli_num_rows($r) >= 1) {
		$errors[] = 'That username is already taken. Please use a different one.';
	}

    // Check for the birthdate.
    if (empty($_POST['month']) and empty($_POST['day']) and empty($_POST['year'])) {
		$errors[] = 'You forgot to enter your birthday.';
	} else {
		$birthday = $_POST['year'] . '-' . $_POST['month'] . '-' . $_POST['day'];
		$b = mysqli_real_escape_string($dbc, trim($birthday));
	}

    // Check to see if the user wants to subscribe to the rss feed.
    if (isset($_POST['rss'])) {
        $rss = '1';
    }

	if (empty($errors)) { // If everything's OK.

		// Register the user in the database...

		$a = md5(uniqid(rand(), true));

		// Make the query:
		$q = "INSERT INTO users (username, email, dob, user_level, active, recieve_updates, pass, registration_date) VALUES ('$un', '$e', '$b', '1', '$a', '$rss', SHA2('$p', 512), NOW() )";
		$r = @mysqli_query($dbc, $q); // Run the query.

		if ($r) { // If it ran OK.

            $base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https" : "http");
            $base_url .= "://".$_SERVER['HTTP_HOST'];
            $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

			$body = "Thank you for registering at this site. To activate your account, please click on this link:\n\n";
			$body .= $base_url . 'activate.php?x=' . urlencode($e) . '&y=' . $a;

			mail($e, 'Registration Confirmation', $body, 'From: admin@' . $_SERVER['HTTP_HOST'] .'.com');

			echo '<h1>Thank you for registering, ' . $un . '!</h1>
				<p>A confirmation email has been sent to your address. Please click on the link in that email in order to activate your account.</p><p><br></p>';

			// include('includes/footer.html');
			exit();

		} else { // If it did not run OK.

			// Public message:
			echo '<h1>System Error</h1>
			<p class="error">You could not be registered due to a system error. We apologize for any inconvenience.</p>';

			// Debugging message:
			echo '<p>' . mysqli_error($dbc) . '<br><br>Query: ' . $q . '</p>';

		} // End of if ($r) IF.

		mysqli_close($dbc); // Close the database connection.

		// Include the footer and quit the script:
		// include('includes/footer.html');
		include('includes/login_page.inc.php');
		exit();

	} else { // Report the errors.

		include('includes/login_page.inc.php');

		// echo '<h1>Error!</h1>
		// <p class="error">The following error(s) occurred:<br>';
		// foreach ($errors as $msg) { // Print each error.
		// 	echo " - $msg<br>\n";
		// }
		// echo '</p><p>Please try again.</p><p><br></p>';

	} // End of if (empty($errors)) IF.

} // End of the main Submit conditional.

?>