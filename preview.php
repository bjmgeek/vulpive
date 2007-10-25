<?php
$subtitle = "Comic Preview";
include("header.php");
if($_SESSION["user_id"]) {
	$comic_id = $_GET["comic_id"];
	$result = mysql_query("SELECT * FROM comic WHERE comic_id=$comic_id");
	$comic = mysql_fetch_array($result);
	echo "<div class=\"comicpanel\"><img src=\"".$comic["path"]."\" alt=\"".clean_sql_display($comic["title"])."\" />\n";
	//display remaining images of a multi-image comic
	$result = mysql_query("SELECT * FROM multi_comic WHERE comic_id=".$comic["comic_id"]." ORDER BY sort_order ASC");
	while($row = mysql_fetch_array($result)) {
		echo "<br /><img src=\"".$row["path"]."\" alt=\"".clean_sql_display($comic["title"])."\" />\n";
	}
	echo "</div>";
} else {
	echo "<h1>Access denied.</h1>";
}
include("footer.php");
?>
