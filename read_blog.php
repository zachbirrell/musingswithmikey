<?php 

    session_start();

	$page_title = 'Home';
	include('includes/header.html');
    require('mysqli_connect.php');
    echo '<script type="text/javascript" src="https://code.jquery.com/jquery-1.7.1.min.js"></script>';

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

    function retrieve_comments($dbc, $submit_button, $readonly) { //read the comments from the database and return them.

        $blog_id = $_GET['id'];
        $html = '';

        if (!isset($_SESSION['user_id'])) {
            return '';
        }

        $q = "SELECT blog_comments.*, blog_comments_body.body, users.username FROM blog_comments INNER JOIN blog_comments_body ON blog_comments.comment_id = blog_comments_body.comment_id INNER JOIN users ON users.user_id = blog_comments.user_id WHERE blog_comments.blog_id='$blog_id' AND blog_comments.deleted='0';";
        $r = @mysqli_query($dbc, $q);
    
        // echo '<pre>' . print_r($r) . '</pre>';

        $html .= '<h1 style="margin-bottom: 10px; margin-top: 2%;">Comments (' . $r->num_rows .')</h1>';
        
        if ($readonly == 'true') {
            $html .= '<div style="border: 1px solid black; padding: 15px;">
                <h3 style="color: red">Commenting has been disabled for this blog.</h3>
            </div>';
            return $html;
        }

        $html .= '<div class="tabcontents" style="height: auto">
        <form class="commentsform" action="read_blog.php?id='. $_GET["id"] . '" method="post">
            <textarea class="comments" name="comments" id="comments" rows="1" cols="20" placeholder="Commenting as ' . $_SESSION['username'] . '...">' . '' . '</textarea>
            <script>
                tinymce.init({
                selector: ".comments",
                plugins: "",
                toolbar: true,
                menubar: false,
                readonly: ' . $readonly . ',
                resize: false,
                });
            </script>
            ' . $submit_button . '
            <div id="comments">';
    
        while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {

            $actions = '';
            $comment_id = $row['comment_id'];
            $body = spam_scrubber($row['body']);

            if ($row['user_id'] == $_SESSION['user_id']) {
                $actions .= '<button type="button" id="comment-edit-' . $comment_id . '" class="comment-edit" value="edit" onclick="on(' . $comment_id . ', \'edit\')">Edit</button>';
                $actions .= '<button type="button" id="comment-delete-' . $comment_id . '" class="comment-delete" value="delete" onclick="on(' . $comment_id . ', \'delete\')">Delete</button>';
            }
            // print_r($row);
            $html .= '<div class="comment" id="comment-' . $comment_id . '">
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
                </div>';
        }

        $html .= '</div></form></div>';

        return $html;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // require_once('mysqli_connect.php'); // Connect to the db

        $errors = [];

        $id = $_GET['id'];
        $uid = $_SESSION['user_id'];

        if (empty($_POST['comments'])) {
            $errors[] = 'You forgot to enter comments.';
        } else {
            $c = mysqli_real_escape_string($dbc, trim($_POST['comments']));
            $c = spam_scrubber($c);
        }

        if (empty($errors)) {

            $q = "SELECT comments FROM blog WHERE blog.blog_id='$id';";
            $r = @mysqli_query($dbc, $q);

            $comments = $r->fetch_row()[0] ?? false;
            echo $comments;

            if ($comments != 'disabled') {
                // Make the query:
                $q = "INSERT INTO blog_comments (user_id, blog_id, posted) VALUES ('$uid', '$id', NOW())";
                $r = @mysqli_query($dbc, $q);

                if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.

                    $q = "SELECT MAX(comment_id) AS 'comment_id' FROM blog_comments;";
                    $r = @mysqli_query($dbc, $q);

                    $comment_id = $r->fetch_row()[0] ?? false;

                    if ($comment_id) {
                        $q = "INSERT INTO blog_comments_body (comment_id, body) VALUES ('$comment_id', '$c')";
                        $r = @mysqli_query($dbc, $q);
                    } else {
                        echo "<h1>Failed to add to comments body.</h1>";
                    }   
                }
            } else {
                echo '<script language="javascript">alert("As stated before, comments have been disabled for this blog. However, I applaud you for making it this far. But seriously, the comments are disabled for a reason!\n\nIf you need to contact Mike or an admin please use the Contact form and we will get back with you as soon as possible.")</script>';
            }
        }
    }

    $blog_id = '1';
    $title = 'An Error Has Occurred!';
    $body = 'There was an error fetching the blog. Please try again later.';
    $thumbnail = 'images/plant2.png';
    $date = '1/1/2023';

    if (isset($_GET['id'])) {
        $blog_id = $_GET['id'];
    }

    require_once('mysqli_connect.php'); // Connect to the db

    $q = "SELECT blog.blog_id, blog.title, blog.published, blog.views, blog.thumbnail, blog.comments, blog.visibility, blog_body.body FROM blog INNER JOIN blog_body ON blog.blog_id = blog_body.blog_id WHERE blog.blog_id = '$blog_id';";
    $r = @mysqli_query($dbc, $q);

    // echo '<pre>' . print_r($r) . '</pre>';

    while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
        // echo '<pre>' . print_r($row) . '</pre>';
        $title = $row['title'];
        $body = $row['body'];
        $thumbnail = $row['thumbnail'];
        $date = $row['published'];
        $blog_comments = $row['comments'];
        $visibility = $row['visibility'];
    }

    echo "<script>document.title = '${title} - Musings with Mikey'</script>";

    if ($visibility == 'private' AND $_SESSION['user_level'] != 2) {
        $_SESSION['error_index'] = 7;
        require('includes/login_functions.inc.php');
        redirect_user('error.php');
    }

    $q = "UPDATE blog SET views = views + 1 WHERE blog.blog_id = '$blog_id';";
    $r = @mysqli_query($dbc, $q);

    $readonly = ($blog_comments == 'disabled') ? ('true') : ('false');
    $submit_button = ($blog_comments == 'disabled') ? ('') : ('<button class="submit" style="margin-bottom: 30px; margin-top: 15px;" type="submit">Post Comment</button>');
    $comments_tab = ($blog_comments == 'disabled') ? ('comments_disabled()') : ('showContents(1)');
    $comment_box = (isset($_SESSION['user_id']) ? '' : '');


