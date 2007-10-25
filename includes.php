<?php
session_start();
mysql_connect("localhost","root",null);  // <-- change me!
mysql_select_db("vulpive");  // change me too, if your database isn't called "vulpive"
define("VERSION", "Vulpive 0.2");
define("FIRST", "&lt;&lt; First");
define("PREVIOUS", "&lt;&nbsp;Previous");
define("NEXT", "Next&nbsp;&gt;");
define("LAST", "Last &gt;&gt;");
define("MONTH_BACK", PREVIOUS);
define("MONTH_FORWARD", NEXT);
define("THUMB_WIDTH",150);
define("NO_THUMB","images/no_thumb.png");

include("tooltips.php");

function reload_options() {
	global $options;
	$result = mysql_query("SELECT * FROM options");
	while($row = mysql_fetch_array($result)) {
		$options[$row["name"]] = $row["value"];
	}
}

function ordinal($cardinal)    {
  $cardinal = (int)$cardinal;
  $digit = substr($cardinal, -1, 1);

  if ($cardinal <100) $tens = round($cardinal/10);
  else $tens = substr($cardinal, -2, 1);

  if($tens == 1)  {
    return $cardinal.'th';
  }

  switch($digit) {
    case 1:
      return $cardinal.'st';
    case 2:
      return $cardinal.'nd';
    case 3:
      return $cardinal.'rd';
    default:
      return $cardinal.'th';
  }
}

function draw_thumb($comic_id) {
	$result = mysql_query("SELECT path FROM comic WHERE comic_id=".$_GET["comic_id"]);
	$image = array_pop(mysql_fetch_array($result));
	$thumb = filename_to_thumb($image);
	echo "<a href=\"$image\"><img src=\"$thumb\" border=\"0\" /></a>\n";
}

function filename_to_thumb($filename,$nothumb=true) {
	$thumb = substr($filename,0,strrpos($filename,".")).".thumb.png";
	if($nothumb && !file_exists($thumb)) {
		return NO_THUMB;
	}
	return $thumb;
}

function strip_folder($path) {
	return substr($path, strrpos($path,"/")+1);
}

