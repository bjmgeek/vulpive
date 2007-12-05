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
<form enctype="multipart/form-data" action="upload.php" method="post">
<input type="hidden" name="edit_id" value="<?=$_GET["comic_id"]?>" />
<input type="hidden" name="browse_date" value="<?=$_GET["date"]?>" />
<input type="hidden" name="overwrite" value="true" />
<input type="hidden" name="naming_scheme" value="<?=$options["naming_scheme"]?>" />
<table class="databox">
<tr><td rowspan="<?=($enable_commentary?4:3)+$options["max_images"]?>"><a href="<?=$image?>"><img border="0" src="<?=$thumb?>" /></a></td>
<td>Comic date:</td>
<td><?=display_date_dropdown("edit_date","date",$row["date"])?></td>
</tr>
<tr><td>Comic title:</td><td><input type="text" width="40" name="edit_title" value="<?=clean_sql_form($row["title"])?>" /></td></tr>
<?php
if($enable_commentary) {
	echo "<tr><td>Commentary:</td><td><textarea name=\"commentary\" rows=\"5\" cols=\"40\">".clean_sql_form($row["commentary"])."</textarea></td></tr>";
}
echo "<tr><th colspan=\"2\" class=\"top_divider\">Change image".($options["max_images"]=="1"?"":"s")."</th></tr>";
if($options["max_images"]=="1") {
	echo "<tr><td>File:</td><td><input type=\"file\" name=\"filename_1\" /></td></tr>\n";
} else {
	for($i = 1; $i <= (int)$options["max_images"]; $i++) {
		echo "<tr><td>".ordinal($i)." file:</td><td><input type=\"file\" name=\"filename_$i\" /></td></tr>\n";
	}
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
