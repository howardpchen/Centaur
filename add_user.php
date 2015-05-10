<?php
include "primerLib.php";
include "header.php";
/*
    Copyright 2014 Po-Hao Chen.
    This file is part of Centaur.

    Centaur is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

//$db = new mysqli('localhost', 'chenp', '6qvQ6drD572x3hut','primer');

$tbl_name="participants"; // Table name 
$salt = openssl_random_pseudo_bytes(22);
$salt = '$2x$%13$' . strtr($salt, array('_' => '.', '~' => '/'));


$myusername = $_POST['myusername'];
$mypassword = $_POST['mypassword'];
$mytraining = $_POST['training'];
$myemail = $_POST['emailaddress'];

$mypasswordhash = crypt($mypassword, $salt);

// Check if all the fields are completed.

$error = '';

if ($myusername == '')  {
	$error .= "The username cannot be blank.<br>";
} if ($mypassword == '')  {
	$error .= "The password cannot be blank.<br>";
} if ($mypassword == '')  {
	$error .= "The password cannot be blank.<br>";
} if ($_POST['mypassword'] != $_POST['mypasswordconfirm']) {
    $error .= "The two repeated password entries must match.<br>";
} if ($myemail == '') {
    $error .= "The email address cannot be blank.<br>";
}

if (!($error == ''))  {
	echo "An error occurred in the user creation process.  Please go back and try again.  See specific messages as below. <p> $error";
	include "footer.php";
	exit();
}


// Check to see whether the account exists.

$sqlquery = "SELECT COUNT(*) as count FROM $tbl_name WHERE Username LIKE \"$myusername\"";

$results = $db->query($sqlquery);

$row = $results->fetch_array();
if ($row['count'] > 0) {    // username exists
    echo "This username is in use.  Please try again.";
    // Have to manage duplicate usernames.
    exit();
} else if ($row['count'] == 0) {        // username doesn't exist.
    // add user
    $execstring = "INSERT INTO $tbl_name (Username, PasswordHash, TrainingType, Email) VALUES (\"$myusername\", \"$mypasswordhash\", \"$mytraining\", \"$myemail\");";
    $success = $db->query($execstring) or die (mysqli_error($db));
    if ($success) {
        $_SESSION['username'] = $myusername;
        echo "<br><br><center>User created successfully.";
        if  (isset($_POST['moduleid'])) {
            $moduleID = $_POST['moduleid'];
            echo "<P><A class='button' href='module.php?moduleid=$moduleID'>Continue</A><BR><BR><BR><BR>";
        }
		else  {
			echo "<P><a href='index.php'>Return Home</a>";
		}
        echo "</center>";
    } else {
        echo "Error has occurred.";
    }
}
// Select Database.

include "footer.php";
?>
