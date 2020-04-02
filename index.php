<?php

session_start();

/*
 * Include the necessary files
 */
include_once 'inc/functions.inc.php';
include_once 'inc/db.inc.php';
// Open a database connection
$db = new PDO(DB_INFO, DB_USER, DB_PASS);
/*
 * Figure out what page is being requested (default is blog)
 * Perform basic sanitization on the variable as well
 */
if (isset($_GET['page'])) {
    $page = htmlentities(strip_tags($_GET['page']));
} else {
    $page = 'blog';
}

// Determine if an entry URL was passed
$url = (isset($_GET['url'])) ? $_GET['url'] : NULL;
// Load the entries 
//print_r($url); testshit
$e = retrieveEntries($db, $page, $url);
//print_r($e);
// Get the fulldisp flag and remove it from the array
$fulldisp = array_pop($e);
// Sanitize the entry data
$e = sanitizeData($e);



?>




<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <link rel="stylesheet" href="/simple_blog/css/default.css" type="text/css" />
    <link rel="alternate" type="text/xml" title="My Simple Blog - RSS 2.0" href="/simple_blog/feeds/rss.php" />
    <title> Simple Blog </title>
</head>

<body>
    <h1> Simple Blog Application </h1>
    <ul id="menu">
        <li><a href="/simple_blog/blog/">Blog</a></li>
        <li><a href="/simple_blog/about/">About the Author</a></li>
       
          

      
    </ul>

    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 1) : ?>
        <p id="control_panel">
            You are logged in!
            <a href="/simple_blog/inc/update.inc.php?action=logout">Log
                out</a>.
        </p>
    
    < 
    
    
    <?php else: (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 0)  ?> 
        <ul id="menu">
        <li><a href="/simple_blog/admin/">Login</a></li>
        </ul>

    <?php endif;?>






    <div id="entries">
        <?php
        // Format the entries from the database

        // If the full display flag is set, show the entry
        if ($fulldisp == 1) {
            // Get the URL if one wasn't passed
            $url = (isset($url)) ? $url : $e['url'];
            // Build the admin links
            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 1) {
                // Build the admin links
                $admin = adminLinks($page, $url);
            } else {
                $admin = array('edit' => NULL, 'delete' => NULL);
            }
            // Format the image if one exists
            $img = formatImage($e['image'], $e['title']);
            if ($page == 'blog') {
                // Load the comment object
                include_once 'inc/comments.inc.php';
                $comments = new Comments();
                $comment_disp = $comments->showComments($e['id']);

                $comment_form = $comments->showCommentForm($e['id']);
                // Generate a Post to Twitter link
                $twitter = postToTwitter($e['title']);
            } else {
                $comment_form = NULL;
                $twitter = NULL;
            }
        ?>
            <h2> <?php echo $e['title'] ?> </h2>
            <p> <?php echo $img, $e['entry'] ?> </p>

            <p> <?php echo $e['entry'] ?> </p>

            <p>
                <?php echo $admin['edit'] ?>
                <a href="<?php echo $twitter ?>">Post to Twitter</a><br/> 

                <?php if ($page == 'blog') echo $admin['delete'] ?>
            </p>


            <?php if ($page == 'blog') : ?>
                <p class="backlink">
                    <a href="./">Back to Latest Entries</a>
                </p>
                //<?php echo $comment_disp, $comment_form;
                endif; ?>




                <?php
            } // End the if statement
            // If the full display flag is 0, format linked entry titles
            else {
                // Loop through each entry
                foreach ($e as $entry) {
                ?>
                    <p>
                        <a href="/simple_blog/<?php echo $entry['page'] ?>/<?php echo $entry['url'] ?>">
                            <?php echo $entry['title'] ?>
                        </a>
                    </p>
            <?php
                } // End the foreach loop
            } // End the else
            ?>
            <p class="backlink">

                <?php
                if (
                    $page == 'blog'
                    && isset($_SESSION['loggedin'])
                    && $_SESSION['loggedin'] == 1

                ) :
                ?>
                    <a href="/simple_blog/admin/<?php echo $page ?>">
                        Post a New Entry
                    </a>


                <?php endif; ?>


            </p>
            <p>
                <a href="/simple_blog/feeds/rss.php">
                    Subscribe via RSS!
                </a>
            </p>
    </div>
</body>

</html>