<?php 
    session_start();

    function spam_scrubber($value) {

        // List of very bad values:
        $very_bad = ['to:', 'cc:', 'bcc:', 'content-type:', 'mime-version:', 'multipart-mixed:', 'content-transfer-encoding:', '<script', '</script>', '<?php', '?>', '<iframe'];

        // If any of the very bad strings are in
        // the submitted value, return an empty string:
        foreach ($very_bad as $v) {

            if (stripos($value, $v) !== false) {
                $value = str_replace($v, '', $value);
            }

        }

        // Replace any newline characters with spaces:
        $value = str_replace(["\r", "\n", "%0a", "%0d"], ' ', $value);

        // Return the value:
        return trim($value);

    } // End of spam_scrubber() function.

    $action = $_POST['a']; // action to peform on the comment.
    $id = $_POST['id']; // comment id.

    require('mysqli_connect.php');

    $q = "SELECT user_id FROM users WHERE user_id='$id'";
    $r = @mysqli_query($dbc, $q);

    $uid = $r->fetch_row()[0] ?? false;

    if ($_SESSION['user_level'] != '2') {
        echo "Access Denied";
        exit();
    }

    if ($action == 'delete') {

        // Delete the user.
        $q = "DELETE FROM users WHERE user_id='$id'";
        $r = @mysqli_query($dbc, $q);

        // Delete all blog comments made by the user.
        $q = "DELETE FROM blog_comments WHERE user_id='$id'";
        $r = @mysqli_query($dbc, $q);

        // Delete all video comments made by the user.
        $q = "DELETE FROM video_comments WHERE user_id='$id'";
        $r = @mysqli_query($dbc, $q);

        // Delete the password hash from the database.
        $q = "DELETE FROM passwdhash WHERE username=(SELECT username FROM users WHERE user_id='$id')";
        $r = @mysqli_query($dbc, $q);

        echo "Success";

    }

    if ($action == 'disable' OR $action == 'enable') {

        $disabled = '1';

        if ($action == 'enable') {
            $disabled = '0';
        }

        // Disable the user, preventing them from logging in.
        $q = "UPDATE users SET user_disabled='$disabled' WHERE user_id='$id'";
        $r = @mysqli_query($dbc, $q);

        // Set the blog comments to 1. This flag prevents comments from being displayed.
        $q = "UPDATE blog_comments SET deleted='$disabled' WHERE user_id='$id'";
        $r = @mysqli_query($dbc, $q);

        // Set the video comments to 1. This flag prevents comments from being displayed.
        $q = "UPDATE video_comments SET deleted='$disabled' WHERE user_id='$id'";
        $r = @mysqli_query($dbc, $q);

        // Delete the password hash from the database. This invalidates the set cookies and prevents autologin.
        $q = "DELETE FROM passwdhash WHERE username=(SELECT username FROM users WHERE user_id='$id')";
        $r = @mysqli_query($dbc, $q);

        echo "Success";
    }

?>