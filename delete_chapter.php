<?php
$subtitle = "Delete Chapter";
include("header.php");
if($_SESSION["user_id"]) {
?><h2>Are you sure you want to delete this chapter?</h2>
<form action="browse.php?date=<?=$_GET["date"]?>" method="post">
<input type="hidden" name="chapter_delete_id" value="<?=$_GET["chapter_id"]?>" />
<input type="submit" name="chapter_delete" value="Yes" />
<input type="submit" value="No" />
</form>
<?php
} else {
	echo "<h1>Access denied.</h1>";
}
include("footer.php");
?>
