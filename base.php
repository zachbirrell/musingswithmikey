<?php
    $base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https" : "http");
    $base_url .= "://".$_SERVER['HTTP_HOST'];
    $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

    $url =  $base_url . 'M5Competency/';

    echo $base_url;
    echo "<br>";
    echo $url;
    echo "<br>";
    echo $_SERVER['HTTP_HOST'];
?>




