<?php

   session_start();

   // check if user hasn't logged in.
   if (empty($_SESSION['user_id'])) {
      $_SESSION['error_index'] = 2; //set the error index.
      require('includes/login_functions.inc.php'); //load the functions from website_functions.php file.
      redirect_user('error.php'); //call the redirect function, and have it redirect to the error page.
  }

  function spam_scrubber($value) {

   // List of very bad values:
   $very_bad = ['to:', 'cc:', 'bcc:', 'content-type:', 'mime-version:', 'multipart-mixed:', 'content-transfer-encoding:', '<script', '</script>', '<?php', '?>', '<iframe'];

   // If any of the very bad strings are in
   // the submitted value, return an empty string:
   foreach ($very_bad as $v) {

       if (stripos($value, $v) !== false) {
           $value = str_replace($v, '', $value);
       }

   }

   // Replace any newline characters with spaces:
   $value = str_replace(["\r", "\n", "%0a", "%0d"], ' ', $value);

   // Return the value:
   return trim($value);

 } // End of spam_scrubber() function.

   $page_title = 'Contact Me';
   include('includes/header.html');

   if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      $errors = [];

      if (isset($_POST['comments']) && !empty($_POST['comments'])) { //ensure the user has supplied their comments. 

         // Call the spam_scrubber function to ensure harmful text is removed from the input.
         $message = spam_scrubber($_POST['comments']);

         // setup the body of the email.
         $body = "Thank you so much for reading out, " . $_SESSION["username"] . "!\n\nWe've got your message and will reach out shortly!\n\nYour message: \n" . $message . "\n\nThis email was sent from a send-only address. Please do not reply to this email.";

         //send the email to the user.
         mail($_SESSION['email'], '[Musings with Mikey] Thank You for Contacting Us!', $body, 'From: admin@' . $_SERVER['HTTP_HOST'] .'.com');

         // inform the user that their message has been recieved.
         echo '<script>alert("Thank you for reaching out! Your message has been successfully sent!\n\nKeep an eye on your email, and we\'ll respond ASAP ;)")</script>';

         // reset the post value.
         $_POST['comments'] = '';

      } else {
         $errors[] = 'Please provide your comments before submitting.'; // the user didn't specify their comments.
      }


      if (isset($errors) && !empty($errors)) { // check if an error occured during the post process.
         echo '<div style="border: 1px solid black; padding: 10px;"><h1>Error!</h1>
         <p class="error">The following error(s) occurred:<br>';
         foreach ($errors as $msg) { // loop through the errors array, and display the error message.
            echo " - $msg<br>\n";
         }
         echo '</p><p>Please try again.</p></div>';
      }
   
   }
   
  
  ?>
  <div class="loginregister">
   <div class="logincontainer">
      <div class="content">
         <h1 class="header">Contact Me</h1>
         <form class="commentsform" action="contact.php" method="post">
            <textarea class="comments" name="comments" rows="5" cols="20" placeholder="What's on your mind?"><?php if
            (isset($_POST['comments'])) echo $_POST['comments']; ?></textarea>
            <button class="submitcomment" type="submit">Submit</button>
        </form>
      </div>
   </div>
  </div>

