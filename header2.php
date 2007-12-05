<table class="bodytable">
<tr><td colspan="2" align="right" class="divider"><a href="index.php?logout=true">Log out</a></td></tr>
<tr><td class="admin_sidebar">
<a href="index.php">Main&nbsp;page</a><br /><br />
<a href="upload.php">Upload&nbsp;comics</a><br />
<a href="browse.php">Browse/edit&nbsp;comics</a><br />
<a href="options.php">Change&nbsp;settings</a><br />
<a href="password.php">Change&nbsp;username/password</a>
</td>
<td valign="top">
<?php
if($_REQUEST["message"]) $messages[] = $_REQUEST["message"];
if(count($_SESSION["messages"])) {
	$messages = array_merge($_SESSION["messages"],(array)$messages);
	unset($_SESSION["messages"]);
}
if(count($_REQUEST["messages"])) $messages = array_merge($_REQUEST["messages"],(array)$messages);
if(count($messages)) {
	echo "<ul>\n";
	foreach($messages as $message) {
		echo "<li>$message</li>\n";
	}
	echo "</ul>\n";
}
?>
