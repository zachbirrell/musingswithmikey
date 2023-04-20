<?php

    $src = $_FILES['a-thumbnail']['tmp_name'];
    $targ = "images/" . $_FILES['a-thumbnail']['name'];
    move_uploaded_file($src, $targ);