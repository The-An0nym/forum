<?php
include $_SERVER['DOCUMENT_ROOT'] . '/functions/.connect.php';

function NewNotifCount(string $user_id = "") : array {
    if($user_id === "") {
        return [false, "args"];
    }

    $conn = getConn();

    $sql = "SELECT COUNT(*) AS total FROM notifications WHERE receiver_id = '$user_id' AND read = 0";
    $result = $conn->query($sql);
    return [true, (int)$result->fetch_assoc()["total"]];
}