<?php

   $page_title = 'Login/Register';
   include('includes/header.html');
   
  if (isset($errors) && !empty($errors)) {
     echo '<div class="errormsg"><h1>Error!</h1>
     <p class="error">The following error(s) occurred:<br>';
     foreach ($errors as $msg) {
        echo " - $msg<br>\n";
     }
     echo '</p><p>Please try again.</p></div>';
  }
  
  ?>
  <div class="loginregister">

   <div style="display: column;">
      
      <div class="logincontainer">
         <div class="content">
            <h1 class="header">Login</h1>
            <form action="login.php" method="post" novalidate>
               <p>Email Address: </p>
               <input class="input" type="email" name="email" size="20" maxlength="60" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>">
               <p>Password:</p>
               <input class="input" type="password" name="pass" size="20" maxlength="20">

               <button class="submit" type="submit">Submit</button>
            </form>
            <!-- <div style="width: 100%; display: flex; opacity: 50%; margin-bottom: 5px">
               <hr style="width: 50%; height: 0px; margin-top: 17px;">
               <p style="margin-left: 10px; margin-right: 10px;">OR</p>
               <hr style="width: 50%; height: 0px; margin-top: 17px;">
            </div> -->
         </div>
      </div>
      <div class="logincontainer" style="margin-top: 8px;">
      <div class="content">
      <!-- <div style="position: absolute; left: 40%;"> -->
         <h1 class="header">Continue with Third Party</h1>
         <div style="margin-left: 20px; margin-right: 20px; margin-top: 5%;">
            <!-- <h2 style="display: flex; align-items: center; justify-content: center">Login with Social Media</h2> -->
            <!-- <p style="margin-right: 10px">Google:</p> -->
            <div style="display: flex; justify-content: center; align-items: center;">
               <div id="g-signin" style=""></div>
                  <script src="https://accounts.google.com/gsi/client" async defer></script>
                  <script>
                     window.onload = function() {
                           google.accounts.id.initialize({
                              client_id: "641193310893-vs7dev0jvmqk3glnikurvvel16bjlg1q.apps.googleusercontent.com",
                              callback: handleCredentialResponse
                           });

                           google.accounts.id.renderButton(
                              document.getElementById("g-signin"), {
                                 type: 'standard',
                                 theme: 'filled_blue',
                                 text: 'continue_with',
                                 size: 'large',
                                 shape: 'rectangle',
                                 width: '500px',
                                 click_listener: onClickHandler
                              } // customization attributes
                           );

                           google.accounts.id.prompt(); // also display the One Tap dialog

                     }

                     function handleCredentialResponse(response) {
                           var xhttp = new XMLHttpRequest();
                           xhttp.onreadystatechange = function() {
                              if (this.readyState == 4 && this.status == 200) {
                                 if ('success' == this.responseText) {
                                       // redirect to profile page
                                       location.href = '/loggedin.php';
                                 }
                              }
                           };
                           xhttp.open("POST", "save-user.php", true);
                           xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                           xhttp.send("response=" + response.credential);
                     }

                     function onClickHandler() {
                           console.log("Sign in with Google button clicked...")
                     }
                  </script>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="logincontainer">
      <div class="content">
      <!-- <div style="position: absolute; left: 40%;"> -->
         <h1 class="header">Register</h1>
         <form action="register.php" method="post" novalidate>
            <p>Email Address: </p>
            <input class="input" type="email" name="email" size="20" maxlength="60" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>">
            <p>Password:</p>
            <input class="input" type="password" name="pass1" size="20" maxlength="20" value="<?php if (isset($_POST['pass1'])) echo $_POST['pass1']; ?>">
            <p>Confirm Password:</p>
            <input class="input" type="password" name="pass2" size="20" maxlength="20">
            <p>Username:</p>
            <input class="input" type="text" name="username" size="20" maxlength="20" value="<?php if (isset($_POST['username'])) echo $_POST['username']; ?>"><br>
            <p>Birthdate: </p>
            <div id="bday">
               <?php
                  // Make the months array:
                  $months = [1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

                  // Make the months pull-down menu:
                  echo '<div style="padding-right: 5px"><select name="month">';
                  foreach ($months as $key => $value) {
                     echo "<option value=\"$key\">$value</option>\n";
                  }
                  echo '</select></div>';

                  // Make the days pull-down menu:
                  echo '<div style="padding-right: 5px"><select name="day">';
                  for ($day = 1; $day <= 31; $day++) {
                     echo "<option value=\"$day\">$day</option>\n";
                  }
                  echo '</select></div>';

                  // Make the years pull-down menu:
                  echo '<select name="year">';
                  for ($year = 1960; $year <= 2008; $year++) {
                     echo "<option value=\"$year\">$year</option>\n";
                  }
                  echo '</select>';
               ?>
               </div>
            <br><input id="rss" type="checkbox" name="rss" value="<?php if (isset($_POST['rss'])) echo 'True'; ?>" checked="<?php if (isset($_POST['rss'])) echo 'True'; ?>">
            <label for="rss">Email me when there's something new.</label><br>
            
            <button class="submit" type="submit">Create Account</button>
            
            <!-- <p><input class="submit" type="submit" name="submit" value=" Submit "></p> -->
         </form>
      </div>
   </div>
  </div>

  
  <!-- <?php include('includes/footer.html'); ?>  -->