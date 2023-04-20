<?php

    session_start();
    session_regenerate_id();

    if  (!isset($_SESSION['user_id']) or ($_SESSION['agent'] != sha1($_SERVER['HTTP_USER_AGENT']))) {
        
        require('includes/login_functions.inc.php');
        redirect_user();
    }

    $cookieString = password_hash($_SESSION['username'], PASSWORD_DEFAULT);
    setcookie('a', $cookieString, time() + 86400 * 30, '/'); //cookie will expire in 30 days.
    setcookie('z', $_SESSION['username'], time() + 86400 * 30, '/');
    $un = $_SESSION['username'];

    require('mysqli_connect.php'); // Connect to the db

    $q = "SELECT user_id, email, user_level FROM users WHERE username='$un'";
    $r = @mysqli_query($dbc, $q);

    if (mysqli_num_rows($r) == 1) {
        $q = "DELETE FROM passwdhash WHERE username='$un'";
        $r = @mysqli_query($dbc, $q); // Run the query.
    }

    // Make the query:
    $q = "INSERT INTO passwdhash (username, salt, created) VALUES ('$un', '$cookieString', NOW() )";
    $r = @mysqli_query($dbc, $q); // Run the query.

    if ($r) {
    
    } else {
        echo "<h1>An error occurred</h1><br><h1>$r</h1>";
    }

    $page_title = 'Logged In';
    include('includes/header.html');

    echo "<div class='loggedin-container'>
            <div class='loggedin'>
                <h1>Welcome to Musings with Mikey, {$_SESSION['username']}!</h1>
                <p id='counter' style='margin-top: 10px; display: flex; align-items: center; justify-content: center'>Proceeding to homepage in 5 seconds...</p>
            </div>
        </div>";

    echo '<script src="javascript/loggedin_counter.js"></script>';

    include('includes/footer.html');

?>