<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/errors.php' ;

echo response();

function response() : string {
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

    $sql = "SELECT `id` FROM `threads` WHERE `slug` = '$thread_slug' AND `deleted` = 0";
    $result = $conn->query($sql);
    if($result->num_rows === 0) {
        return jsonErr("404thrds");
    }

    $thread_id = $result->fetch_assoc()["id"];

    $sql = "SELECT `thread_id` FROM `subscribed` WHERE `thread_id` = '$thread_id' AND `user_id` = '$user_id'";
    $result = $conn->query($sql);
    if($result->num_rows === 1) {
        $sql = "UPDATE `subscribed` SET `subscribed` = $sub WHERE `thread_id` = '$thread_id' AND `user_id` = '$user_id'";
        if($conn->query($sql) === FALSE) {
            return jsonErr("US0");
        }
        return pass();
    }

    if($result->num_rows > 1) {
        $sql = "DELETE `subscribed` WHERE `thread_id` = '$thread_id' AND `user_id` = '$user_id'";
        if($conn->query($sql) === FALSE) {
            return jsonErr("US1");
        }
    }

    $sql = "INSERT INTO `subscribed` (`thread_id`, `user_id`, `subscribed`)
            VALUES ('$thread_id', '$user_id', $sub)";
    if($conn->query($sql) === FALSE) {
        return jsonErr("US2");
    }

    if($sub == 1) {
        return getPass("sub");
    } else {
        return getPass("unSub");
    }
}