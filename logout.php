<?php

    session_start();

    if (!isset($_SESSION['user_id'])) { //check if the user is logged in. If not, redirect the user back to the index.php page.

        require('includes/login_functions.inc.php');
        redirect_user();

    } else {

        $un = $_SESSION['username']; // store the username variable so it's usable when the session is cleared.

        $_SESSION = []; // clear the session array.

        session_destroy(); // destroy and kill the current session and it's data.

        setcookie('PHPSESSID', '', time()-3600, '/', '', 0, 0); // use a time in the past, forcing the php session cookie to be deleted.

        unset($_COOKIE['a']); //unset an HTTP cookie variable.
        unset($_COOKIE['z']);
        setcookie('a', '', time()-3600, '/');
        setcookie('z', '', time()-3600, '/');
        
        require('mysqli_connect.php'); // Connect to the db
    
        // Make the query:
        $q = "DELETE FROM passwdhash WHERE username='$un'"; // delete the username hash from the password hash table.
        $r = @mysqli_query($dbc, $q); // Run the query.
    
        if ($r) {
        
        }
    }
    
    $page_title = 'Logged Out!';
    include('includes/header.html');
    
    echo "<div class='loggedin-container'>
    <div class='loggedin'>
        <h1>Goodbye, {$un}!</h1>
        <p id='counter' style='margin-top: 10px; display: flex; align-items: center; justify-content: center'>Proceeding to homepage in 5 seconds...</p>
    </div>
    </div>";

    echo '<script src="javascript/loggedin_counter.js"></script>';
   
  include('includes/footer.html');

?>