<?php 
/*
    Copyright 2014 Po-Hao Chen.
    This file is part of PRIMER - Platform for Rapid IMaging Education in Radiology

    PRIMER is free software: you can redistribute it and/or modify
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

if (!isset($_GET['moduleid']))  {
    header("location:/centaur/");
}

$moduleID = $_GET['moduleid'];
$quizDir = getDirFromID($moduleID);
$quiz = getQuizFromDir($quizDir);
include "header.php";

?> 
<div class='questionContainer'>

<?php echo $quiz->getCover() ?>
<hr>
<?php 
if (!isset($_SESSION['username']) || (isset($_SESSION['username']) && $_SESSION['username'] == "guest") || (isset($_SESSION['username']) && $_SESSION['username'] == "notrack")) {
echo <<< END
I consent to provide anonymous data to improve web-based teaching of radiology and would like to <a class=button href="/centaur/">Log In</a>
<P>
Alternatively, I consent to provide anonymous data to improve web-based teaching of radiology and would like to create an account:
<P>
END;
?>
<?php include "loginform.php";?>
<?php
echo <<< END
<BR><BR>
<a href="guestlogin.php?moduleid=$moduleID">I consent as guest</A> to provide anonymous data for research but would like to proceed using a guest account.
END;
}

else {
    echo <<< END
    <A HREF="module.php?moduleid=$moduleID">I consent to provide anonymous data to improve web-based teaching of radiology and would like to proceed.</A><BR>
END;
}


?>
<BR><BR>
<font size=-1>
<a href="notracklogin.php?moduleid=<?php echo $quiz->getDivertModule(); ?>">
I do not consent to this research; show me the module but please do not track my data.</a>

</font>

</P>
</div>
<?php include "footer_nologin.php" ?>
