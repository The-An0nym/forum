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
        echo "Comment posted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Increment post count of user
    $sql = "UPDATE users SET posts = posts +1 WHERE user_id = '$user_id'";
    if ($conn->query($sql) === TRUE) {
        echo "Incremented post count for user successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Increment user post count of category and thread
    $sql = "UPDATE categories c
            INNER JOIN threads t ON t.category = c.name
            SET c.posts = c.posts +1, t.posts = t.posts +1 
            WHERE t.name = '$thread'";
    if ($conn->query($sql) === TRUE) {
        echo "Incremented post count for category successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Please Login to comment";
}

$conn->close();
?>