echo '<body>
    <link rel="stylesheet" href="css/blog.css" type="text/css">
    <script src="https://cdn.tiny.cloud/1/67lso40op4ebgurc85rjhmhmgzuie613ej313b6qts6w4hzs/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <div style="position: relative; width: 100%; height: 270px; display: flex; justify-content: center; align-items: center; overflow: hidden; border: 1px solid black;">
        <img src="' . $thumbnail . '" id="thumbnail" name="thumbnail" style="flex-shrink: 0; min-width: 100%; min-height: 100%">
        <div style="position: absolute; width: 100%; height: auto; color: white; left: 0; top: 0; padding-top: 30px; z-index: 5; margin-top: 5%;">
            <div style="display: flex; justify-content: center; align-items: center">
                <div style="background-color: rgba(0, 0, 0, 0.4); padding: 7px;">
                    <h1>' . $title .'</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="blog-body" style="flex: 1;">
        <textarea style="width: 100%; padding: 3px;" readonly id="desc">Published on <b>' . date("m/d/Y", strtotime($date)) . '</b> by <b>Mike Birrell</b>' . $body . '</textarea>
        <script>
            tinymce.init({
            selector: "#desc",
            plugins: "autoresize",
            autoresize_min_height: 100,
            autoresize_max_height: 7000,
            autoresize_bottom_margin: 0,
            toolbar: false,
            menubar: false,
            readonly: true,
            resize: false,
            });
        </script>
    </div>' . retrieve_comments($dbc, $submit_button, $readonly) . '
    <script>
        function off(id) {
            document.getElementById(`manage-comment-${id}`).style.display = "none";
            document.getElementById(`comment-details-${id}`).style.display = "block";
        }
        function on(id, action) {
            console.log(action);
            if (action == \'delete\') {
                if (confirm("The comment will be deleted.") == true) {
                    console.log("Delete comment");
                    $.post("manage_comment.php", {a: action, id: `${id}`, t: "blog"}).done(function(data) {
                        alert("data Loaded:" + data);
                        if (data == "Success") {
                            document.getElementById(`comment-${id}`).style.display = "none";
                        } else if (data == "Access Denied") {
                            alert("Hold up! You just tried to delete another person\'s comment without their permission? That\'s not cool!\n\nPlease be more respectable towards others in your community. 10 points have been removed from your account. If your points fall below 100 you will no longer be able to comment on blogs or videos.\n\nPlease respect others and not try to delete their posts. Thank you!");
                            off(id);
                        } else {
                            console.log("Failed to delete comment.");
                        }
                    });
                }
            }
            
            if (action == \'edit\') {
                document.getElementById(`manage-comment-${id}`).style.display = "block";
                document.getElementById(`comment-details-${id}`).style.display = "none";
            }
            
            if (action == \'update\') {
                var newText = document.getElementById(`edit-comment-${id}`).value;
                console.log(newText);
                $.post("manage_comment.php", {a: action, id: `${id}`, newtext: newText, t: "blog"}).done(function(data) {
                    console.log(data);
                    if (data == "Success") {
                        document.getElementById(`comment-body-${id}`).innerHTML = \'\';
                        document.getElementById(`comment-body-${id}`).innerHTML = newText;
                        off(id);
                    } else if (data == "Access Denied") {
                        alert("Hold up! You just tried to edit another person\'s comment without their permission? That\'s not cool!\n\nPlease be more respectable towards others in your community. 10 points have been removed from your account. If your points fall below 100 you will no longer be able to comment on blogs or videos.\n\nPlease respect others and not try to edit their posts. Thank you!");
                        off(id);
                    } else {
                        console.log("Failed to edit comment.");
                    }
                });
            }
            
        }
    </script>
    <script src="javascript/website_functions.js"></script>
    <script>insert_thumbnail("' . $thumbnail . '")</script>

    </div>'

?>
