<?php
    session_start();

    $page_title = $_SESSION['username'];
    include('includes/header.html');
    echo '<script type="text/javascript" src="https://code.jquery.com/jquery-1.7.1.min.js"></script>';

    // non-logged in user shouldn't access this page
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
    }

    // log out the user and redirect to login page
    if (isset($_GET['action']) && ('logout' == $_GET['action'])) {
        unset($_SESSION['user_id']);
        header('Location: login.php');
    }

    require('mysqli_connect.php'); // Connect to the db

    $user_id = $_SESSION['user_id'];

    if (isset($_GET['id'])) {
        if ($_SESSION['user_level'] == '2') {
            $user_id = $_GET['id'];
        }
    }

    $user = [];

    $q = "SELECT * FROM users WHERE user_id='$user_id'";
    $r = @mysqli_query($dbc, $q);

    if (mysqli_num_rows($r) == 1) {
        $user = mysqli_fetch_array($r, MYSQLI_ASSOC);
    }

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

    function retrieve_comments($dbc, $type) { //read the comments from the database and return them.

        $html = '';
        // $user_id = $_SESSION['user_id'];
        $user_id = $_SESSION['user_id'];

        if (isset($_GET['id'])) {
            if ($_SESSION['user_level'] == '2') {
                $user_id = $_GET['id'];
            }
        }    
        $item_id = '';
        $where = ($_SESSION['user_level'] == '2' AND !isset($_GET['id'])) ? "" : "WHERE users.user_id='$user_id' AND ${type}_comments.deleted='0'";

        $q = "SELECT ${type}_comments.*, ${type}_comments_body.body, users.username FROM ${type}_comments INNER JOIN ${type}_comments_body ON ${type}_comments.comment_id = ${type}_comments_body.comment_id INNER JOIN users ON users.user_id = ${type}_comments.user_id ${where} ORDER BY ${type}_id;";
        $r = @mysqli_query($dbc, $q);
    
        // echo '<pre>' . print_r($r) . '</pre>';

        // $html .= '<h1 style="margin-bottom: 10px;">Comments (' . $r->num_rows .')</h1>';
    
        while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {

            // echo '<pre>' . print_r($row) . '</pre>';

            if ($row[$type . '_id'] != $item_id) {

                $item_id = $row[$type . '_id'];

                $q = "SELECT ${type}.*, ${type}_body.* FROM ${type} INNER JOIN ${type}_body ON ${type}_body.${type}_id = ${type}.${type}_id WHERE ${type}.${type}_id='$item_id'";
                $re = @mysqli_query($dbc, $q);

                while ($ro = mysqli_fetch_array($re, MYSQLI_ASSOC)) { 
                    // echo '<pre>' . print_r($ro) . '</pre>';
                    $html .= '<div style="display: flex; border: 1px solid black; padding: 7px; margin-bottom: 15px; margin-top: 15px;">
                            <img src="' . $ro['thumbnail'] . '" width="50" height="50">
                            <div style="display: flex; flex-direction: column; padding-left: 7px; padding-right: 7px;">
                                <h3 style="">' . $ro['title'] . '</h3>
                                <p>' . mb_strimwidth($ro['body'], 0, 200, '...') . '</p>
                            </div>
                    </div>';
                }
            }

            $actions = '';
            $comment_id = $row['comment_id'];
            $body = spam_scrubber($row['body']);

            $actions .= '<button type="button" id="comment-delete-' . $comment_id . '" class="comment-delete" value="delete" onclick="on(' . $comment_id . ', \'delete\', \'' . $type . '\')">Delete</button>';

            // print_r($row);
            $html .= '<div style="border-left: 1px solid green; display: flex; height: auto; margin-left: px; width: 100%;">';

            $html .= '<div class="comment" id="comment-' . $comment_id . '" style="display: flex; width: 100%;">
                <div>
                    <div style="display: flex;">
                        <img src="images/ui/user.png" width="50" height="50">
                        <div>
                            <h3 id="comment-username">' . $row['username'] . '</h3>
                            <p id="comment-date">' . date('m/d/Y', strtotime($row['posted'])) . '</p>
                        </div>
                    </div>
                    <div id="comment-details-' . $comment_id . '" style="">
                        <div class="comment-body" id="comment-body-' . $comment_id . '">' . $body . ' </div>
                        '. $actions . ' 
                    </div>
                    <div id="manage-comment-' . $comment_id . '" style="margin-top: 7px; display: none; width: 98%">
                        <textarea id="edit-comment-' . $comment_id . '" style="resize: none; margin-left: 50px; margin-right: 50px; width: 100%; box-sizing: border-box; -moz-box-sizing: border-box;" name="body" rows="5" cols="20" placeholder="' . $body . '">' . $body . '</textarea>
                        <div style="width: 90%">
                        <button type="button" id="" class="c-action" value="Cancel" style="margin-left: 50px; margin-top: 5px; margin-bottom: 15px; padding: 5px;" onclick="off(' . $comment_id .')">Cancel</button>
                        <button type="button" id="" class="c-action" value="delete" style="margin-left: 5px; margin-top: 5px; margin-bottom: 15px; padding: 5px;" onclick="on(' . $comment_id . ', \'update\')">Update</button>
                        </div>
                    </div>
                </div>
                </div></div>';
        }

        return $html;
    }
