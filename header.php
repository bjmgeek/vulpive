<?php
include_once("includes.php");
reload_options();
$title = $options["title"];
if($subtitle) $title .= " - $subtitle";
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?=$title?></title>
<link rel="stylesheet" href="default.css" type="text/css" />
<link rel="alternate" type="application/rss+xml" href="index_rss.php" title="<?=$options["title"]?>" />
<link rel="shortcut icon" href="images/favicon.ico" type="image/png" />
<!--BEGIN SITE-SPECIFIC HEAD DATA-->
<!--END SITE-SPECIFIC HEAD DATA-->
</head>
<body>
<!--BEGIN SITE-SPECIFIC HEADER-->
<!--END SITE-SPECIFIC HEADER-->
