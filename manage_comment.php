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
    $table = $_POST['t']; // Specifies what table to modify.

    require('mysqli_connect.php');

    $q = "SELECT user_id FROM {$table}_comments WHERE comment_id='$id'";
    $r = @mysqli_query($dbc, $q);

    $uid = $r->fetch_row()[0] ?? false;

    if ($uid != $_SESSION['user_id']) {
        echo "Access Denied";
        exit();
    }

    if ($action == 'delete') {

        $q = "DELETE FROM {$table}_comments WHERE comment_id='$id'";
        $r = @mysqli_query($dbc, $q);

        if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.

            $q = "DELETE FROM {$table}_comments_body WHERE comment_id='$id'";
            $r = @mysqli_query($dbc, $q);
            if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
                echo "Success";
            } else {
                echo "Failed";
            }
            
        } else {
            echo "Failed";
        }
    }

    if ($action == 'update') {

        if (empty($_POST['newtext'])) {
            echo "Failed";
        } else {

            $newtext = $_POST['newtext'];
            $newtext = spam_scrubber($newtext);

            $q = "UPDATE {$table}_comments SET modified = NOW(), edited='1' WHERE comment_id='$id'";
            $r = @mysqli_query($dbc, $q);
    
            if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
                $q = "UPDATE {$table}_comments_body SET body = '$newtext' WHERE comment_id='$id'";
                $r = @mysqli_query($dbc, $q);
                if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
                    echo "Success";
                } else {
                    echo "Failed";
                }
                
            } else {
                echo "Failed";
            }
        }
    }


?>