?>

<body>
    <link rel="stylesheet" href="css/profile.css" type="text/css">
    <h1 style="margin-left: 15px; margin-bottom: 15px;">My Profile</h1>

    <div class="card" id="user-details">
        <img src="images/ui/user.png" width="100" height="100">
        <div style="display: flex; flex-direction: column; margin-top: 15px;">
            <div style="display: flex;">
                <p id="user-name"><?php echo $user['username']; ?></p>
                <?php echo ($_SESSION['user_level'] == '2' ? '<a style="margin-left: 7px; margin-top: 2px;" href="user_management.php">User Management</a>' : '') ?>
            </div>
            <p id="user-email"><?php echo $user['email']; ?></p>
            <p id="user-birthday"><?php echo '<strong>Birthday:</strong> ' . date('m/d/Y', strtotime($user['dob'])) . ', <strong>Member Since:</strong> ' . date('m/d/Y', strtotime($user['registration_date'])); ?></p>
        </div>
    </div>

    <h2 style="margin-left: 15px; margin-bottom: 15px; margin-top: 15px;">Comment History</h2>
    
    <div style="margin-right: 30px;">
        <div style="width: 100%; display: flex; padding-left: 15px;">
            <div id="blog-comments-container" style="width: 50%; border: 1px solid black; padding: 7px; height: 26px;">
                <div style="display: flex; width: 100%;">
                    <h3>Blogs</h3>
                    <button type="button" id="toggle-comments-blog" style="margin-left: auto; margin-top: 0px; margin-right: 5px;" class="comment-delete" value="hidden" onclick="showComments('blog')">Show Comments</button>
                </div>
                <div id="comments-blog" style="display: none;">
                    <?php echo retrieve_comments($dbc, 'blog'); ?>
                </div>
            </div>
            <div id="video-comments-container" style="width: 50%; border: 1px solid black; margin-left: 15px; padding: 7px; height: 26px;">
            <div style="display: flex; width: 100%;">
                    <h3>Videos</h3>
                    <button type="button" id="toggle-comments-video" style="margin-left: auto; margin-top: 0px; margin-right: 5px;" class="comment-delete" value="hidden" onclick="showComments('video')">Show Comments</button>
                </div>
                <div id="comments-video" style="display: none;">
                    <?php echo retrieve_comments($dbc, 'video'); ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        function off(id) {
            document.getElementById(`manage-comment-${id}`).style.display = "none";
            document.getElementById(`comment-details-${id}`).style.display = "block";
        }
        function on(id, action, table) {
            console.log(action);
            if (action == 'delete') {
                if (confirm("The comment will be deleted.") == true) {
                    console.log("Delete comment");
                    $.post("manage_comment.php", {a: action, id: `${id}`, t: `${table}`}).done(function(data) {
                        if (data == "Success") {
                            document.getElementById(`comment-${id}`).style.display = "none";
                        } else if (data == "Access Denied") {
                            alert("Hold up! You just tried to delete another person\'s comment without their permission? That\'s not cool!\n\nPlease be more respectable towards others in your community. 10 points have been removed from your account. If your points fall below 100 you will no longer be able to comment on blogs or videos.\n\nPlease respect others and not try to delete their posts. Thank you!");
                            off(id);
                        } else {
                            console.log("Failed to delete comment.");
                        }
                    });
                } else {
                    console.log("dont delete comment");
                };
            }
            
            if (action == 'edit') {
                document.getElementById(`manage-comment-${id}`).style.display = "block";
                document.getElementById(`comment-details-${id}`).style.display = "none";
            }
            
            if (action == 'update') {
                var newText = document.getElementById(`edit-comment-${id}`).value;
                console.log(newText);
                $.post("manage_comment.php", {a: action, id: `${id}`, newtext: newText, t: "video"}).done(function(data) {
                    if (data == "Success") {
                        document.getElementById(`comment-body-${id}`).innerHTML = '';
                        document.getElementById(`comment-body-${id}`).innerHTML = newText;
                        off(id);
                    } else if (data == "Access Denied") {
                        alert("Hold up! You just tried to edit another person\'s comment without their permission? That\'s not cool!\n\nPlease be more respectable towards others in your community. 10 points have been removed from your account. If your points fall below 100 you will no longer be able to comment on blogs or videos.\n\nPlease respect others and not try to edit their posts. Thank you!");
                        off(id);
                    } else {
                        console.log("Failed to delete comment.");
                    }
                });
            }
            
        }
        function showComments(table) {
            var status = document.getElementById(`toggle-comments-${table}`);

            if (status.value == 'visible') {
                document.getElementById(`comments-${table}`).style.display = "none";
                document.getElementById(`${table}-comments-container`).style.height = "26px";
                status.value = 'hidden';
                status.innerText = 'Show Comments';
            } else if (status.value == 'hidden') {
                document.getElementById(`comments-${table}`).style.display = "block";
                document.getElementById(`${table}-comments-container`).style.height = "100%";
                status.value = 'visible';
                status.innerText = 'Hide Comments';
            }
        }
    </script>



</body>

</html>