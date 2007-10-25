<?php
$subtitle = "Upload Comics";
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
} elseif($_POST["upload"] && $_SESSION["user_id"] && strlen($_FILES['filename_1']['name'])) {  //only allow upload if logged in and a file is specified
	//process upload
	$scheme = $_POST["naming_scheme"];
	//save naming scheme
	mysql_query("UPDATE options SET value='$scheme' WHERE name='naming_scheme'");
	reload_options();
	//generate filename
	for($i=1;$i<=$options["max_images"];$i++) {
		if($_FILES["filename_$i"]["name"]) {
			$filenames[$i] = generate_filename($_FILES["filename_$i"]["name"],$i);
		} else {
			break;
		}
	}
	$num_images = $i-1;
	//does a comic exist for this date?
	$result = mysql_query("SELECT * FROM comic WHERE date = '".array_to_date($_POST['date'],"date")."'");
	if(mysql_num_rows($result)) {
		$row = mysql_fetch_array($result);
		$overwrite_filename = strip_folder($row["path"]);
		$overwrite_id = $row["comic_id"];
	}
	if($overwrite_id && !$_POST["overwrite"]) {
		$abort = true;
		$messages[] = "A comic already exists for that date.";
	}
	foreach($filenames as $i=>$filename) {
		if(file_exists("images/".$filename)) {
			if(!$_POST["overwrite"] || $filename != $overwrite_filename) {
				$abort = true;
				$overwrite_error = true;
				$message = "Filename already taken".($num_images>1?" (".ordinal($i)." image)":"").".";
				$messages[] = $message;
			}
		}
	}
	if($_POST["overwrite"] && $overwrite_error) $message .= "  (You can only overwrite a comic if it's on the same date as the one you're uploading.)";
	if(!$abort) {
		if($overwrite_id) {
			//delete old comic
			unlink("images/".$overwrite_filename);
			mysql_query("DELETE FROM comic WHERE comic_id=$overwrite_id");
			//delete extra images
			$result = mysql_query("SELECT * FROM multi_comic WHERE comic_id=$overwrite_id");
			while($row = mysql_fetch_array($result)) {
				unlink($row["path"]);
			}
			mysql_query("DELETE FROM multi_comic WHERE comic_id=$overwrite_id");
		}
		for($i=1;$i<=$num_images;$i++) {
			if(move_uploaded_file($_FILES["filename_$i"]['tmp_name'],"images/".$filenames[$i])) {
				if($i == 1) {
					$query = "INSERT INTO comic (date, title, path, is_visible".($enable_commentary?", commentary":"").") VALUES ('".array_to_date($_POST['date'])."', '".clean_form_sql($_POST["title"])."', 'images/".$filenames[1]."', ".($_POST["is_visible"]?"1":"0").($enable_commentary?", '".clean_form_sql($_POST["commentary"])."'":"").")";
					mysql_query($query);
					$first_comic = mysql_insert_id();
					if(strlen(trim($_POST["chapter"]))) {
						$query = "INSERT INTO chapter (title, comic_id) VALUES ('".clean_form_sql($_POST["chapter"])."',".mysql_insert_id().")";
						mysql_query($query);
						$messages[] = "New chapter created.";
					}
					//create thumbnail if possible
					$type = substr($_FILES["filename_1"]['type'],strrpos($_FILES["filename_1"]['type'],"/")+1);
					switch($type) {
						case "png":
							$im_in = imagecreatefrompng("images/".$filenames[1]);
							break;
						case "jpeg":
							$im_in = imagecreatefromjpeg("images/".$filenames[1]);
							break;
						case "gif":
							$im_in = imagecreatefromgif("images/".$filenames[1]);
							break;
					}
					if(isset($im_in)) {
						$width = THUMB_WIDTH;
						$height = THUMB_WIDTH * imagesy($im_in) / imagesx($im_in);
						$im_out = imagecreatetruecolor($width,$height);
						imagecopyresampled($im_out,$im_in,0,0,0,0,$width,$height,imagesx($im_in),imagesy($im_in));
						imagepng($im_out,"images/".filename_to_thumb($filenames[1],false));
						imagedestroy($im_in); imagedestroy($im_out);
					}
				} else {
					$query = "INSERT INTO multi_comic (comic_id, path, sort_order) VALUES ($first_comic, 'images/".$filenames[$i]."',$i)";
					mysql_query($query);
				}
				$success++;
			} else {
				$messages[] = "Upload failed".($num_images>1?" (".ordinal($i)." image)":"").".";
			}
		}
		if($overwrite_id) {
			//If old comic had a chapter, move it to this comic.  (Unless we're creating a chapter; in that case, delete it.)
			if(!strlen(trim($_POST["chapter"]))) {
				//move chapter
				mysql_query("UPDATE chapter SET comic_id=$first_comic WHERE comic_id=$overwrite_id");
			} else {
				//delete chapter
				mysql_query("DELETE FROM chapter WHERE comic_id=$overwrite_id");
			}
		}
		if($success == $num_images) $messages[] = "Comic successfully uploaded!";
	}
}
if(!$_SESSION["user_id"]) {
	//not logged in
	include("log_in.php");
	include("footer.php");
	exit();
}
//upload form
include("header2.php");
?>
<h1>Upload comic:</h1>
<form enctype="multipart/form-data" action="upload.php" method="post">
<table class="databox">
<?php
if($options["max_images"]=="1") {
	echo "<tr><td>File:</td><td><input type=\"file\" name=\"filename_1\" /></td></tr>\n";
} else {
	//oh boy, here we go
	//uploading multiple images
	for($i = 1; $i <= (int)$options["max_images"]; $i++) {
		echo "<tr><td>".ordinal($i)." file:</td><td><input type=\"file\" name=\"filename_$i\" /></td></tr>\n";
	}
}
?>
<tr><td><span class="tooltip" title="<?=$tooltips["upload_date"]?>">Date:</span></td><td><?=display_date_dropdown("date","date",array_to_date($_REQUEST["date"]))?></td></tr>
<tr><td><span class="tooltip" title="<?=$tooltips["upload_title"]?>">Comic title:</span></td><td><input type="text" size="40" name="title" /></td></tr>
<tr><td><span class="tooltip" title="<?=$tooltips["upload_chapter"]?>">Start chapter?</span></td><td><input type="text" size="40" name="chapter" /></td></tr>
<tr><td><span class="tooltip" title="<?=$tooltips["upload_name"]?>">File name:</span></td><td><select name="naming_scheme">
<?
$my_options = array("original","date","title","random");
$scheme = $options["naming_scheme"];
foreach($my_options as $option) {
	echo "<option value=\"$option\" ".($option==$scheme?"selected=\"selected\"":"").">$option</option>";
}
?>
</select></td></tr>
<tr><td><span class="tooltip" title="<?=$tooltips["upload_visible"]?>">Visible?</span></td><td><input type="checkbox" name="is_visible" checked="checked" /></td></tr>
<tr><td><span class="tooltip" title="<?=$tooltips["upload_overwrite"]?>">Overwrite?</span></td><td><input type="checkbox" name="overwrite" /></td></tr>
<?php
if($enable_commentary) {
	echo "<tr><td>Commentary:</td><td><textarea name=\"commentary\" rows=\"5\" cols=\"40\"></textarea></td></tr>";
}
?>
<tr><td colspan="2" align="right"><input type="submit" name="upload" value="Upload" /></td></tr>
</table>
</form>
<?
include("footer2.php");
include("footer.php");

function generate_filename($name,$i) {
	global $_POST;
	if($i > 1) $suffix = "_$i";
	switch($_POST["naming_scheme"]) {
		case "original":
			$filename = $name;
			break;
		case "date":
			$extention = strrchr($name,".");
			$filename = $_POST['date']['year'].$_POST['date']['month'].$_POST['date']['day'].$suffix.$extention;
			break;
		case "title":
			$extention = strrchr(stripslashes($name),".");
			$tr_array = array(" "=>"-",","=>"",":"=>"",";"=>"","."=>"","!"=>"","?"=>"","("=>"",")"=>"","["=>"","]"=>"","$"=>"","%"=>"","\""=>"","'"=>"","\\"=>"","/"=>"");
			$filename = strtr(strtolower($_POST["title"]),$tr_array).$suffix.$extention;
			break;
		case "random":
			$extention = strrchr($name,".");
			$filename = md5($_POST['date']['year'].$_POST['date']['month'].$_POST['date']['day']).$suffix.$extention;
			break;
	}
	return $filename;
}
?>
