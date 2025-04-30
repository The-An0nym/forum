<?php
$path = $_SERVER['DOCUMENT_ROOT'];

// CONNECT
$configs = include($path . '/functions/.config.php');
extract($configs);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

session_start();
if(include($path . '/functions/validateSession.php')) {
    // idk about mysql_real_escape_string ??
    $cont = htmlspecialchars($_POST["c"]);
    $dtime = date('Y-m-d H:i:s');
    $post_id = uniqid(rand(), true);
    $user_id = $_SESSION["user_id"];
    $thread = $_POST["t"];
    $sql = "INSERT INTO posts (user_id, post_id, content, created, edited, thread)
    VALUES ('$user_id', '$post_id', '$cont', '$dtime', 'false', '$thread')";
    if ($conn->query($sql) === TRUE) {
        popUp("Comment posted successfully");
    } else {
        popUp("Error: " . $sql . "<br>" . $conn->error);
    }
} else {
    echo "Please Login to comment";
}

$conn->close();
?>