<?php # Script 10.3 - edit_product.php

   session_start();
   
   $page_title = 'Add a Blog';
   include('includes/header.html');
   echo '<div style="display: flex; width: 100%; padding-bottom: 15px">
   <h1 style="width: 90%">' . ucfirst($_GET['a']) . ' Blog</h1>
   </div>
   <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
   <script type="text/javascript" src="https://code.jquery.com/jquery-1.7.1.min.js"></script>
   <script src="javascript/website_functions.js"></script>
   <script src="https://cdn.tiny.cloud/1/67lso40op4ebgurc85rjhmhmgzuie613ej313b6qts6w4hzs/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>';

if ($_SESSION['user_level'] != '2') {
    $_SESSION['error_index'] = 1;
        require('includes/login_functions.inc.php');
        redirect_user('error.php');
} else {
  
    require('mysqli_connect.php');

    // Check if the form has been submitted:
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $errors = [];

        if ($_GET['a'] == 'edit' or $_GET['a'] == 'new') {

                    // Check for the blog title.
            if (empty($_POST['title'])) {
                $errors[] = 'You forgot to enter the title.';
            } else {
                $t = mysqli_real_escape_string($dbc, trim($_POST['title']));
            }
                // Check for the blog body:
            if (empty($_POST['body'])) {
                $errors[] = 'You forgot to enter the blog body.';
            } else {
                $b = mysqli_real_escape_string($dbc, trim($_POST['body']));
            }

            // Check for the thumbnail:
            if (empty($_POST['thumb'])) {
                $errors[] = 'You forgot to provide a thumbnail.';
            } else {
                $tn = mysqli_real_escape_string($dbc, trim($_POST['thumb']));
            }

            // Check for visibility:
            if (empty($_POST['visibility'])) {
                $errors[] = 'You forgot to specify the visibility type.';
            } else {
                $vis = mysqli_real_escape_string($dbc, trim($_POST['visibility']));
            }

            // Check for visibility:
            if (empty($_POST['comments'])) {
                $errors[] = 'You forgot to specify the comment type.';
            } else {
                $c = mysqli_real_escape_string($dbc, trim($_POST['comments']));
            }
            
            if (empty($errors)) { // If everything's OK.
                
                $blog_id = '0';

                if (isset($_GET['id'])) {
                    $blog_id = $_GET['id'];
                }

                // Make the query:
                $q = $_GET['a'] == 'new' ? "INSERT INTO blog (title, published, thumbnail, visibility, comments) VALUES ('$t', NOW(), '$tn', '$vis', '$c')" : "UPDATE blog SET title='$t', modified=NOW(), thumbnail='$tn', visibility='$vis', comments='$c' WHERE blog_id='$blog_id'";

                $r = @mysqli_query($dbc, $q);
                if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.

                    if ($_GET['a'] == 'new') {

                        $q = "SELECT MAX(blog_id) AS 'blog_id' FROM blog;";
                        $r = @mysqli_query($dbc, $q);

                        $blog_id = $r->fetch_row()[0] ?? false;

                        if ($blog_id) {

                            $q = "INSERT INTO blog_body (blog_id, body) VALUES ('$blog_id', '$b')";
                            $r = @mysqli_query($dbc, $q);

                            if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
                                echo '<script>alert("Your new blog has been posted!")</script>';
                            }
                            
                        }

                        if(isset($_POST['notify'])) {

                            $q = "SELECT email, recieve_updates, username FROM users WHERE recieve_updates='1';";
                            $r = @mysqli_query($dbc, $q);
    
                            while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                                $username = $row['username'];
                                $email = $row['email'];
                                $notify = $row['recieve_updates'];
    
                                $base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https" : "http");
                                $base_url .= "://".$_SERVER['HTTP_HOST'];
                                $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
    
                                $body = "A new blog has been posted. It can be viewed at the following link:\n\n";
                                $body .= $base_url . 'read_blog.php?id=' . urlencode($blog_id);
                                $body .= "\n\nYou're recieving this email because you've agreed to be notify when ever new content is posted on the website.";
    
                                mail($email, '[Musings with Mikey] New Blog Has Been Posted', $body, 'From: admin@' . $_SERVER['HTTP_HOST']);
                            }

                        }

                    } else {

                        $q = "UPDATE blog_body SET body='$b' WHERE blog_id='$blog_id'";
                        $r = @mysqli_query($dbc, $q);

                        if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
                            echo "<script>alert('The blog has successfully updated!')</script>";
                        }

                    }

                } elseif (mysqli_affected_rows($dbc) == 0) {
                    echo '<p>No records were updated.</p>';
                } else { // If it did not run OK.
                    echo '<p class="error">The blog could not be added due to a system error. We apologize for any inconvenience.</p>'; // Public message.
                    echo '<p>' . mysqli_error($dbc) . '<br>Query: ' . $q . '</p>';
                        // Debugging message.
                }
        
            } else { // Report the errors.
        
                echo '<p class="error">The following error(s) occurred:<br>';
                        foreach ($errors as $msg) { // Print each error.
                            echo " - $msg<br>\n";
                        }
                        echo '</p><p>Please try again.</p>';
            
            } // End of if (empty($errors)) IF.

        } else {
            $id = $_GET['id'];

            $q = "DELETE FROM blog WHERE blog_id='$id'";
            $r = @mysqli_query($dbc, $q);

            $q = "DELETE FROM blog_body WHERE blog_id='$id'";
            $r = @mysqli_query($dbc, $q);
        }

        require('includes/login_functions.inc.php');
        redirect_user('blogs.php');
        
        } // End of submit conditional.

        if (isset($_GET['a']))
        {

            $title = '';
            $comments = '';
            $thumbnail = 'images/plant.png';
            $submit_value = '+ Post Blog';
            $c = '';
            $visibility = '';

            if ($_GET['a'] == 'new') {
                $title = isset($_POST['title']) ? $_POST['title'] : '';
                $comments = isset($_POST['body']) ? $_POST['body'] : '';
            } elseif ($_GET['a'] == 'edit' or $_GET['a'] == 'delete') {
                $blog_id = $_GET['id'];
                $q = "SELECT blog.blog_id, blog.title, blog.published, blog.views, blog.thumbnail, blog.visibility, blog.comments, blog_body.body FROM blog INNER JOIN blog_body ON blog.blog_id = blog_body.blog_id WHERE blog.blog_id = '$blog_id';";
                $r = @mysqli_query($dbc, $q);

                while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                    $title = $row['title'];
                    $comments = $row['body'];
                    $thumbnail = $row['thumbnail'];
                    $c = $row['comments'];
                    $visibility = $row['visibility'];
                    echo "<script>insert_thumbnail(\"../$thumbnail\")</script>";
                }

                $submit_value = $_GET['a'] == 'edit' ? "Save Changes" : "Delete Blog";
            }

        }

        // Always show the form...
        $readonly = $_GET["a"] == "delete" ? "readonly" : "";
        $disable = $_GET['a'] == 'delete' ? "disabled" : "";
        $id = isset($_GET['id']) ? "&id={$_GET['id']}" : "";

        echo '<link rel="stylesheet" href="css/blog.css" type="text/css">
                <form action="manage_blog.php?a=' . $_GET['a'] . $id .'" method="post" style="width: 100%; height: auto">
                    <div style="position: relative; width: 100%; height: 270px; display: flex; justify-content: center; align-items: center; overflow: hidden">
                        <img src="' . $thumbnail . '" id="thumbnail" name="thumbnail" style="flex-shrink: 0; min-width: 100%; min-height: 100%">
                        <div style="position: absolute; width: 100%; height: auto; color: gray; left: 0; top: 0; padding-top: 30px; z-index: 5; margin-top: 5%;">
                            <div style="display: flex; justify-content: center; align-items: center">
                                <input type="text" name="title" id="a-title" placeholder="BLOG TITLE" size="40" maxlength="40" ' . $readonly .' value="' . $title . '"><br><br>
                                <p style="margin-left: 10px;" id="title-counter">0/40</p>
                            </div>
                            <div style="display: flex; justify-content: center; align-items: center">
                                <input id="a-thumbnail" type="file" name="a-thumbnail" value="" ' . $disable . ' ></input>
                                <script>upload_thumbnail_a()</script>
                            </div>
                        </div>
                    </div>
                    <div style="width: 100%; margin-top: 10px; height: 100%">
                        <div style="margin-left: 0px; margin-right: 10px;">
                            <textarea id="blog-body" style="width: 100%; margin-bottom: 10px; resize: none; width: 100%; padding: 5px;" name="body" rows="30" cols="20" ' . $readonly . ' placeholder="What\'s on your mind?">' . $comments . '</textarea>
                            <script>
                                tinymce.init({
                                selector: "textarea",
                                plugins: "anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount",
                                toolbar: "undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat",
                                });
                            </script>
                        </div>
                        <div style="margin-bottom: 7px; margin-top: 15px;">
                            <p style="font-weight: bold">Visibility:</p>
                            <input type="radio" id="public" name="visibility" value="public" ' . ($visibility == 'public' ? 'checked="checked"' : 'checked="checked"') . '>
                            <label for="public">Public</label>
                            <input type="radio" id="hidden" name="visibility" value="hidden" ' . ($visibility == 'hidden' ? 'checked="checked"' : "") . '>
                            <label for="hidden">Hidden</label>
                            <input type="radio" id="private" name="visibility" value="private" ' . ($visibility == 'private' ? 'checked="checked"' : "") . '>
                            <label for="private">Private</label>
                        </div>
                        <div style="margin-bottom: 7px;">
                            <p style="font-weight: bold">Notification:</p>
                            <input type="checkbox" id="notify" name="notify" value="Notify">
                            <label for="notify">Notify Users of Post</label>
                        </div>
                        <div style="margin-bottom: 7px;">
                            <p style="font-weight: bold">Comments:</p>
                            <input type="radio" id="enabled" name="comments" value="enabled" ' . ($c == 'enabled' ? 'checked="checked"' : 'checked="checked"') . '>
                            <label for="enabled">Enabled</label>
                            <input type="radio" id="disabled" name="comments" value="disabled" ' . ($c == 'disabled' ? 'checked="checked"' : "") . '>
                            <label for="disabled">Disabled</label>
                        </div>
                        <input type="hidden" id="thumb" name="thumb" value="' . $thumbnail . '">
                        <p><input type="submit" name="submit" id="a-submit" value="' . $submit_value . '"></p>
                </form></div>';

            echo '<script src="javascript/textarea_expander.js"></script>';
            echo '<script src="javascript/title_counter.js"></script>';

    mysqli_close($dbc);

}

?>