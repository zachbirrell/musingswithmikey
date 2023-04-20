<?php
    session_start();

    require_once 'vendor/autoload.php';
    require('mysqli_connect.php'); // Connect to the db

    $client_id = "641158310893-45ftev0jvmqk3glnikurart6bjlg100.apps.googleusercontent.com";
    $id_token = $_POST['response'];
    $client = new Google_Client(['client_id' => $client_id]);
    $payload = $client->verifyIdToken($id_token); // verify JWT token received

    if ($payload) { 

        $un = str_replace('@gmail.com', '', $payload['email']);
        $e = $payload['email'];
        $b = '2000-01-01';
        $a = '';
        $rss = '0';
        $p = $payload['sub'];
        $r = '';

        $q = "SELECT * FROM users WHERE email = '$e'";
        $r = @mysqli_query($dbc, $q); // Run the query.

        if (mysqli_num_rows($r) == 1) {
            $q = "UPDATE users SET username = '$un', email = '$e', dob = '$b', user_level = '1', active = '', recieve_updates = '0', pass = SHA2('$p', 512), registration_date = NOW() WHERE email='$e'";
            $r = @mysqli_query($dbc, $q); // Run the query.
        } else {
            // send user data to the database
            $q = "INSERT INTO users (username, email, dob, user_level, active, recieve_updates, pass, registration_date) VALUES ('$un', '$e', '$b', '1', '$a', '$rss', SHA2('$p', 512), NOW() )";
		    $r = @mysqli_query($dbc, $q); // Run the query.
        }
        
		if ($r) {

            $q = "SELECT user_id FROM users WHERE email='$e'";
            $r = @mysqli_query($dbc, $q);

            if (mysqli_num_rows($r) == 1) {
                // set user id in session aka log in the user
                if (!isset($_SESSION['user_id'])) {
                    $user = mysqli_fetch_array($r, MYSQLI_ASSOC);
                    $_SESSION['user_id'] = $user['user_id'];
                }

                $_SESSION['user_level'] = '1';
                $_SESSION['username'] = $un;
                $_SESSION['email'] = $e;
                $_SESSION['error_index'] = 0;

                $_SESSION['agent'] = sha1($_SERVER['HTTP_USER_AGENT']);
            }

            echo 'success';

			exit();

		} else {

			// Public message:
			echo '<h1>System Error</h1>
			<p class="error">You could not be registered due to a system error. We apologize for any inconvenience.</p>';

			// Debugging message:
			echo '<p>' . mysqli_error($dbc) . '<br><br>Query: ' . $q . '</p>';

		} // End of if ($r) IF.

    } else {
        echo 'Invalid Token';
    }
