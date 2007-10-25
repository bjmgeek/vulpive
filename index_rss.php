<?php
header("Content-type: application/rss+xml");
include("includes.php");
reload_options();
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
echo "<?xml-stylesheet href=\"http://feeds.feedburner.com/~d/styles/rss2enclosuresfull.xsl\" type=\"text/xsl\" media=\"screen\"?><?xml-stylesheet href=\"http://feeds.feedburner.com/~d/styles/itemcontent.css\" type=\"text/css\" media=\"screen\"?>";
$location = "http://".$_SERVER["HTTP_HOST"].substr($_SERVER["REQUEST_URI"],0,strrpos($_SERVER["REQUEST_URI"],"/"));
?>
<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" version="2.0">
<channel>
<link><?=$location?></link>
<pubDate><?=date("r")?></pubDate>
<language>en</language>
<?php
$result = mysql_query("SELECT *,IF(LENGTH(title)>0,title,DATE_FORMAT(comic.date,'%M %e, %Y')) AS title FROM comic WHERE is_visible=1 AND CAST(comic.date AS DATETIME)<NOW() ORDER BY date DESC LIMIT 10");
while($row=mysql_fetch_array($result)) {
	echo "<item><title>".$row["title"]."</title>\n";
	echo "<link>$location/index.php?date=".$row["date"]."</link>\n";
	echo "<guid isPermaLink=\"true\">$location/index.php?date=".$row["date"]."</guid>\n";
	if($options["commentary"]=="true") echo "<description><![CDATA[\"".$row["commentary"]."\"]]></description>\n";
	echo "</item>\n";
}
?>
</channel>
</rss>
