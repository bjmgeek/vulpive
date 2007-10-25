<?php
$subtitle = "Edit Comic";
include("header.php");
//is commentary enabled?
if($options["enable_commentary"]=="true") $enable_commentary = true;

if($_SESSION["user_id"]) {
	$result = mysql_query("SELECT * FROM comic WHERE comic_id=".$_GET["comic_id"]);
	$row = mysql_fetch_array($result);
	$image = $row["path"];
	$thumb = filename_to_thumb($image);
?>
<h1>Edit comic:</h1>
<form action="browse.php?date=<?=$_GET["date"]?>" method="post">
<input type="hidden" name="edit_id" value="<?=$_GET["comic_id"]?>" />
<table class="databox">
<tr><td rowspan="3"><a href="<?=$image?>"><img border="0" src="<?=$thumb?>" /></a></td>
<td>Comic date:</td>
<td><?=display_date_dropdown("edit_date","date",$row["date"])?></td>
</tr>
<tr><td>Comic title:</td><td><input type="text" width="40" name="edit_title" value="<?=clean_sql_form($row["title"])?>" /></td></tr>
<?php
if($enable_commentary) {
	echo "<tr><td>Commentary:</td><td><textarea name=\"commentary\" rows=\"5\" cols=\"40\">".clean_sql_form($row["commentary"])."</textarea></td></tr>";
}
?>
<tr><td colspan="3" align="right"><input type="submit" name="edit" value="Save" /></td></tr>
</table>
</form>
<?php
} else {
	echo "<h1>Access denied.</h1>";
}
include("footer.php");
?>
