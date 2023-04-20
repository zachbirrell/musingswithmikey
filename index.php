<?php 

    session_start(); //resume existing session on the page.

	$page_title = 'Home';
	include('includes/header.html'); //integrate the header into the page.
?>
    <link rel="stylesheet" href="css/index.css" type="text/css"> <!-- Load the index css file. -->
    <div id="welcome-container">
        <img src="images/logo.jpg">
        <div style="flex-direction: column">
            <h1 id="welcome-title">Welcome to Musings with Mikey!</h1>
            <div style="padding-left: 15px; padding-right: 10px;">
            <p>Hi, and welcome to my blog. My name is Mike. My intention with this blog, as well as sometimes, vlog, will be to paint a picture of my life. I will accomplish this through the means of stories, thoughts, ideas, examples and most importantly, experience. As someone who lives with several mental illness challenges, life for me, is not always as it seems. I like to present myself, always, as is. What you see is what you get. I have no hidden agendas, secrets, ideas, etc,. I find as I navigate this ever changing world, I have more questions than answers. With all that in mind, please join me for a wild ride and let's get this party started.</p>
            <br><p>If you haven't registered an account yet, I highly suggest that you do. The perks of being a registered member is as follows:</p>
            <div style="margin-left: 30px; margin-top: 3px;">
                <ul>
                    <li>Gain access to uploaded blogs/videos. (unregistered users can still access the Introductory Blog/Video)</li>
                    <li>Recieve an email notification when new content is posted.</li>
                    <li>Comment on blogs and videos.</li>
                    <li>Contact me directly from the website.</li>
                    <li>Recieve a personalized shoutout from Mikey, and an answer to a question of your choice.</li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    
    <h2>Newest Blog</h2>
                <?php 

                    $title = 'Sample Blog'; // declare the template strings.
                    $thumbnail = 'images/plant.png';
                    $posted = '2/14/2023';
                    $description = 'This section is where the first few pargraphs of the blog will be displayed.';
                    $id = '1';
                    $blog_arr = []; //this array holds the info for the newest blog.
                    $newest_date = ''; //holds the newest blog date.

                    require_once('mysqli_connect.php'); // Connect to the db

                    // If the user is logged in get the newest blog, if logged out, get the introductory blog.
                    $q = "SELECT blog.*, blog_body.body FROM blog INNER JOIN blog_body ON blog.blog_id = blog_body.blog_id WHERE blog.visibility='public' ORDER BY blog_id " . (!isset($_SESSION['user_id']) ? "ASC" : "DESC") . " LIMIT 1;";
                    $r = @mysqli_query($dbc, $q);

                    while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                        $newest_date = $row['published'];
                        $blog_arr = $row;
                    }

                    if (!empty($blog_arr)) {
                        $id = $blog_arr['blog_id'];
                        $title = $blog_arr['title'];
                        $thumbnail = $blog_arr['thumbnail'];
                        $posted_date = $blog_arr['published'];
                        $posted = date('m/d/Y', strtotime($posted_date));
                        $description = mb_strimwidth($blog_arr['body'], 0, 500, '...');
                    }

                    echo '<div class="flex-container">';
                    echo '<div class="recentblog">';
                    echo '<img class="thumbnail" src="' . $thumbnail . '">';
                    echo '<div id="blogdetails">';
                    echo '<h1>' . $title .'</h1>';
                    echo '<p id="blogdate">Posted: ' . $posted . ' by Mike Birrell</p>';
                    echo '<p id="blogdescription">' . $description .'</p>';
                    echo '<button id="readblogbtn1" onclick="window.location=\'read_blog.php?id=' . $id .'\';">Read Blog</button>';
                
                ?>
            </div>
        </div>
    </div>

    <h2>Recent Videos</h2>
    <div class="flex-container">

    <?php 

        $title = 'Sample Video'; // declare the template strings.
        $thumbnail = 'images/plant.png';
        $posted = '2/14/2023';
        $description = 'This section is where the first few pargraphs of the video description will be displayed.';
        $id = '1';

        require_once('mysqli_connect.php'); // Connect to the db
        
        // If the user is logged in get the 2 newest videos, if logged out, get the introductory video.
        $q = "SELECT video.*, video_body.body FROM video INNER JOIN video_body ON video.video_id = video_body.video_id WHERE video.visibility='public' ORDER BY video_id " . (!isset($_SESSION['user_id']) ? "ASC" : "DESC") . " LIMIT " . (!isset($_SESSION['user_id']) ? "1" : "2") . ";";
        $r = @mysqli_query($dbc, $q);

        while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {

            $id = $row['video_id'];
            $title = $row['title'];
            $thumbnail = $row['thumbnail'];
            $posted_date = $row['publish_date'];
            $posted = date('m/d/Y', strtotime($posted_date));
            $description = mb_strimwidth($row['body'], 0, 500, '...');

            echo '<div class="recentblog" style="margin-right: 0px;">';
            echo '<img class="thumbnail" src="' . $thumbnail . '">';
            echo '<div id="blogdetails">';
            echo '<h1>' . $title .'</h1>';
            echo '<p id="blogdate">Posted: ' . $posted . ' by Mike Birrell</p>';
            echo '<p id="blogdescription">' . $description .'</p>';
            echo '<button id="readblogbtn1" onclick="window.location=\'watch_video.php?id=' . $id .'\';">Watch</button>';
            echo '</div>';
            echo '</div>';
        }

        require('includes/login_functions.inc.php');
        auto_login($dbc);

        mysqli_free_result ($r); // Free up the resources.
        mysqli_close($dbc); // close the database connection.

    ?>
    </div>