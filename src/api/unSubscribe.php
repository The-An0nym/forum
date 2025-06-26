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
    if (!isset($_GET["t"])) {
        $thread_slug = $_GET["t"];
        if (!isset($_GET["s"])) {
            $sub = (bool)$_GET["s"];
        } else {
            $sub = true;
        }
    } else {
        echo "Thread id not given";
        die();
    }

    $user_id = $_SESSION["user_id"];

    $sql = "SELECT id FROM threads WHERE slug = '$thread_slug' AND deleted = 0";
    $result = $conn->query($sql);
    if($result->num_rows === 0) {
        echo "Thread not found";
        die();
    }

    $thread_id = $result->fetch_assoc()["id"];

    $sql = "SELECT thread_id from subscribed WHERE thread_id = '$thread_id' AND user_id = '$user_id'";
    $result = $conn->query($sql);
    if($result->num_rows === 1) {
        $sql = "UPDATE subscribed SET subscribed = $sub WHERE thread_id = '$thread_id' AND user_id = '$user_id'"
        die();
    }


    if($result->num_rows > 1) {
        $sql = "DELETE subscribed WHERE thread_id = '$thread_id' AND user_id = '$user_id'";
        if($conn->query($sql) === FALSE) {
            echo "An error has occured trying to delete duplicate subscription entires";
        }
    }

    $sql = "INSERT INTO subscribed (thread_id, user_id, subscribed)
            VALUES ('$thread_id', '$user_id', $sub)";
    if($conn->query($sql) === FALSE) {
        echo "An error has occured trying to (un)subscribe from this thread";
    }
    
} else {
    echo "Please log in to continue";
}