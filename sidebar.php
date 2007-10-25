    <td class="sidebar">
      <table width="100%">
        <tr>
	  <td class="links">
<?php
include("links.php");
?>
          </td>
	</tr>
	<tr>
	  <td class="calendar_panel">
<?php
if(strstr($_SERVER["SCRIPT_NAME"],"index.php")) {
	echo "<a href=\"calendar.php?date=$year-$month-$day\"><b>Calendar view</b></a></td></tr>";
	if($options["show_calendar"]=="true") {
		include("mini_calendar.php");
	}
} else {
	echo "<a href=\"index.php?date=$year-$month-$day\"><b>Normal view</b></a>";
}
?>
	  </td>
	</tr>
      </table>
    </td>
