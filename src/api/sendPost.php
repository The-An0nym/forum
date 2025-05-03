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
    $thread = $_POST["t"];

    if($thread != "") {
        $cont = trim(htmlspecialchars($_POST["c"]), "\u{0009}\u{000a}\u{000b}\u{000c}\u{000d}\u{0020}\u{00a0}\u{0085}\u{1680}\u{180e}\u{2000}\u{2001}\u{2002}\u{2003}\u{2004}\u{2005}\u{2006}\u{2007}\u{2008}\u{2009}\u{200a}\u{200b}\u{2028}\u{2029}\u{202f}\u{205f}\u{3000}\u{feff}"); // idk about mysql_real_escape_string ??
        
        if(strlen($cont) !== 0 && strlen($cont) <= 2000) {
            $dtime = date('Y-m-d H:i:s');
            $post_id = uniqid(rand(), true);
            $user_id = $_SESSION["user_id"];
            $sql = "INSERT INTO posts (user_id, post_id, content, created, edited, thread)
            VALUES ('$user_id', '$post_id', '$cont', '$dtime', 'false', '$thread')";
            if ($conn->query($sql) === FALSE) {
                echo "An error has occured [SP0]";
            }

            // Increment post count of user
            $sql = "UPDATE users SET posts = posts +1 WHERE user_id = '$user_id'";
            if ($conn->query($sql) === FALSE) {
                echo "An error has occured [SP1]";
            }

            // Increment post count of category and thread
            $sql = "UPDATE categories c
                    INNER JOIN threads t ON t.category = c.name
                    SET c.posts = c.posts +1, t.posts = t.posts +1 
                    WHERE t.name = '$thread'";
            if ($conn->query($sql) === FALSE) {
                echo "An error has occured [SP2]";
            }

        } else if(strlen($cont) === 0) {
            echo "No content";
        } else if(strlen($cont) > 2000) {
            echo "2000 character limit surpassed";
        }

    } else {
        echo "ERROR: Invalid or missing arguments.";
    }

} else {
    echo "Please Login to post";
}

$conn->close();
?>