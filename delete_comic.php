<?php
$subtitle = "Delete Comic";
include("header.php");
if($_SESSION["user_id"]) {
?><h2>Are you sure you want to delete this comic?</h2>
<form action="browse.php?date=<?=$_GET["date"]?>" method="post">
<input type="hidden" name="delete_id" value="<?=$_GET["comic_id"]?>" />
<input type="submit" name="delete" value="Yes" />
<input type="submit" value="No" />
</form>
<?php
} else {
	echo "<h1>Access denied.</h1>";
}
include("footer.php");
?>
