<?php
$subtitle = "Browse";
include("header.php");
//is commentary enabled?
if($options["enable_commentary"]=="true") $enable_commentary = true;

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
} elseif($_SESSION["user_id"] && $_GET["hide"]) {
	mysql_query("UPDATE comic SET is_visible = is_visible ^ 1 WHERE comic_id = ".$_GET["hide"]);
} elseif($_SESSION["user_id"] && $_POST["delete"]) {
	$result = mysql_query("SELECT * FROM comic WHERE comic_id = ".$_POST["delete_id"]);
	$comic = mysql_fetch_array($result);
	$path = $comic["path"];
	unlink($path);
	//delete thumbnail
	if(file_exists($thumb=filename_to_thumb($path,false))) unlink($thumb);
	mysql_query("DELETE FROM comic WHERE comic_id = ".$_POST["delete_id"]);
	//delete extra images
	$result = mysql_query("SELECT path FROM multi_comic WHERE comic_id = ".$_POST["delete_id"]);
	while($row = mysql_fetch_array($result)) {
		unlink($row["path"]);
	}
	mysql_query("DELETE FROM multi_comic WHERE comic_id = ".$_POST["delete_id"]);
	$messages[] = "Comic deleted.";

	//Move the chapter to the next comic if possible.  If not, delete it.
	$result = mysql_query("SELECT comic_id FROM comic WHERE date > '".$comic["date"]."' ORDER BY date ASC LIMIT 1");
	if(mysql_num_rows($result)) {
		$next_comic = array_pop(mysql_fetch_array($result));
		$result = mysql_query("SELECT * FROM chapter WHERE comic_id=$next_comic");
		if(mysql_num_rows($result)) {
			$abort = true;
		} else {
			//move it
			mysql_query("UPDATE chapter SET comic_id=$next_comic WHERE comic_id=".$_POST["delete_id"]);
			if(mysql_affected_rows()) $messages[] = "Chapter moved to next comic.";
		}
	} else {
		$abort = true;
	}
	if($abort) {
		//delete it
		mysql_query("DELETE FROM chapter WHERE comic_id = ".$_POST["delete_id"]);
		if(mysql_affected_rows()) $messages[] = "Chapter deleted.";
	}
} elseif($_SESSION["user_id"] && $_POST["chapter_edit"]) {
	$query = "UPDATE chapter SET comic_id=".$_POST["chapter_comic_id"].", title='".clean_form_sql($_POST["chapter_title"])."' WHERE chapter_id=".$_POST["chapter_edit_id"];
	mysql_query($query);
	if(mysql_affected_rows()) $messages[] = "Chapter changed.";
} elseif($_SESSION["user_id"] && $_POST["chapter_delete"]) {
	mysql_query("DELETE FROM chapter WHERE chapter_id=".$_POST["chapter_delete_id"]);
	$messages[] = "Chapter deleted.";
} elseif($_SESSION["user_id"] && $_POST["add_chapter"]) {
	if(strlen(trim($_POST["add_chapter_title"]))) {
		$query = "INSERT INTO chapter (title, comic_id) VALUES ('".clean_form_sql($_POST["add_chapter_title"])."',".$_POST["add_chapter_comic_id"].")";
		mysql_query($query);
		$messages[] = "Chapter created.";
	} else {
		$messages[] = "Title omitted, chapter not created.";
	}
}
if(!$_SESSION["user_id"]) {
	//not logged in
	include("log_in.php");
	include("footer.php");
	exit();
}
//browsing window
include("header2.php");
if($_GET["date"]) {
	$date = array_to_date($_GET["date"],"unix");
} else {
	$date = time();
}
$year = date("Y",$date);
$month = date("m",$date);
$day = date("d",$date);
$back = strtotime("-1 month",$date);
$forward = strtotime("+1 month",$date);
$result = mysql_query("SELECT date FROM comic ORDER BY date ASC LIMIT 1");
if(mysql_num_rows($result)) $first = strtotime(array_pop(mysql_fetch_array($result)));
$result = mysql_query("SELECT date FROM comic ORDER BY date DESC LIMIT 1");
if(mysql_num_rows($result)) $last = strtotime(array_pop(mysql_fetch_array($result)));
?>
<table class="calendar_frame" width="100%">
<tr><th colspan="5"><?=date("F, Y",$date)?></th></tr>
<tr><td colspan="5" width="100%">
<table class="calendar big_calendar">
<tr><th>Sunday</th><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th><th>Saturday</th></tr>
<tr>
<?php
//calendar
$start_day = date("w",strtotime("$year-$month-1"));
$num_days = date("t",$date);
for($x = 0; $x < $start_day; $x++) {
	echo "<td class=\"off\">&nbsp;</td>";
}
for($x = $start_day; $x < $num_days + $start_day; $x++) {
	if($x % 7 == 0) echo "</tr><tr>";
	$my_day = $x - $start_day + 1;
	echo "<td valign=\"top\" ".("$year-$month-$my_day"==date("Y-m-d")?"class=\"today\"":"class=\"normal_day\"").">";
	echo "<a href=\"upload.php?date[year]=$year&amp;date[month]=$month&amp;date[day]=$my_day\"><b>$my_day</b></a><br />\n";
	$query = "SELECT * FROM comic WHERE date = '$year-$month-$my_day'";
	$result = mysql_query($query);
	if(mysql_num_rows($result)) {
		$row = mysql_fetch_array($result);
		echo "<small>".$row["title"]."</small>\n";
		echo "<table><tr><td valign=\"top\"><a href=\"index.php?date=$year-$month-$my_day\"><img border=\"0\" src=\"".filename_to_thumb($row["path"])."\" /></a></td>\n";
?>
<td valign="top">
<a href="edit_comic.php?date=<?=$date?>&amp;comic_id=<?=$row["comic_id"]?>">Edit&nbsp;comic</a><br />
<a href="browse.php?date=<?=$date?>&amp;hide=<?=$row["comic_id"]?>"><?=($row["is_visible"]?"Hide":"Show")?>&nbsp;comic</a><br />
<a href="delete_comic.php?date=<?=$date?>&amp;comic_id=<?=$row["comic_id"]?>">Delete&nbsp;comic</a><br /><br />
<?php
$result = mysql_query("SELECT chapter.title, chapter_id FROM chapter INNER JOIN comic USING (comic_id) WHERE comic.comic_id=".$row["comic_id"]);
if(mysql_num_rows($result)) {
	//this comic is a chapter start
	$chapter = mysql_fetch_array($result);
	echo "Chapter: <i>".$chapter["title"]."</i><br />\n";
	echo "<a href=\"edit_chapter.php?date=$date&amp;chapter_id=".$chapter["chapter_id"]."\">Edit&nbsp;chapter</a><br />\n";
	echo "<a href=\"delete_chapter.php?date=$date&amp;chapter_id=".$chapter["chapter_id"]."\">Delete&nbsp;chapter</a><br />\n";
} else {
	echo "<a href=\"create_chapter.php?date=$date&amp;comic_id=".$row["comic_id"]."\">New&nbsp;chapter</a><br />\n";
}
?>
</td>
</tr></table>
<?php
	}
	echo "</td>";
}
while($x % 7) {
	echo "<td class=\"off\">&nbsp;</td>";
	$x++;
}
?>
</tr></table>
</td></tr>
<tr>
<td width="20%" align="left" valign="top"><a href="browse.php?date=<?=$first?>"><?=FIRST?></a></td>
<td width="20%" align="left" valign="top"><a href="browse.php?date=<?=$back?>"><?=MONTH_BACK?></a></td>
<td width="20%" align="center" valign="top"><form action="browse.php" method="get"><?=display_date_dropdown("date","month",date("Y-m-d",$date))?> <input type="submit" value="Go" /></form></td>
<td width="20%" align="right" valign="top"><a href="browse.php?date=<?=$forward?>"><?=MONTH_FORWARD?></a></td>
<td width="20%" align="right" valign="top"><a href="browse.php?date=<?=$last?>"><?=LAST?></a></td></tr>
<?php
if(mysql_num_rows(mysql_query("SELECT * FROM chapter"))) {
?>
<tr>
<td colspan="5" align="center" class="top_divider">
<form action="browse.php" method="get">
Jump to chapter:<br /> <select name="date">
<?php
//find current chapter
$result = mysql_query("SELECT date FROM chapter LEFT JOIN comic USING (comic_id) WHERE date <= '$year-$month-31' ORDER BY date DESC LIMIT 1");
if(mysql_num_rows($result)) $my_chapter = array_pop(mysql_fetch_array($result));

//get chapters
$query = "SELECT date, chapter.title FROM chapter LEFT JOIN comic USING (comic_id) ORDER BY date ASC";
$result = mysql_query($query);
while($row = mysql_fetch_array($result)) {
	echo "<option value=\"".strtotime($row["date"])."\" ".($row["date"]==$my_chapter?"selected=\"selected\"":"").">".clean_sql_form($row["title"])."</option>\n";
}
?>
</select>
<input type="submit" value="Go" />
</form>
</td>
</tr>
<?php
}
?>
</table>
<?
include("footer2.php");
include("footer.php");
?>
