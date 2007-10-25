<?php
$subtitle = "Create Chapter";
include("header.php");
if($_SESSION["user_id"]) {
?>
<h1>Add chapter:</h1>
<form action="browse.php?date=<?=$_GET["date"]?>" method="post">
<input type="hidden" name="add_chapter_comic_id" value="<?=$_GET["comic_id"]?>" />
<table class="databox">
<tr><td>Chapter title:</td><td><input type="text" width="40" name="add_chapter_title" /></td></tr>
<tr><td colspan="2" align="right"><input type="submit" name="add_chapter" value="Add" /></td></tr>
</table>
</form>
<?php
} else {
	echo "<h1>Access denied.</h1>";
}
include("footer.php");
?>

