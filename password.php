<?php
$subtitle = "Change Username and Password";
include("header.php");
if($_POST["username"]) {
	//log in
	$query = "SELECT user_id FROM user WHERE name='".clean_form_sql($_POST["username"])."' AND password=PASSWORD('".$_POST["password"]."')";
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result)) {
		$_SESSION["user_id"] = $row["user_id"];
	}
	if(!$_SESSION["user_id"]) {
		echo "User not found.";
	}
} elseif($_SESSION["user_id"] && $_POST["submit"]) {
	//let's do this thing
	//check of old password is correct
	$result = mysql_query("SELECT name FROM user WHERE user_id=".$_SESSION["user_id"]." AND password=PASSWORD('".$_POST["current_password"]."')");
	if(!mysql_num_rows($result)) {
		$messages[] = "Old password is incorrect!";
		$abort = true;
	}
	//check if passwords match
	if($_POST["new_password"] != $_POST["new_password2"]) {
		$messages[] = "Passwords do not match!";
		$abort = true;
	}
	if(!$abort) {
		//everything looks fine
		$query = "UPDATE user SET name='".clean_form_sql($_POST["new_username"])."', password=PASSWORD('".clean_form_sql($_POST["new_password"])."') WHERE user_id=".$_SESSION["user_id"];
		//point of no return!
		mysql_query($query);
		$messages[] = "New username and password will apply on next login.";
	}
}
if(!$_SESSION["user_id"]) {
	//not logged in
	include("log_in.php");
	include("footer.php");
	exit();
}

//change username/password
include("header2.php");
$result = mysql_query("SELECT name FROM user WHERE user_id=".$_SESSION["user_id"]);
$name = array_pop(mysql_fetch_array($result));
?>
<h1>Change username/password:</h1>
<form action="password.php" method="POST">
<table class="databox">
<tr><td>Current password:</td><td><input type="password" size="20" name="current_password" /></td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td>New username:</td><td><input type="text" size="20" name="new_username" value="<?=$name?>" /></td></tr>
<tr><td>New password:</td><td><input type="password" size="20" name="new_password" /></td></tr>
<tr><td>New password again:</td><td><input type="password" size="20" name="new_password2" /></td></tr>
<tr><td colspan="2" align="right"><input type="submit" name="submit" value="Click if you're sure" /></td></tr>
</table>
</form>
<?
include("footer2.php");
include("footer.php");
?>
