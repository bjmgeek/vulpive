<?php
$subtitle = "Edit Chapter";
include("header.php");
if($_SESSION["user_id"]) {
	$result = mysql_query("SELECT * FROM chapter WHERE chapter_id=".$_GET["chapter_id"]);
	$row = mysql_fetch_array($result);
	$image = $row["path"];
?>
<h1>Edit chapter:</h1>
<form action="browse.php?date=<?=$_GET["date"]?>" method="post">
<input type="hidden" name="chapter_edit_id" value="<?=$_GET["chapter_id"]?>" />
<table class="databox">
<tr><td>Chapter start:</td><td><select name="chapter_comic_id">
<?php
$result = mysql_query("SELECT * FROM comic ORDER BY date ASC");
while($comic = mysql_fetch_array($result)) {
	echo "<option value=\"".$comic["comic_id"]."\" ".($comic["comic_id"]==$row["comic_id"]?"selected=\"selected\"":"").">".$comic["date"].": ".clean_sql_form($comic["title"])."</option>\n";
}
?>
</select></td></tr>
<tr><td>Chapter title:</td><td><input type="text" width="40" name="chapter_title" value="<?=clean_sql_form($row["title"])?>" /></td></tr>
<tr><td colspan="2" align="right"><input type="submit" name="chapter_edit" value="Save" /></td></tr>
</table>
</form>
<?php
} else {
	echo "<h1>Access denied.</h1>";
}
include("footer.php");
?>
