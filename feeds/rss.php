<?php
// Include necessary files
include_once '../inc/functions.inc.php';
include_once '../inc/db.inc.php';
// Open a database connection
$db = new PDO(DB_INFO, DB_USER, DB_PASS);
// Load all blog entries
$e = retrieveEntries($db, 'blog'); 
// Remove the fulldisp flag
array_pop($e);
// Perform basic data sanitization
$e = sanitizeData($e);
// Add a content type header to ensure proper execution
header('Content-Type: text/xml; charset=utf-8');
// Output the XML declaration
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<rss version="2.0">

<channel>
 <title>My Simple Blog</title>
 <link>http://localhost/simple_blog/</link>
 <description>This blog is awesome.</description>
 <language>en-us</language>
<?php
// Loop through the entries and generate RSS items
foreach($e as $e):
 // Escape HTML to avoid errors
 $entry = htmlentities($e['entry']);
 // Build the full URL to the entry
 $url = 'http://localhost/simple_blog/blog/' . $e['url'];
 // Format the date correctly for RSS pubDate
 $date = date(DATE_RSS, strtotime($e['created'])); 
?>
 <item>
 <title><?php echo $e['title']; ?></title>
 <description><?php echo $entry; ?></description>
 <link><?php echo $url; ?></link>
 <guid><?php echo $url; ?></guid> 
 <pubDate><?php echo $date; ?></pubDate> 



 </item>
<?php endforeach; ?>
</channel>
</rss> 
 

