<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(!session_id()) {
  session_start();
} 

if(include($path . '/functions/validateSession.php')) {

    if(isset($_POST["c"], $_POST["p"], $_POST["n"])) {
        $category = $_POST["c"];
        $cont = trim(htmlspecialchars($_POST["p"]), "\u{0009}\u{000a}\u{000b}\u{000c}\u{000d}\u{0020}\u{00a0}\u{0085}\u{1680}\u{180e}\u{2000}\u{2001}\u{2002}\u{2003}\u{2004}\u{2005}\u{2006}\u{2007}\u{2008}\u{2009}\u{200a}\u{200b}\u{2028}\u{2029}\u{202f}\u{205f}\u{3000}\u{feff}"); // idk about mysql_real_escape_string ??
        $threadName = str_replace(" ", "-", trim(htmlspecialchars($_POST["n"]), "\u{0009}\u{000a}\u{000b}\u{000c}\u{000d}\u{0020}\u{00a0}\u{0085}\u{1680}\u{180e}\u{2000}\u{2001}\u{2002}\u{2003}\u{2004}\u{2005}\u{2006}\u{2007}\u{2008}\u{2009}\u{200a}\u{200b}\u{2028}\u{2029}\u{202f}\u{205f}\u{3000}\u{feff}")); // idk about mysql_real_escape_string ??

        if(strlen($cont) !== 0 && strlen($cont) <= 2000 && strlen($threadName) <= 64 && strlen($threadName) >= 8 && preg_match('/^[A-z0-9-_!?().,]*$/i', $threadName) == 1) {
            // Create Thread
            $dtime = date('Y-m-d H:i:s');
            $sql = "INSERT INTO threads (name, category, created, posts)
            VALUES ('$threadName', '$category', '$dtime', 1)";
            if ($conn->query($sql) === FALSE) {
                echo "An error has occured [CT0]";
            }

            // Create Post
            $post_id = uniqid(rand(), true);
            $user_id = $_SESSION["user_id"];
            $sql = "INSERT INTO posts (user_id, post_id, content, created, edited, thread)
            VALUES ('$user_id', '$post_id', '$cont', '$dtime', 'false', '$threadName')";
            if ($conn->query($sql) === FALSE) {
                echo "An error has occured [CT1]";
            }

            // Increment post count of user
            $sql = "UPDATE users SET posts = posts +1 WHERE user_id = '$user_id'";
            if ($conn->query($sql) === FALSE) {
                echo "An error has occured [CT2]";
            }

            // Increment post count of category
            $sql = "UPDATE categories
                    SET posts = posts +1, threads = threads +1 
                    WHERE name = '$category'";
            if ($conn->query($sql) === FALSE) {
                echo "An error has occured [CT3]";
            }

        } else if(strlen($cont) === 0) {
            echo "No content";
        } else if(strlen($cont) > 2000) {
            echo "2000 character limit surpassed";
        } else if(preg_match('/^[A-z0-9-_!?().,]*$/i', $threadName) != 1) {
            echo "Only characters a-Z 0-9 - _ ! ? ( ) . , are allowed";
        } else if(strlen($threadName) > 64) {
            echo "Max. 64 chars allowed for thread names";
        } else if(strlen($threadName) < 8) {
            echo "At least 8 chars are needed for a thread name";
        }

    } else {
        echo "ERROR: Invalid or missing arguments.";
    }

} else {
    echo "Please Login to post";
}

$conn->close();
?>