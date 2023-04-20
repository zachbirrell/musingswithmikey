<?php 

    session_start();

    if (empty($_SESSION['user_id'])) {
        $_SESSION['error_index'] = 2;
        require('includes/login_functions.inc.php');
        redirect_user('error.php');
    }

	$page_title = 'All Videos';
	include('includes/header.html');
?>
<body>
    <link rel="stylesheet" href="css/index.css" type="text/css">
    <link rel="stylesheet" href="css/video.css" type="text/css">
    <h2>All Videos</h2>
            <?php 

                if ($_SESSION['user_level'] == '2') {
                    echo '<button style="margin-top: 7px; margin-bottom: 10px; padding: 7px; cursor: pointer" onclick="window.location=\'manage_video.php?a=new\'"> + Add New Video</button>';
                }

            $title = 'Sample Video'; // declare the template strings.
            $posted = '2/14/2023';
            $description = 'This section is where the first few pargraphs of the video description will be displayed.';
            $id = '1';
            $thumbnail = 'images/plant2.png';
            $counter = 1;

            require_once('mysqli_connect.php'); // Connect to the db

            $q = ($_SESSION['user_level'] == '2') ? ('SELECT video.video_id, video.title, video.publish_date, video.views, video.thumbnail, video.visibility, video_body.body FROM video INNER JOIN video_body ON video.video_id = video_body.video_id ORDER BY video.publish_date DESC;') : ('SELECT video.video_id, video.title, video.publish_date, video.views, video.thumbnail, video.visibility, video_body.body FROM video INNER JOIN video_body ON video.video_id = video_body.video_id WHERE video.visibility="public" ORDER BY video.publish_date DESC;');
            $r = @mysqli_query($dbc, $q);

            // echo '<pre>' . print_r($r) . '</pre>';

            while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                // echo '<pre>' . print_r($row) . '</pre>';
                $id = $row['video_id'];
                $title = $row['title'];
                $thumbnail = $row['thumbnail'];
                $posted_date = $row['publish_date'];
                $posted = date('m/d/Y', strtotime($posted_date));
                $visibility = $row['visibility'];
                $description = mb_strimwidth($row['body'], 0, 500, '...');
                $buttons = $_SESSION["user_level"] == "2" ? '<button id="readblogbtn1" style="margin-left: 5px" onclick="window.location=\'manage_video.php?id=' . $id .'&a=edit\';">Edit</button><button id="readblogbtn1" style="margin-left: 5px" onclick="window.location=\'manage_video.php?id=' . $id .'&a=delete\';">Delete</button>' : "";
                $badge = $_SESSION['user_level'] == '2' ? '<p id="' . $visibility . '-badge" class="badge">' . $visibility . '</p>' : '';
                $html = '                
                    <div class="recentblog" style="margin-left: 7px;">
                        <img class="thumbnail" src="' . $thumbnail . '">
                        <div id="blogdetails">
                            <div style="display: flex;">
                                <h1>' . $title .'</h1>' . $badge . '
                            </div>
                            <p id="blogdate">Posted: ' . $posted . ' by Mike Birrell</p>
                            <p id="blogdescription">' . $description .'</p>
                            <button id="readblogbtn1" onclick="window.location=\'watch_video.php?id=' . $id .'\';">Watch</button>' . $buttons . '
                        </div>
                    </div>';

                if ($counter == 1) {
                    $html = '<div style="display: flex;">' . $html;
                    $counter = $counter + 1;
                } else {
                    if ($counter == 2) {
                        $counter = 1;
                        $html = $html . '</div>';
                    } else {
                        $counter = $counter + 1;
                    }
                }

                echo $html;
                
            }

            // echo '<pre>' . print_r($video_arr) . '</pre>';

            mysqli_free_result ($r); // Free up the resources
            mysqli_close($dbc);

        ?>








            <!-- <div class="recentblog">
                <img class="thumbnail" src="images/plant2.png">
                <div id="blogdetails">
                    <h1>Sample Video</h1>
                    <p id="blogdate">Uploaded: 2/14/2023 by Mike Birrell</p>
                    <p id="blogdescription">
                        This section is where the first few pargraphs of the Video description will be displayed.
                    </p>
                    <button id="readblogbtn1">Watch</button>
                </div>
            </div>
            <div class="recentblog">
                <img class="thumbnail" src="images/plant3.png">
                <div id="blogdetails">
                    <h1>Sample Video</h1>
                    <p id="blogdate">Uploaded: 2/14/2023 by Mike Birrell</p>
                    <p id="blogdescription">
                        This section is where the first few pargraphs of the Video description will be displayed.
                    </p>
                    <button id="readblogbtn1">Watch</button>
                </div>
            </div>
        </div>
        <div style="display: flex;">
            <div class="recentblog">
                <img class="thumbnail" src="images/plant2.png">
                <div id="blogdetails">
                    <h1>Sample Video</h1>
                    <p id="blogdate">Uploaded: 2/14/2023 by Mike Birrell</p>
                    <p id="blogdescription">
                        This section is where the first few pargraphs of the Video description will be displayed.
                    </p>
                    <button id="readblogbtn1">Watch</button>
                </div>
            </div>
            <div class="recentblog">
                <img class="thumbnail" src="images/plant3.png">
                <div id="blogdetails">
                    <h1>Sample Video</h1>
                    <p id="blogdate">Uploaded: 2/14/2023 by Mike Birrell</p>
                    <p id="blogdescription">
                        This section is where the first few pargraphs of the Video description will be displayed.
                    </p>
                    <button id="readblogbtn1">Watch</button>
                </div>
            </div> -->