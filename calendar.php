<?php
include_once("includes.php");
if(isset($_GET["date"])) {
	$date = strtotime(array_to_date($_GET["date"]));
} else {
	$date = time();
}

//draw header
$subtitle = "Calendar - ".date("M, Y",$date);
include("header.php");

$year = date("Y",$date);
$month = date("m",$date);
$day = date("d",$date);
$concat = date("Ym",$date);
//find most recent comic that is before this one and not in the same month
$result = mysql_query("SELECT date FROM comic WHERE is_visible=1 AND EXTRACT(YEAR_MONTH FROM date) < $concat AND CAST(comic.date AS DATETIME)<NOW() ORDER BY date DESC LIMIT 1");
if(mysql_num_rows($result)) {
	$back = array_pop(mysql_fetch_array($result));
} else {
	$back = false;
}
//uh, the same thing in reverse
$result = mysql_query("SELECT date FROM comic WHERE is_visible=1 AND EXTRACT(YEAR_MONTH FROM date) > $concat AND CAST(comic.date AS DATETIME)<NOW() ORDER BY date ASC LIMIT 1");
if(mysql_num_rows($result)) {
	$forward = array_pop(mysql_fetch_array($result));
} else {
	$forward = false;
}
$result = mysql_query("SELECT date FROM comic WHERE is_visible=1 AND CAST(comic.date AS DATETIME)<NOW() ORDER BY date ASC LIMIT 1");
$first = array_pop(mysql_fetch_array($result));
$result = mysql_query("SELECT date FROM comic WHERE is_visible=1 AND CAST(comic.date AS DATETIME)<NOW() ORDER BY date DESC LIMIT 1");
$last = array_pop(mysql_fetch_array($result));
?>
<table class="bodytable">
  <tr>
<?php
if($options["column_format"] !== "single") {
	include("sidebar.php");
}
?>
    <td valign="top" align="center">
<table>
<?php
//display links if mode is "single"
if($options["column_format"] == "single") {
	echo "<tr><td colspan=\"5\" class=\"links divider\">\n";
	include("links.php");
	echo "</td></tr>\n";
}
?>
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
	echo "<b>$my_day</b><br />\n";
	$query = "SELECT * FROM comic WHERE date = '$year-$month-$my_day' AND is_visible=1 AND CAST(comic.date AS DATETIME)<NOW()";
	$result = mysql_query($query);
	if(mysql_num_rows($result)) {
		$row = mysql_fetch_array($result);
		echo "<small>".$row["title"]."</small><br />\n";
		echo "<a href=\"index.php?date=$year-$month-$my_day\"><img border=\"0\" src=\"".filename_to_thumb($row["path"])."\" /></a>\n";
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
<tr class="nav">
<td width="20%" align="left" valign="top">
<?=($back?"<a href=\"calendar.php?date=$first\">".FIRST."</a>":"")?>
</td>
<td width="20%" align="left" valign="top">
<?=($back?"<a href=\"calendar.php?date=$back\">".MONTH_BACK."</a>":"")?>
</td>
<td width="20%" align="center" valign="top"><form action="calendar.php" method="get"><?=display_date_dropdown("date","month",date("Y-m-d",$date))?> <input type="submit" value="Go" /></form></td>
<td width="20%" align="right" valign="top">
<?=($forward?"<a href=\"calendar.php?date=$forward\">".MONTH_FORWARD."</a>":"")?>
</td>
<td width="20%" align="right" valign="top">
<?=($forward?"<a href=\"calendar.php?date=$last\">".LAST."</a>":"")?>
</td></tr>
<?php
if($options["sort_dropdown"]=="chapter") {
?>
  <tr>
    <td colspan="5" align="center" class="top_divider">
      <form action="browse.php" method="get">
        Jump to chapter:<br />
        <select name="date">
<?php
//find current chapter
$result = mysql_query("SELECT date FROM chapter LEFT JOIN comic USING (comic_id) WHERE date <= '$year-$month-31' AND CAST(comic.date AS DATETIME)<NOW() ORDER BY date DESC LIMIT 1");
if(mysql_num_rows($result)) $my_chapter = array_pop(mysql_fetch_array($result));

//get chapters
$query = "SELECT date, chapter.title FROM chapter LEFT JOIN comic USING (comic_id) WHERE CAST(comic.date AS DATETIME)<NOW() ORDER BY date ASC";
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
if($options["column_format"]=="single") {
	echo "<tr><td colspan=\"5\" class=\"calendar_panel top_divider\"><a href=\"index.php?date=$year-$month-$day\"><b>Normal view</b></a></td></tr>\n";
}
?>
</table>
    </td>
  </tr>
</table>
<?php
include("footer.php");
?>
