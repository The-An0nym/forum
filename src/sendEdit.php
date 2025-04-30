<?php
$path = $_SERVER['DOCUMENT_ROOT'];


// CONNECT
$configs = include($path . 'functions/.config.php');
extract($configs);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

session_start();
if(include($path . 'functions/validateSession.php')) {
    // idk about mysql_real_escape_string ??
    $cont = htmlspecialchars($_POST["c"]);
    $user_id = $_SESSION["user_id"];
    $post_id = $_POST["i"];
    $sql = "UPDATE posts
            SET content = '$cont', edited = '1'
            WHERE post_id = '$post_id' AND user_id = '$user_id'";
    if ($conn->query($sql) === TRUE) {
        echo "0";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Please Login to comment";
}

$conn->close();
?>