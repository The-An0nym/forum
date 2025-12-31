<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/errors.php' ;

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

    if (!isset($_POST["i"])) {
        return jsonErr("args");
    }

    $user_id = $_SESSION['user_id'];
    $sql = "SELECT `clearance` FROM `users` WHERE `user_id` = '$user_id' LIMIT 1";

    $result = $conn->query($sql);
    $clearance = $result->fetch_assoc()["clearance"];

    // Only lvl 5 admins can pin threads
    if($clearance < 5) {
        return jsonErr("auth");
    }

    $thread_id = $_POST["i"];

    $sql = "SELECT `id`, `pinned` FROM `threads` WHERE `id` = '$thread_id' AND `deleted` = 0";
    $result = $conn->query($sql);
    if($result->num_rows === 0) {
        return jsonErr("404thrds");
    }

    $pinned = $result->fetch_assoc()["pinned"];

    $newPinVal = $pinned == 1 ? 0 : 1;

    $sql = "UPDATE `threads` SET `pinned` = $newPinVal WHERE `id` = '$thread_id'";
    if($conn->query($sql) === FALSE) {
        return jsonErr();
    }

    if($newPinVal === 1) {
        return getPass("pin");
    } else {
        return getPass("unpin");
    }
}