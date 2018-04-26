<?php
if (!isset($moduleID)) {
	$moduleID='';
}
echo <<< END
<table width="500" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#444444">
<tr>
<form name="form1" method="post" action="add_user.php">
<input name="moduleid" type="hidden" value="$moduleID">
<td>
<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#000000">
<tr>
<td colspan="3"><strong>Create Account</strong></td>
</tr>
<tr>
<td width="250">Username</td>
<td>&nbsp;</td>
<td><input name="myusername" type="text" id="myusername"></td>
</tr>
<tr>
<td>Password</td>
<td>&nbsp;</td>
<td><input name="mypassword" type="password" id="mypassword"></td>
</tr>
<tr>
<td>Confirm Password</td>
<td>&nbsp;</td>
<td><input name="mypasswordconfirm" type="password" id="mypasswordconfirm"></td>
</tr>

<tr>
<td>Your Training</td>
<td>&nbsp;</td>
<td><select name="training" id="training">
<option value="">--Select one--
<option value="No Medical Training">No Medical Training
<option value="Medical Student">Medical Student
<option value="Non-Radiology MD">Non-Radiology MD
<option value="Radiology R1">Radiology R1
<option value="Radiology R2">Radiology R2
<option value="Radiology R3">Radiology R3
<option value="Radiology R4">Radiology R4
<option value="Fellow, Radiology">Fellow, Radiology
<option value="Attending, Radiology">Attending, Radiology
</select>
</tr>

<tr>
<td>Your Email Address</td>
<td>&nbsp;</td>
<td><input type="text" name="emailaddress" id="emailaddress"><br>
</tr>
<tr><td colspan=3>Your email address is only used to reset your password if you forget.</tr>
<tr>
<td colspan=3 align=center><input type="Submit" value="Submit">
</tr>
</table>
</td>
</form>
</tr>
</table>
END;
?>
