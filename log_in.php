<?php
//display messages if they haven't been displayed yet
if(!is_included("header2.php") {
	if(count($messages)) {
		echo "<ul>\n";
		foreach($messages as $message) {
			echo "<li>$message</li>\n";
		}
		echo "</ul>\n";
	}
}
?>
<h1>Please log in:</h1>
<form action="<?=strip_folder($_SERVER["SCRIPT_NAME"])?>" method="post">
<table class="databox">
<tr><td>Username:</td><td><input type="text" size="20" name="username" /></td></tr>
<tr><td>Password:</td><td><input type="password" size="20" name="password" /></td></tr>
<tr><td colspan="2" align="right"><input type="submit" value="Submit" /></td></tr>
</table>
</form>
