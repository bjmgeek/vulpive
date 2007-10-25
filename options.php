<?php
$subtitle = "Options";
include("header.php");
//entries that must exist in the "options" table
$must_have = array("show_calendar"=>"true","show_dropdown"=>"true","sort_dropdown"=>"date","title"=>VERSION,"shoutout"=>"","enable_commentary"=>"false","max_images"=>"1","column_format"=>"double");
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
} elseif($_SESSION["user_id"] && $_POST["submit"]) {
	//save options
	$query = "UPDATE options SET value='".clean_form_sql($_POST["title"])."' WHERE name='title'";
	mysql_query($query);
	$query = "UPDATE options SET value='".$_POST["sort_dropdown"]."' WHERE name='sort_dropdown'";
	mysql_query($query);
	$query = "UPDATE options SET value='".$_POST["column_format"]."' WHERE name='column_format'";
	mysql_query($query);
	$query = "UPDATE options SET value='".$_POST["max_images"]."' WHERE name='max_images'";
	mysql_query($query);
	$query = "UPDATE options SET value='".($_POST["show_dropdown"]?"true":"false")."' WHERE name='show_dropdown'";
	mysql_query($query);
	$query = "UPDATE options SET value='".($_POST["show_calendar"]?"true":"false")."' WHERE name='show_calendar'";
	mysql_query($query);
	$query = "UPDATE options SET value='".($_POST["enable_commentary"]?"true":"false")."' WHERE name='enable_commentary'";
	mysql_query($query);
	$query = "UPDATE options SET value='".clean_form_sql($_POST["shoutout"])."' WHERE name='shoutout'";
	mysql_query($query);
	$messages[] = "Options saved.";
	reload_options();
}
if(!$_SESSION["user_id"]) {
	//not logged in
	include("log_in.php");
	include("footer.php");
	exit();
}

//let's double-check now to make sure all of those entries listed at the top exist in "options"
$what_we_have = array();
foreach($options as $name=>$value) {
	$what_we_have[] = $name;
}
foreach($must_have as $option_test=>$default_value) {
	if(!(in_array($option_test,$what_we_have))) {
		$query = "INSERT INTO options (name, value) VALUES ('$option_test',".clean_form_sql($default_value).")";
		mysql_query($query);
	}
}

//options page
include("header2.php");
?>
<h1>Global options:</h1>
<form action="options.php" method="post">
<table class="databox">
<tr><td><span class="tooltip" title="<?=$tooltips["options_title"]?>">Comic title:</span></td><td><input type="text" name="title" size="40" value="<?=$options["title"]?>" /></td></tr>
<tr><td><span class="tooltip" title="<?=$tooltips["options_format"]?>">Column format:</span></td><td><select name="column_format">
<?php
$format_options = array("double"=>"Two columns","single_archive"=>"One column for archives","single"=>"One column");
foreach($format_options as $value=>$name) {
	echo "<option value=\"$value\" ".($options["column_format"]==$value?"selected=\"selected\"":"").">$name</option>\n";
}
?>
</select></td></tr>
<tr><td>Show calendar on comic page?</td><td><input type="checkbox" name="show_calendar" <?=($options["show_calendar"]=="true"?"checked=\"checked\"":"")?> /></td></tr>
<tr><td>Show dropdown menu on comic page?</td><td><input type="checkbox" name="show_dropdown" <?=($options["show_dropdown"]=="true"?"checked=\"checked\"":"")?> /></td></tr>
<tr><td><span class="tooltip" title="<?=$tooltips["options_sort"]?>">Dropdown contents:</span></td><td><select name="sort_dropdown">
<?php
$sort_options = array("date"=>"Comics by date","title"=>"Comics by title","chapter"=>"Chapters");
foreach($sort_options as $value=>$name) {
	echo "<option value=\"$value\" ".($options["sort_dropdown"]==$value?"selected=\"selected\"":"").">$name</option>\n";
}
?>
</select></td></tr>
<tr><td><span class="tooltip" title="<?=$tooltips["options_max_images"]?>">Max images per comic:</span></td><td><select name="max_images">
<?php
for($i=1;$i<=10;$i++) {
	echo "<option value=\"$i\" ".((int)$options["max_images"]==$i?"selected=\"selected\"":"").">$i</option>\n";
}
?>
</select></td></tr>
<tr><td><span class="tooltip" title="<?=$tooltips["options_commentary"]?>">Enable comic commentary:</span></td><td><input type="checkbox" name="enable_commentary" <?=($options["enable_commentary"]=="true"?"checked=\"checked\"":"")?> /></td></tr>
<tr><td><span class="tooltip" title="<?=$tooltips["options_shoutout"]?>">Shout-out:</span></td><td><textarea name="shoutout" rows="5" cols="40"><?=clean_sql_form($options["shoutout"])?></textarea></td></tr>
<tr><td colspan="2" align="right"><input type="submit" name="submit" value="Save" /></td></tr>
</table>
</form>
<?
include("footer2.php");
include("footer.php");
?>
