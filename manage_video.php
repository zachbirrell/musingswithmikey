<?php 
use Google\Service\IDS\IdsEmpty;# Script 10.3 - edit_product.php

   session_start();
   
   $page_title = 'Managing Video';
   include('includes/header.html');
   header("Access-Control-Allow-Origin: *");
   echo '<div style="display: flex; width: 100%; padding-bottom: 15px">
   <h1 style="width: 90%">' . ucfirst($_GET['a']) . ' Video</h1>
   </div>
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
                // if (!strpos($tn, 'img.youtube.com')) {
                //     $tn = "images/{$tn}";
                // }
            }

            // $tn = "images/plant.png";

            // Check for the thumbnail:
            if (empty($_POST['yt-id'])) {
                $errors[] = 'You forgot to provide a youtube video id.';
            } else {
                $yt_id = mysqli_real_escape_string($dbc, trim($_POST['yt-id']));
            }

            // Check for the thumbnail:
            if (empty($_POST['visibility'])) {
                $errors[] = 'You forgot to specify the visibility type.';
            } else {
                $vi = mysqli_real_escape_string($dbc, trim($_POST['visibility']));
            }

            // Check for the thumbnail:
            if (empty($_POST['comments'])) {
                $errors[] = 'You forgot to specify the comment type.';
            } else {
                $com = mysqli_real_escape_string($dbc, trim($_POST['comments']));
            }
            
            if (empty($errors)) { // If everything's OK.
                
                $video_id = '0';

                if (isset($_GET['id'])) {
                    $video_id = $_GET['id'];
                }

                // Make the query:
                $q = $_GET['a'] == 'new' ? "INSERT INTO video (title, publish_date, thumbnail, yt_video_id, visibility, comments) VALUES ('$t', NOW(), '$tn', '$yt_id', '$vi', '$com')" : "UPDATE video SET title='$t', modified=NOW(), thumbnail='$tn', yt_video_id='$yt_id', visibility='$vi', comments='$com' WHERE video_id='$video_id'";

                $r = @mysqli_query($dbc, $q);
                if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.

                    if ($_GET['a'] == 'new') {

                        $q = "SELECT MAX(video_id) AS 'video_id' FROM video;";
                        $r = @mysqli_query($dbc, $q);

                        $video_id = $r->fetch_row()[0] ?? false;

                        if ($video_id) {

                            $q = "INSERT INTO video_body (video_id, body) VALUES ('$video_id', '$b')";
                            $r = @mysqli_query($dbc, $q);

                            if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
                                echo '<script>alert("Your new video has been posted!")</script>';
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
    
                                $body = "A new video has been posted. It can be viewed at the following link:\n\n";
                                $body .= $base_url . 'watch_video.php?id=' . urlencode($video_id);
                                $body .= "\n\nYou're recieving this email because you've agreed to be notify when ever new content is posted on the website.";
    
                                mail($email, '[Musings with Mikey] New Video Has Been Posted', $body, 'From: admin@' . $_SERVER['HTTP_HOST'] .'.com');
                            }

                        }

                    } else {

                        $q = "UPDATE video_body SET body='$b' WHERE video_id='$video_id'";
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

            $q = "DELETE FROM video WHERE video_id='$id'";
            $r = @mysqli_query($dbc, $q);

            $q = "DELETE FROM video_body WHERE video_id='$id'";
            $r = @mysqli_query($dbc, $q);
        }

        require('includes/login_functions.inc.php');
        redirect_user('videos.php');
        
    } // End of submit conditional.

        if (isset($_GET['a']))
        {

            $title = '';
            $comments = '';
            $thumbnail = 'images/ui/upload.svg';
            $submit_value = '+ Post Video';
            $yt_id = '';
            $visibility = '';
            $c = '';

            if ($_GET['a'] == 'new') {
                $title = isset($_POST['title']) ? $_POST['title'] : '';
                $comments = isset($_POST['body']) ? $_POST['body'] : '';
            } elseif ($_GET['a'] == 'edit' or $_GET['a'] == 'delete') {
                $video_id = $_GET['id'];
                $q = "SELECT video.video_id, video.title, video.publish_date, video.views, video.thumbnail, video.yt_video_id, video.visibility, video.comments, video_body.body FROM video INNER JOIN video_body ON video.video_id = video_body.video_id WHERE video.video_id = '$video_id';";
                $r = @mysqli_query($dbc, $q);

                while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                    $title = $row['title'];
                    $comments = $row['body'];
                    $yt_id = $row['yt_video_id'];
                    $visibility = $row['visibility'];
                    $c = $row['comments'];
                    $thumbnail = empty($row['thumbnail']) ? 'https://img.youtube.com/vi/' . $yt_id . '/hqdefault.jpg' : $row['thumbnail'];
                    echo "<script>insert_thumbnail(\"../$thumbnail\")</script>";
                }

                $submit_value = $_GET['a'] == 'edit' ? "Save Changes" : "Delete Video";
            }

        }

        // Always show the form...
        $readonly = $_GET["a"] == "delete" ? "readonly" : "";
        $disable = $_GET['a'] == 'delete' ? "disabled" : "";
        $id = isset($_GET['id']) ? "&id={$_GET['id']}" : "";

        echo '<link rel="stylesheet" href="css/video.css" type="text/css">
                <form action="manage_video.php?a=' . $_GET['a'] . $id .'" method="post" style="width: 100%">
                <div class="frame">
                    <div style="display: flex";>
                        <div style="width: 200px; height: 205px; padding: 2px; padding-right: 10px; display: flex; flex-direction: column;">
                            <iframe id="yt-frame" width="200" height="200" style="border: 1px solid black;" allow="fullscreen;" src="https://www.youtube.com/embed/' . $yt_id .'"></iframe>
                            <img id="thumbnail" name="thumbnail" src="' . $thumbnail . '" alt="Video Thumbnail" width="200" height="200" style="border: 1px solid black; margin-top: 10px;"> 
                        </div>
                        <div style="display: flex; flex-direction: column; flex: 1">
                            <p style="font-weight: bold">Title:</p>
                            <div class="title">
                                <input type="text" name="title" id="a-title" placeholder="VIDEO TITLE" size="40" maxlength="40" ' . $readonly .' value="' . $title . '">
                                <p style="margin-left: 10px;" id="title-counter">' . strlen($title) . '/40</p>
                            </div>
                        <div>
                    <div>
                    <div>
                        <p style="font-weight: bold">YouTube Video ID:</p>
                        <input type="text" name="yt-id" id="a-ytid" placeholder="dQw4w9WgXcQ&pp" size="40" maxlength="40" ' . $readonly .' value="' . $yt_id . '">
                    </div>
                    <div style="margin-bottom: 7px;">
                        <p style="font-weight: bold">Thumbnail:</p>
                        <input id="a-thumbnail" type="file" name="a-thumbnail" value="" ' . $disable . ' ></input>
                        <script>upload_thumbnail_a()</script>
                    </div>
                    <div style="margin-bottom: 7px;">
                        <textarea id="blog-body" style="width: 100%; resize: none; width: 99%;" name="body" rows="30" cols="20" ' . $readonly . ' placeholder="Describe Your Video...">' . $comments . '</textarea>
                        <script>
                            tinymce.init({
                            selector: "textarea",
                            plugins: "anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount",
                            toolbar: "undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat",
                            });
                        </script>
                    </div>
                    <div style="margin-bottom: 7px;">
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
                        <label for="notify">Notify Users of Upload</label>
                    </div>
                    <div style="margin-bottom: 7px;">
                        <p style="font-weight: bold">Comments:</p>
                        <input type="radio" id="enabled" name="comments" value="enabled" ' . ($c == 'enabled' ? 'checked="checked"' : 'checked="checked"') . '>
                        <label for="enabled">Enabled</label>
                        <input type="radio" id="disabled" name="comments" value="disabled" ' . ($c == 'disabled' ? 'checked="checked"' : "") . '>
                        <label for="disabled">Disabled</label>
                    </div>
                    <input type="hidden" id="thumb" name="thumb" value="' . $thumbnail . '">
                    <p><input type="submit" name="submit" id="a-submit" style="margin-top: 5px; margin-bottom: 10px; cursor: pointer; background-color: transparent; padding: 7px;" value="' . $submit_value . '"></p>
                </div>  
                </form></div>';

            echo '<script src="javascript/textarea_expander.js"></script>';
            echo '<script src="javascript/title_counter.js"></script>';
            echo '<script>update_embed()</script>';

    mysqli_close($dbc);

}

?>