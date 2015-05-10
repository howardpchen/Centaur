<?php include "primerLib.php"?>
<!--
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
-->

<?php

// This script both grades and display the results, as well as save data in MySQL if appropriate.

$score = array();
$scoreMax = array();

$username = $_SESSION['username'];
$moduleID = $_POST['moduleid'];
$quizDir = getDirFromID($moduleID);
$quiz = getQuizFromDir($quizDir);
$attemptID = $_POST['attemptID'];    //Use Time for a unique attempt ID.

$sql = "";
$questions = $quiz->getQuestions();

$questionID = -1; 
$questionText = "";
$responseID = -1;
$responseText = "";
$point = 0;
$tag = "";
$notes = "";

//if ($username != "notrack") $success = $db->query("DELETE FROM responses WHERE AttemptID=$attemptID") or die (mysqli_error($db));

foreach ($_POST as $key=>$val)  {
    $keyArray = explode("_", $key);
    $notes = "";
    if ($keyArray[0] == "actualQID")  {
        $questionID = intval($keyArray[1]);
        $responseID = intval($keyArray[3]);

        $questionText = $db->escape_string($questions[$questionID]->getQuestionText());
        foreach ($questions[$questionID]->getImages() as $img)  {
            if (sizeof($img) == 0) continue;
            else if (sizeof($img) == 1) $notes .= "Image: $img; ";
            else  {
                foreach ($img as $i)  {
                       $notes .= "Image: $i; "; 
                }
            }
        }
		
        $tag = array_slice($keyArray, 4);
        $tag = join("_", $tag);
        $responseText = $db->escape_string($val);
        $point = floatval($questions[$questionID]->getPointValueForAnswer($val, $tag));
       	$uniqueID = hash('md5', $username . $attemptID . $moduleID . $responseID . $tag. $notes);
        if (isset($score[$tag]))  $score[$tag] += $point;
        else $score[$tag] = $point;
        $scoreMax[$tag] += floatval($questions[$questionID]->getMaxPossiblePoint($tag));
        $sql = "REPLACE INTO responses (UniqueID, Username, GameID, AttemptID, QuestionID, QuestionText, Tag, ResponseID, ResponseText, PointValue, Notes) VALUES ('$uniqueID', '$username', $moduleID, $attemptID, $questionID, '$questionText', '$tag', $responseID, '$responseText', $point, '$notes');\n"; 
        if ($username != "notrack") {
			$success = $db->query($sql) or die (mysqli_error($db));
		}
    }
    else if ($key != "moduleid" && $key != "attemptID") {
        $notes = "$key => $val";
		$uniqueID = hash('md5', $attemptID . $notes);
        $sql = "REPLACE INTO responses (UniqueID, Username, GameID, AttemptID, Notes) VALUES ('$uniqueID', '$username', $moduleID, $attemptID, '$notes');";
        if ($username != "notrack") $success = $db->query($sql) or die (mysqli_error($db));
    }
}

?>

<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
  <link rel="stylesheet" type="text/css" href="/centaur/style.css" />
  <title>Thank You</title>
</head>
<?php include "header.php"; ?>
<H2>Summary of Your Results</H2>
<table cellpadding=10pt border=0 align=center>
<?php 
foreach ($score as $tag=>$scr)  {
    $ttl = str_replace("_", " ", $tag);
    if ($ttl == "default") continue;
    echo <<< END
    <tr><td valign=middle> <b>$ttl</b><td valign=middle>$scr<td valign=middle>out of maximum possible of $scoreMax[$tag]</tr>
END;
}
if ($_SESSION['username'] == 'notrack')  {
    echo "<tr><td colspan=3><center>Based on your preference, no information has been saved for this session.</center></tr>";
} else  {
    echo "<tr><td colspan=3><center>Thank you again for participating.</center></tr>";
}
?>
</table>
<BR>
<center><A class="button" HREF="/centaur/">Home</A></center>
<?php 

include "footer.php"; 

?>
