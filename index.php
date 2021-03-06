<?php
/*
Vulpive Webcomic Manager
Copyright (C) 2007  Alexander Rhodes (pifactorial@gmail.com)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
include_once("includes.php");
if($_GET["logout"]) unset($_SESSION["user_id"]);
if($_SESSION["user_id"]) { //show all comics if, and only if, logged in
	$admin_view = true;
	$condition = "1";
	$condition2 = "1";
	$condition3 = "1";
} else {
	$admin_view = false;
	$condition = "is_visible=1 AND CAST(comic.date AS DATETIME)<NOW()";
	$condition2 = "is_visible=1";
	$condition3 = "CAST(comic.date AS DATETIME)<NOW()";
}
//if there aren't any comics, automatically redirect to upload.php
$result = mysql_query("SELECT * FROM comic WHERE $condition");
if(!mysql_num_rows($result)) {
	header("Location: upload.php");
}

$result = mysql_query("SELECT date FROM comic WHERE $condition2 ORDER BY date ASC LIMIT 1");
$first = array_pop(mysql_fetch_array($result));
$result = mysql_query("SELECT date FROM comic WHERE $condition2 ORDER BY date DESC LIMIT 1");
$last = array_pop(mysql_fetch_array($result));
if(isset($_GET["date"])) {
	//get comic for a date
	$query = "SELECT * FROM comic WHERE $condition AND date<='".$_GET["date"]."' ORDER BY date DESC LIMIT 1";
	$result = mysql_query($query);
	if(!mysql_num_rows($result)) {
		//we've gone back too far
		$query = "SELECT * FROM comic WHERE date='$first' ORDER BY date DESC LIMIT 1";
		$result = mysql_query($query);
	}
} else {
	//get today's comic
	$query = "SELECT * FROM comic WHERE CAST(comic.date AS DATETIME)<NOW() ORDER BY date DESC LIMIT 1";
	$result = mysql_query($query);
}
$comic = mysql_fetch_array($result);
$date = strtotime($comic["date"]);
$year = date("y",$date);
$month = date("m",$date);
$day = date("d",$date);

//draw header
$subtitle = date("M jS, Y",$date);
include("header.php");

//get next/previous comics
$result = mysql_query("SELECT date FROM comic WHERE $condition AND date < '".$comic["date"]."' ORDER BY date DESC LIMIT 1");
if(mysql_num_rows($result)) {
	$prev = array_pop(mysql_fetch_array($result));
} else {
	$prev = false;
}
$result = mysql_query("SELECT date FROM comic WHERE $condition AND date > '".$comic["date"]."' ORDER BY date ASC LIMIT 1");
if(mysql_num_rows($result)) {
	$next = array_pop(mysql_fetch_array($result));
} else {
	$next = false;
}
?>
<table class="bodytable">
<?php
if($admin_view) {  //show "Log out" button if we are logged in
	echo "<tr><td colspan=\"2\" align=\"right\" class=\"divider\"><a href=\"browse.php?date=$date\">Back to Admin</a> | <a href=\"index.php?logout=true\">Log out</a></td></tr>";
}
?>
  <tr>
<?php
//don't display sidebar if we're in "single_archive" mode, and we're not viewing today's comic (or if we're in "single" mode)
if(($options["column_format"] !== "single_archive" || !$next) && $options["column_format"] !== "single") {
	include("sidebar.php");
}
?>
    <td valign="top" align="center">
      <table class="navtable">
<?php
//display links if mode is "single"
if($options["column_format"] == "single") {
	echo "<tr><td colspan=\"5\" class=\"links divider\">\n";
	include("links.php");
	echo "</td></tr>\n";
}

if((strlen(trim($options["shoutout"])))&&($options["column_format"] !== "single_archive" || !$next)) {
	echo "<tr><td colspan=\"5\" class=\"shoutout divider\">\n";
	echo clean_sql_display($options["shoutout"]);
	echo "</td></tr>\n";
}
if($options["show_dropdown"]=="true") {
?>
	<tr>
	  <td align="center" colspan="5" class="divider">
	    <form action="index.php" method="get">
	      <select name="date">
<?php
	if($options["sort_dropdown"] !== "chapter") {
		$query = "SELECT *,IF(LENGTH(title)>0,title,DATE_FORMAT(comic.date,'%M %e, %Y')) AS title FROM comic WHERE $condition ORDER BY ".$options["sort_dropdown"]." ASC";
		$result = mysql_query($query);
		while($row = mysql_fetch_array($result)) {
			echo "<option value=\"".$row["date"]."\" ".($row["date"]==$comic["date"]?"selected=\"selected\"":"").">".clean_sql_form($row["title"])."</option>\n";
		}
	} else {
		//find current chapter
		$result = mysql_query("SELECT date FROM chapter LEFT JOIN comic USING (comic_id) WHERE date <= '".$comic["date"]."' ORDER BY date DESC LIMIT 1");
		if(mysql_num_rows($result)) $my_chapter = array_pop(mysql_fetch_array($result));

		//get chapters
		$query = "SELECT date, chapter.title FROM chapter LEFT JOIN comic USING (comic_id) WHERE $condition3 ORDER BY date ASC";
		$result = mysql_query($query);
		while($row = mysql_fetch_array($result)) {
			echo "<option value=\"".$row["date"]."\" ".($row["date"]==$my_chapter?"selected=\"selected\"":"").">".clean_sql_form($row["title"])."</option>\n";
		}
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
	<tr>
	  <td align="center" colspan="5" class="comic_title">
	    <?=date("F j",$date).(strlen($comic["title"])?": <i>".clean_sql_display($comic["title"])."</i>":"")."\n"?>
	  </td>
	</tr>
	<tr class="nav">
	  <td align="left">
<?php
if($prev) {
	echo "<a href=\"index.php?date=$first\">".FIRST."</a>\n";
} else {
	echo "&nbsp;\n";
}
?>
	  </td>
	  <td align="left">
<?php
if($prev) {
	echo "<a href=\"index.php?date=$prev\">".PREVIOUS."</a>\n";
} else {
	echo "&nbsp;\n";
}
?>
	  </td>
	  <td width="50%">
	  </td>
	  <td align="right">
<?php
if($next) {
	echo "<a href=\"index.php?date=$next\">".NEXT."</a>\n";
} else {
	echo "&nbsp;\n";
}
?>
	  </td>
	  <td align="right">
<?php
if($next) {
	echo "<a href=\"index.php?date=$last\">".LAST."</a>\n";
} else {
	echo "&nbsp;\n";
}
?>
	  </td>
	</tr>
	<tr>
	  <td colspan="5" class="comicpanel">
	    <img src="<?=$comic["path"]?>" alt="<?=clean_sql_display($comic["title"])?>" />
<?php
//display remaining images of a multi-image comic
$result = mysql_query("SELECT * FROM multi_comic WHERE comic_id=".$comic["comic_id"]." ORDER BY sort_order ASC");
while($row = mysql_fetch_array($result)) {
	echo "<br /><img src=\"".$row["path"]."\" alt=\"".clean_sql_display($comic["title"])."\" />\n";
}
?>
	  </td>
	</tr>
	<tr class="nav">
	  <td align="left">
<?php
if($prev) {
	echo "<a href=\"index.php?date=$first\">".FIRST."</a>\n";
} else {
	echo "&nbsp;\n";
}
?>
	  </td>
	  <td align="left">
<?php
if($prev) {
	echo "<a href=\"index.php?date=$prev\">".PREVIOUS."</a>\n";
} else {
	echo "&nbsp;\n";
}
?>
	  </td>
	  <td width="50%">
	  </td>
	  <td align="right">
<?php
if($next) {
	echo "<a href=\"index.php?date=$next\">".NEXT."</a>\n";
} else {
	echo "&nbsp;\n";
}
?>
	  </td>
	  <td align="right">
<?php
if($next) {
	echo "<a href=\"index.php?date=$last\">".LAST."</a>\n";
} else {
	echo "&nbsp;\n";
}
?>
	  </td>
	</tr>
<?php
if($options["enable_commentary"]=="true" && strlen(trim($comic["commentary"]))) {
	echo "<tr><td colspan=\"5\" class=\"commentary top_divider\">\n";
	echo clean_sql_display($comic["commentary"]);
	echo "</td></tr>\n";
}

//display if we're in "single_archive" mode, and we're not viewing today's comic, or if mode is "single"
if(($options["column_format"] == "single_archive" && $next) || $options["column_format"] == "single") {
	echo "<tr><td class=\"calendar_panel top_divider\" colspan=\"5\"><a href=\"calendar.php?date=$year-$month-$day\"><b>Calendar view</b></a></td></tr>\n";
	echo "<tr><td align=\"center\" colspan=\"5\"><table class=\"embedded_calendar\">\n";
	include("mini_calendar.php");
	echo "</td></tr></table>\n";
	echo "</td></tr>\n";
}
?>
      </table>
    </td>
  </tr>
</table>
<?
include("footer.php");
?>
