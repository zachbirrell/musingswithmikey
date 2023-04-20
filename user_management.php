<?php # Script 10.4 - view_users.php with pagination
// This script retrieves all the records from the users table
// and paginates the results.

session_start();

$page_title = 'User Management';
include('includes/header.html');
echo '<script type="text/javascript" src="https://code.jquery.com/jquery-1.7.1.min.js"></script>';

// Page header
echo '<h1>User Management</h1>';

if ($_SESSION['user_level'] != '2') {
    require('includes/login_functions.inc.php');
    $_SESSION['error_index'] = 1;
    redirect_user('error.php');
} else {

	require_once('mysqli_connect.php'); // Connect to the db

	#Define the query

    $counter = 0;

	$q = "SELECT username, email, dob AS bday, profile_pic, DATE_FORMAT(registration_date, '%M %d, %Y') AS dr, user_id, user_disabled, user_level FROM users";
	$r = @mysqli_query($dbc, $q);

		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {

            if ($counter == 0) {
                echo '<div style="width: 100%; display: flex; padding: 7px; margin-right: 5px;">';
                $counter++;
            } else {

                if ($counter == 2) {
                    $counter = 0;
                } else {
                    $counter++;
                }
            }

            $disabled_status = ($row['user_disabled'] == '0') ? ('disable') : ('enable');

			echo '
            <div style="border: 1px solid black; width: 33%; padding: 7px; display: flex; margin-right: 5px;">
                <img src="' . $row['profile_pic'] . '" width="50" height="50">
                <div style="display: flex; flex-direction: column; margin-top: 3px;">
                    <h3 id="user-username-' . $row['user_id'] . '">' . $row['username'] . ($row['user_disabled'] == '1' ? ' (<span style="color: orange">Disabled</span>)' : '') . ($row['user_level'] == '2' ? ' (<span style="color: green">Admin</span>)' : '') . '</h3>
                    <div style="display: flex;">
                        <p style="margin-right: 7px;"><a href="profile.php?id=' . $row['user_id'] . '">Profile</a></p>
						<p style="margin-right: 7px;"><a href="javascript:on(' . $row['user_id'] . ', \'' . $disabled_status . '\', \'' . $row['username'] . '\')">' . ucfirst($disabled_status) . ' User</a></p>
                        <p><a href="javascript:on(' . $row['user_id'] . ', \'delete\', \'' . $row['username'] . '\')">Delete User</a></p>
                    </div>
                </div>
            </div>
			';

            if ($counter == 0) {
                echo '</div>';
            }
    
		}

		// echo '</div>'; // Close the table.

        echo '<script>
        function on(id, action, username) {

            var desc = "Deletion will completely wipe the user\'s account from the website, and delete ALL comments made by the user. Proceed with caution.";

            if (action == \'disable\') {
                desc = "Disabling will lock the user\'s account. They will not be able to login, and all their comments will be hidden until the user is re-enabled.";
            }

            if (action == \'enable\') {
                desc = "Enabling will unlock the user\'s account. They wlll be able to login again, and all their comments will be unhidden.";
            }

            if (confirm(`Are you sure you want to ${action} the following user: ${username}?\n\n${desc}`) == true) {
                $.post("manage_user.php", {a: action, id: `${id}`}).done(function(data) {
                    if (data == "Success") {
                        alert(`${username}\'s account has been ${action}d.`);
                        window.location.replace(window.location.pathname);
                    } else if (data == "Access Denied") {
                        alert("Hold up! You just tried to delete another person\'s comment without their permission? That\'s not cool!\n\nPlease be more respectable towards others in your community. 10 points have been removed from your account. If your points fall below 100 you will no longer be able to comment on blogs or videos.\n\nPlease respect others and not try to delete their posts. Thank you!");
                    } else {
                        console.log("Failed to delete comment.");
                    }
                });
            } else {
                console.log("dont delete comment");
            };

        }
    </script>';

		mysqli_free_result ($r); // Free up the resources
		mysqli_close($dbc);
}

include('includes/footer.html');
?>