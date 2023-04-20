<?php 

    session_start();

    if (empty($_SESSION['user_id'])) {
        $_SESSION['error_index'] = 2;
        require('includes/login_functions.inc.php');
        redirect_user('error.php');
    }

	$page_title = 'All Blogs';
	include('includes/header.html');
?>
<body>
    <link rel="stylesheet" href="css/index.css" type="text/css">
    <link rel="stylesheet" href="css/blog.css" type="text/css">
    <h2>All Blogs</h2>
    
        <?php 

            if ($_SESSION['user_level'] == '2') {
                echo '<button style="margin-top: 7px; margin-bottom: 10px; padding: 7px; cursor: pointer" onclick="window.location=\'manage_blog.php?a=new\'"> + Add New Blog</button>';
            }

            $title = 'Sample Blog'; // declare the template strings.
            $posted = '2/14/2023';
            $description = 'This section is where the first few pargraphs of the blog description will be displayed.';
            $id = '1';
            $thumbnail = 'images/plant2.png';
            $counter = 1;

            require_once('mysqli_connect.php'); // Connect to the db

            // $q = "SELECT blog.blog_id, blog.title, blog.thumbnail, blog.published, blog.views, blog_body.body FROM blog INNER JOIN blog_body ON blog.blog_id = blog_body.blog_id ORDER BY blog.published DESC;";
            $q = ($_SESSION['user_level'] == '2') ? ('SELECT blog.blog_id, blog.title, blog.published, blog.views, blog.thumbnail, blog.visibility, blog_body.body FROM blog INNER JOIN blog_body ON blog.blog_id = blog_body.blog_id ORDER BY blog.published DESC;') : ('SELECT blog.blog_id, blog.title, blog.published, blog.views, blog.thumbnail, blog.visibility, blog_body.body FROM blog INNER JOIN blog_body ON blog.blog_id = blog_body.blog_id WHERE blog.visibility="public" ORDER BY blog.published DESC;');
            $r = @mysqli_query($dbc, $q);

            // echo '<pre>' . print_r($r) . '</pre>';

            while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                // echo '<pre>' . print_r($row) . '</pre>';
                $id = $row['blog_id'];
                $title = $row['title'];
                $thumbnail = $row['thumbnail'];
                $posted_date = $row['published'];
                $posted = date('m/d/Y', strtotime($posted_date));
                $description = mb_strimwidth($row['body'], 0, 500, '...');
                $visibility = $row['visibility'];
                $buttons = $_SESSION["user_level"] == "2" ? '<button id="readblogbtn1" style="margin-left: 5px" onclick="window.location=\'manage_blog.php?id=' . $id .'&a=edit\';">Edit</button><button id="readblogbtn1" style="margin-left: 5px" onclick="window.location=\'manage_blog.php?id=' . $id .'&a=delete\';">Delete</button>' : "";
                $badge = $_SESSION['user_level'] == '2' ? '<p id="' . $visibility . '-badge" class="badge">' . $visibility . '</p>' : '';
                $html = '                
                    <div class="recentblog" style="margin-right: 5px">
                        <img class="thumbnail" src="' . $thumbnail . '">
                        <div id="blogdetails">
                            <div style="display: flex;">
                                <h1>' . $title .'</h1>' . $badge . '
                            </div>
                            <p id="blogdate">Posted: ' . $posted . ' by Mike Birrell</p>
                            <p id="blogdescription">' . $description .'</p>
                            <button id="readblogbtn1" onclick="window.location=\'read_blog.php?id=' . $id .'\';">Read</button>' . $buttons . '
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



        <!-- <div style="display: flex;">
            <div class="recentblog">
                <img class="thumbnail" src="images/plant3.png">
                <div id="blogdetails">
                    <h1>Sample Blog</h1>
                    <p id="blogdate">Posted: 2/14/2023 by Mike Birrell</p>
                    <p id="blogdescription">
                        This section is where the first few pargraphs of the Blog description will be displayed.
                    </p>
                    <button id="readblogbtn1">Read</button>
                </div>
            </div>
            <div class="recentblog">
                <img class="thumbnail" src="images/plant4.png">
                <div id="blogdetails">
                    <h1>Sample Blog</h1>
                    <p id="blogdate">Posted: 2/14/2023 by Mike Birrell</p>
                    <p id="blogdescription">
                        This section is where the first few pargraphs of the Blog description will be displayed.
                    </p>
                    <button id="readblogbtn1">Read</button>
                </div>
            </div>
        </div>
        <div style="display: flex;">
            <div class="recentblog">
                <img class="thumbnail" src="images/plant.png">
                <div id="blogdetails">
                    <h1>Sample Blog</h1>
                    <p id="blogdate">Posted: 2/14/2023 by Mike Birrell</p>
                    <p id="blogdescription">
                        This section is where the first few pargraphs of the Blog description will be displayed.
                    </p>
                    <button id="readblogbtn1">Read</button>
                </div>
            </div>
            <div class="recentblog">
                <img class="thumbnail" src="images/plant2.png">
                <div id="blogdetails">
                    <h1>Sample Blog</h1>
                    <p id="blogdate">Posted: 2/14/2023 by Mike Birrell</p>
                    <p id="blogdescription">
                        This section is where the first few pargraphs of the Blog description will be displayed.
                    </p>
                    <button id="readblogbtn1">Read</button>
                </div>
            </div>
        </div> -->