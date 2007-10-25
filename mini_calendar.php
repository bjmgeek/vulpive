<?php
$concat = date("Ym",$date);
//find most recent comic that is before this one and not in the same month
$result = mysql_query("SELECT date FROM comic WHERE is_visible=1 AND EXTRACT(YEAR_MONTH FROM date) < $concat AND CAST(comic.date AS DATETIME)<NOW() ORDER BY date DESC LIMIT 1");
if(mysql_num_rows($result)) {
	$cal_back = array_pop(mysql_fetch_array($result));
} else {
	$cal_back = false;
}
//uh, the same thing in reverse
$result = mysql_query("SELECT date FROM comic WHERE is_visible=1 AND EXTRACT(YEAR_MONTH FROM date) > $concat AND CAST(comic.date AS DATETIME)<NOW() ORDER BY date ASC LIMIT 1");
if(mysql_num_rows($result)) {
	$cal_forward = array_pop(mysql_fetch_array($result));
} else {
	$cal_forward = false;
}
echo "<tr>\n<td class=\"calendar_panel\">\n";
echo "<b>".date("F, Y",$date)."</b><br />\n";
echo "<table class=\"calendar\">\n";
echo "<tr><th>S</th><th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th></tr><tr>";
$start_day = date("w",strtotime("$year-$month-1"));
$num_days = date("t",$date);
for($x = 0; $x < $start_day; $x++) {
	echo "<td class=\"off\">&nbsp;</td>";
}
for($x = $start_day; $x < $num_days + $start_day; $x++) {
	if($x % 7 == 0) echo "</tr><tr>";
	$my_day = $x - $start_day + 1;
	echo "<td ".($my_day==$day?"class=\"today\"":"class=\"normal_day\"").">";
	$query = "SELECT * FROM comic WHERE date = '$year-$month-$my_day' AND CAST(comic.date AS DATETIME)<NOW()";
	$result = mysql_query($query);
	if(mysql_num_rows($result)) {
		$row = mysql_fetch_array($result);
		echo "<a href=\"index.php?date=$year-$month-$my_day\"><b>$my_day</b></a>";
	} else {
		echo "<b>$my_day</b>";
	}
	echo "</td>\n";
}
while($x % 7) {
	echo "<td class=\"off\">&nbsp;</td>";
	$x++;
}
echo "</tr></table>\n";
echo "<table class=\"noborder\" width=\"100%\"><tr>\n";
echo "<td width=\"50%\" align=\"left\">".($cal_back?"<a href=\"index.php?date=$cal_back\">".MONTH_BACK."</a>":"")."</td>";
echo "<td width=\"50%\" align=\"right\">".($cal_forward?"<a href=\"index.php?date=$cal_forward\">".MONTH_FORWARD."</a>":"")."</td>";
echo "</tr></table>\n";
?>
