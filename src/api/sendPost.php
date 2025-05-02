<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include($path . '/functions/.config.php');

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
if(include($path . '/functions/validateSession.php')) {
    $thread = $_POST["t"];
    if($thread != "") {
        // idk about mysql_real_escape_string ??
        $cont = htmlspecialchars($_POST["c"]);
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

        // Increment user post count of category and thread
        $sql = "UPDATE categories c
                INNER JOIN threads t ON t.category = c.name
                SET c.posts = c.posts +1, t.posts = t.posts +1 
                WHERE t.name = '$thread'";
        if ($conn->query($sql) === FALSE) {
            echo "An error has occured [SP2]";
        }
    } else {
        echo "ERROR: Invalid or missing arguments.";
    }
} else {
    echo "Please Login to post";
}

$conn->close();
?>