function display_date_dropdown($varname, $date_type="datetime", $set_date=NULL, $font_size=NULL, $display_text="N", $min_incr=5, $seconds=FALSE, $return=false){
/** Parameters :
          $varname gives basename for the date/time variable array that will be filled with data
          $date_type tells us to display Date, time or both. Default is Both.
          $set_date contains a string in MySQL date format "YYYY-MM-DD HH:MM:ss"
                    to which we need to set our initial values on the form.
                    if it is missing we will assume current time/date
    Output  :
            This function sets up Date and/or Time   SELECT drop down boxes for inclusion
            in php scripts for display in HTML forms. The date information is available to
            PHP in the form of an array - IT'S UP TO YOU TO EXTRACT THE INFORMATION FOR USE
            The array delivers information in this structure :

                Jonathan: OOPS!  I really shouldn't have changed this.  These really should have been the arrays they were originally
                          set to
                Richard: So I'm trying to put them back!

                array_name["year"]
                array_name["month"]
                array_name["day"]
                array_name["hour"]
                array_name["minute"]
                array_name["second"]
            you can manipulate these values in any way you want, I don't impose a structure
            on your use of it.
                        Dewi


**/
// Check parameters for format
$dump= eregi("date",$date_type) || eregi("time",$date_type) || $date_type=="month" || $date_type="datetime";
// These arrays can be set up in an include file somewhere to allow easy mod of years
// also include define() for CONSTANTS used here
$current_year = date("Y");
for($x=2000;$x<=$current_year+3;$x++) {
	$years[$x] = $x;
}

$months = array (
  "01" => "Jan",
  "02" => "Feb",
  "03" => "Mar",
  "04" => "Apr",
  "05" => "May",
  "06" => "Jun",
  "07" => "Jul",
  "08" => "Aug",
  "09" => "Sep",
  "10" => "Oct",
  "11" => "Nov",
  "12" => "Dec"
);

// Get current date and time to use as defaults for display. Split in to component parts
if (!isset($set_date))$set_date=date("Y-m-d H:i",time());
list ($tmp_date,$tmp_time)= explode(" ",$set_date);
list($def_year,$def_mo,$def_day)=explode("-",$tmp_date);
list($def_hour,$def_min,$def_sec)=explode(":",$tmp_time);
// some code here to handle default minutes if required - based on 5 minute slots
$def_min=round($def_min/$min_incr,0)*$min_incr;

if ($font_size <> NULL) $ret .= "<font size=\"$font_size\">";
// Now output required date/time selection fields
if (eregi("date",$date_type)||$date_type=="month"){
  $ret .= "<span class=\"dropdowns\">";
// Select Year
  if ($display_text <> 'N') $ret .= "Date: ";
  $ret .= "<select name=\"".$varname."[year]\" id=\"".$varname."_year\">\n";
  foreach ($years as $key => $value){
    if ( strcmp("$key","$def_year")) $selected=""; else $selected = "selected=\"selected\"";
    $ret .= "<option value=\"$key\" $selected>$value</option>\n";
  }
  $ret .= "</select>-";

//Select month
  $ret .= "<select name=\"".$varname."[month]\" id=\"".$varname."_month\">\n";
  foreach ($months as $key => $value){
    if ( strcmp("$key","$def_mo")) $selected=""; else $selected = "selected=\"selected\"";
    $ret .= "<option value=\"$key\" $selected>$value</option>\n";
  }
}
if(eregi("date",$date_type)) {
  $ret .= "</select>-";

// Select day
  $ret .= "<select name=\"".$varname."[day]\" id=\"".$varname."_day\">\n";
  for ($x=1;$x<=31;$x++){
    $day=sprintf("%02d",$x);
    if ( strcmp("$day","$def_day")) $selected=""; else $selected = "selected=\"selected\"";
     $ret .= sprintf("<option value=\"%02d\" $selected>%02d</option>\n",$x,$x);
  }
  $ret .= "</select></span>&nbsp;&nbsp;";
} else {
  $ret .= "</select></span>";
}
#if (eregi("date",$date_type) && eregi("date",$date_type) && $display_text <> 'N') print "<br />";

if (eregi("time",$date_type)) {
  $ret .= "<span class=\"dropdowns\">";
// print out hours
  if ($display_text <> 'N')$ret .= "Time: ";
  $ret .= "<select name=\"".$varname."[hour]\" id=\"".$varname."_hour\">";
  for ($x=0;$x<24;$x++){
    $hour=sprintf("%02d",$x);
    if ( strcmp("$hour","$def_hour")) $selected=""; else $selected = "selected=\"selected\"";
     $ret .= sprintf("<option value=\"%02d\" $selected>%02d</option>\n",$x,$x);
  }
  $ret .= "</select>:";



// print out minutes
  $ret .= "<select name=\"".$varname."[minute]\" id=\"".$varname."_minute\">";
  for ($x=0;$x<60;$x=$x+$min_incr){
    $min=sprintf("%02d",$x);
    if ($min != $def_min) $selected=""; else $selected = "selected=\"selected\"";
     $ret .= sprintf("<option value=\"%02d\" $selected>%02d</option>\n",$x,$x);
  }
  $ret .= "</select>";
  if($seconds) {
          $ret .= ":<select name=\"".$varname."[second]\" id=\"".$varname."_second\">";
          for ($x=0;$x<60;$x=$x+$min_incr){
            $sec=sprintf("%02d",$x);
            if ($sec != $def_sec) $selected=""; else $selected = "selected=\"selected\"";
             $ret .= sprintf("<option value=\"%02d\" $selected>%02d</option>\n",$x,$x);
          }
          $ret .= "</select>\n";
  } else {
        $ret .= sprintf ("<input type=\"hidden\" name=\"".$s["second"]."\" value=\"00\" />",$varname);
  }
  $ret .= "</span>";
}
if ($font_size <> NULL) $ret .= "</font>";
if($return) {
        return $ret;
} else {
        echo $ret;
}
} // End of function display_date_dropdown


function array_to_date($array,$mode="time",$time="manual") {
        if(!is_array($array)) return $array;
        if(($time=="manual")&&(!$array["second"])) {
                $time="start";
        }
	if(!$array["day"]) $array["day"] = 1;
        if($time=="start") {
                $date = mktime(0,0,0,$array["month"],$array["day"],$array["year"]);
        } elseif($time=="end") {
                $date = mktime(23,59,59,$array["month"],$array["day"],$array["year"]);
        } elseif($time=="manual") {
                $date = mktime($array["hour"],$array["minute"],$array["second"],$array["month"],$array["day"],$array["year"]);
        }
        if($mode=="unix") {
                return $date;
        } elseif($mode=="date") {
                return date("Y-m-d",$date);
        } elseif($mode=="time") {
                return date("Y-m-d H:i:s",$date);
        }
        return false;
}

function clean_sql_form($data) {
	$data=htmlentities($data);
	return $data;
}


function clean_form_sql($data) {
        $data=stripslashes($data);
        $data=html_entity_decode($data);
        $data=mysql_escape_string($data);
        return $data;
}

function clean_sql_display($data) {
        $data=htmlentities($data);
        $data=nl2br($data);
        // allow <i>,<u>,<b>,<s> tags through
        //$strtr_array = array('&lt;i&gt;'=>'<i>','&lt;/i&gt;'=>'</i>','&lt;i&gt;'=>'<u>','&lt;/i&gt;'=>'</u>','&lt;i&gt;'=>'<b>','&lt;/i&gt;'=>'</b>','&lt;i&gt;'=>'<s>','&lt;/i&gt;'=>'</s>');
	//allow all tags
	$strtr_array = array('&lt;' => '<', '&gt;' => '>');
        $data=strtr($data,$strtr_array);
        return $data;
}

?>
