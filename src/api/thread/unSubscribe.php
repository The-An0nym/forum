<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include($path . '/functions/validateSession.php')

// Get connection
$conn = getConn();

if(!session_id()) {
  session_start();
}

echo response();

function repsonse() {

    if(validateSession()) {
        return "Please login to continue";
    }

    $sub = "1";
    if (isset($_GET["t"])) {
        $thread_slug = $_GET["t"];
        if (isset($_GET["s"])) {
            if($_GET["s"] == 0 || $_GET["s"] == 1) {
                $sub = $_GET["s"];
            }
        }
    } else {
        return "Thread id not given";
    }

    $user_id = $_SESSION["user_id"];

    $sql = "SELECT id FROM threads WHERE slug = '$thread_slug' AND deleted = 0";
    $result = $conn->query($sql);
    if($result->num_rows === 0) {
        return "Thread not found";
    }

    $thread_id = $result->fetch_assoc()["id"];

    $sql = "SELECT thread_id FROM subscribed WHERE thread_id = '$thread_id' AND user_id = '$user_id'";
    $result = $conn->query($sql);
    if($result->num_rows === 1) {
        $sql = "UPDATE subscribed SET subscribed = $sub WHERE thread_id = '$thread_id' AND user_id = '$user_id'";
        if($conn->query($sql) === FALSE) {
            return "An error has occured while trying to update subscription entry";
        }
        return; // Pass
    }

    if($result->num_rows > 1) {
        $sql = "DELETE subscribed WHERE thread_id = '$thread_id' AND user_id = '$user_id'";
        if($conn->query($sql) === FALSE) {
            return "An error has occured while trying to delete duplicate subscription entires";
        }
    }

    $sql = "INSERT INTO subscribed (thread_id, user_id, subscribed)
            VALUES ('$thread_id', '$user_id', $sub)";
    if($conn->query($sql) === FALSE) {
        return "An error has occured trying to (un)subscribe from this thread";
    }
}