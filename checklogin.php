<?php
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

include "primerLib.php";

$tbl_name="participants"; // Table name 

function userExists($username, $database) {
    global $tbl_name;
    $sqlquery = "SELECT COUNT(*) as count FROM $tbl_name WHERE Username LIKE \"$username\";";
    $results = $database->query($sqlquery) or die ("Error occured");
    $row = $results->fetch_array();
    if ($row['count'] > 0) {
        return true;
    }
    return false;
}

function passwordAccepted($password, $username, $database) {
    global $tbl_name;
    $sqlquery = "SELECT PasswordHash FROM $tbl_name WHERE Username LIKE \"$username\";";
    $results = $database->query($sqlquery);
    $row = $results->fetch_array();
    $pwhash = $row['PasswordHash'];
    if (crypt($password, $pwhash) == $pwhash) { 
        return true; 
    }
    return false;
}
$myusername = $_POST['myusername'];
$mypassword = $_POST['mypassword'];

if ((userExists($myusername, $db) && passwordAccepted($mypassword, $myusername, $db)) || isset($_SESSION['username'])) {
    $_SESSION['username'] = $myusername;
    header("location:" . $_SERVER['HTTP_REFERER']);
} else {
    include "header.php";
    echo "<H1>Error</H1><H3>Login failed. Please go <a href='javascript:back()'>back</a> and try again.</H3>";
    include "footer_nologin.php";
    session_destroy();
}
ob_end_flush();
?>
