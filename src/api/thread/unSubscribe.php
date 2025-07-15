<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/validateSession.php';
include $path . '/functions/errors.php' ;

echo response();

function response() {
    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    }

    if(!validateSession()) {
        return jsonErr("login");
    }

    $sub = "1";
    if (!isset($_POST["t"])) {
        return jsonErr("args");
    }

    $thread_slug = $_POST["t"];
    if (isset($_POST["s"])) {
        if($_POST["s"] == 0 || $_POST["s"] == 1) {
            $sub = $_POST["s"];
        }
    }

    $user_id = $_SESSION["user_id"];

    $sql = "SELECT id FROM threads WHERE slug = '$thread_slug' AND deleted = 0";
    $result = $conn->query($sql);
    if($result->num_rows === 0) {
        return jsonErr("404thrds");
    }

    $thread_id = $result->fetch_assoc()["id"];

    $sql = "SELECT thread_id FROM subscribed WHERE thread_id = '$thread_id' AND user_id = '$user_id'";
    $result = $conn->query($sql);
    if($result->num_rows === 1) {
        $sql = "UPDATE subscribed SET subscribed = $sub WHERE thread_id = '$thread_id' AND user_id = '$user_id'";
        if($conn->query($sql) === FALSE) {
            return jsonErr("US0");
        }
        return pass();
    }

    if($result->num_rows > 1) {
        $sql = "DELETE subscribed WHERE thread_id = '$thread_id' AND user_id = '$user_id'";
        if($conn->query($sql) === FALSE) {
            return jsonErr("US1");
        }
    }

    $sql = "INSERT INTO subscribed (thread_id, user_id, subscribed)
            VALUES ('$thread_id', '$user_id', $sub)";
    if($conn->query($sql) === FALSE) {
        return jsonErr("US2");
    }

    return pass();
}