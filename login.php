<?php

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            require('includes/login_functions.inc.php');

            require('mysqli_connect.php'); // connect to the database.

            list($check, $data) = check_login($dbc, $_POST['email'], $_POST['pass']); // check if the user specified username and password match one on file. Returns the check status, and user info (if check was a success).

            if ($check) { // check if the user has successfully authenticated.

                session_start(); // start a new session.

                // set the session variables.
                $_SESSION['user_id'] = $data['user_id'];
                $_SESSION['user_level'] = $data['user_level'];
                $_SESSION['username'] = $data['username'];
                $_SESSION['email'] = $_POST['email'];
                $_SESSION['error_index'] = 0;


                $_SESSION['agent'] = sha1($_SERVER['HTTP_USER_AGENT']); // get the user agent string. Can be checked to see if session hijacking has occured.

                redirect_user('loggedin.php'); // redirect the user to the logged in page.

            } else {
                $errors = $data; // return the errors that occured during login.
            }

            mysqli_close($dbc); // close the database connection.
    }

    include('includes/login_page.inc.php'); // load the login_page again.

?>