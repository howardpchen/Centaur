<?php include "primerLib.php"; ?>
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

<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
  <link rel="stylesheet" type="text/css" href="/centaur/style_768.css" />
  <title>Centaur - Rapid learning platform</title>
</head>

<?php include "header.php"; 
if (isset($_SESSION['username']) && ($_SESSION['username'] == 'notrack' || $_SESSION['username'] == "guest"))  {
    unset($_SESSION['username']);
}
    
?>


<br>
<br>

<center><table>
<tr><td colspan=3><H2 align=center>Available Modules</H2></tr>

<!-- 

The eventual goal is to automatically display a list of available modules
automatically using the database.  However, for now it suffices just
hardcoding that stuff in...

-->

<tr>

<td width=33%><A HREF="cover.php?moduleid=1101"><img src="moduleImg/1101.png"></A>
<td width=33%>&nbsp; </tr>
<td width=33%>&nbsp; </tr>
<tr>
<td colspan=3>
</tr>
</form>

</table>
</center>
<?php include "footer.php"; ?>


		

