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
    // idk about mysql_real_escape_string ??
    $cont = htmlspecialchars($_POST["c"]);
    $user_id = $_SESSION["user_id"];
    $post_id = $_POST["i"];
    $sql = "UPDATE posts
            SET content = '$cont', edited = '1'
            WHERE post_id = '$post_id' AND user_id = '$user_id'";
    if ($conn->query($sql) === FALSE) {
        echo "An error has occured [EP0]";
    }
} else {
    echo "Please Login to edit posts";
}

$conn->close();
?>