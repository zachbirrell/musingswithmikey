<?php 

    $video_id = $_GET['id'];
    $action = $_GET['a'];

    echo $action;
    echo $video_id;
    echo "<script>console.log('TEST')</script>";

    if (!empty($action)) {

        echo $action;
        echo $video_id;


    }


?>