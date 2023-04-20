<?php

   function redirect_user($page = 'index.php') {
    
        $url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
        $url = rtrim($url, '/\\');
        $url .= '/' . $page;
        
        header("Location: $url");
        exit();

    }
        
    function check_login($dbc, $email = '', $pass = '') {

        $errors = [];
    
        if (empty($email)) {
            $errors[] = 'You forgot to enter your email address.';
        } else {

            $pattern = '/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/';

            if (preg_match($pattern, trim($email))) {
                $e = mysqli_real_escape_string($dbc, trim($email));
            } else {
                $errors[] = 'Email is not correctly formatted.';
            }

        }
    
        if (empty($pass)) {
        $errors[] = 'You forgot to enter your password.';
        } else {
        $p = mysqli_real_escape_string ($dbc, trim($pass));
        }
    
        if (empty($errors)) {
    
        $q = "SELECT user_id, username, email, user_level, user_disabled FROM users WHERE email='$e' AND pass=SHA2('$pass', 512) AND active is NULL";
        $r = @mysqli_query($dbc, $q);

        if (mysqli_num_rows($r) == 1) {
    
            $row = mysqli_fetch_array($r,
                MYSQLI_ASSOC);

            if ($row['user_disabled'] == '1') {
                $errors[] = 'Your account has been <span style="color: red;">disabled</span> due to violating our <a href="tos.php">Terms of Service</a>.';
                return [false, $errors];
            }
    
            return [true, $row];
    
        } else {
            $errors[] = 'The email address and password entered do not match those on file, or your account is not activated.';
        }
    
        }
    
        return [false, $errors];
        
    }

    function auto_login($dbc) {

        if (isset($_COOKIE['a'])) {

            $cookie_hash = $_COOKIE['a'];
            $cookie_username = $_COOKIE['z'];
            // echo "<p>$cookie_username</p>";
            // echo "<p>$cookie_hash</p>";

            $q = "SELECT username, salt, created FROM passwdhash WHERE username='$cookie_username'";
            $r = @mysqli_query($dbc, $q);
    
            if (mysqli_num_rows($r) == 1) {

                $row = mysqli_fetch_array($r, MYSQLI_ASSOC);

                if ($row['salt'] == $cookie_hash) {

                    $q = "SELECT user_id, email, user_level FROM users WHERE username='$cookie_username'";
                    $r = @mysqli_query($dbc, $q);

                    if (mysqli_num_rows($r) == 1) {
                        $data = mysqli_fetch_array($r, MYSQLI_ASSOC);

                        $_SESSION['user_id'] = $data['user_id'];
                        $_SESSION['user_level'] = $data['user_level'];
                        $_SESSION['username'] = $cookie_username;
                        $_SESSION['email'] = $data['email'];
                        $_SESSION['error_index'] = 0;
    
    
                        $_SESSION['agent'] = sha1($_SERVER['HTTP_USER_AGENT']);

                        echo "<script>window.onload = function() {if (!window.location.hash) {window.location = window.location + '#loggedin'; window.location.reload();}}</script>";
                        // echo "<p>Logged in</p>";
                    }
                    
                } else {

                    unset($_COOKIE['a']);
                    unset($_COOKIE['z']);
                    setcookie('a', '', time()-3600, '/');
                    setcookie('z', '', time()-3600, '/');
                }
            }
        }



    }