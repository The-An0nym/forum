<?php

function createHistory($conn, int $type, int $judgement $id, $sender_id, int $reason, string $message) {
    $mod_id = uniqid(rand(), true);

    if($type === 0) {
        $sql = "INSERT INTO mod_history_posts (post_id, mod_id)
                VALUES ('$id', '$mod_id')";
    } else if($type === 1) {
        $sql = "INSERT INTO mod_history_threads (post_id, mod_id)
                VALUES ('$id', '$mod_id')";
    } else if($type === 2) {
        $sql = "INSERT INTO mod_history_users (post_id, mod_id)
                VALUES ('$id', '$mod_id')";
    } else {
        return;
    }

    if ($conn->query($sql) === FALSE) {
        echo "ERROR M0";
    }

    $sql = "INSERT INTO mod_history (mod_id, judgement, sender_id, reason, message)
            VALUES ('$mod_id', $judgement, '$user_id', $reason, '$message')";
    if ($conn->query($sql) === FALSE) {
        echo "ERROR M1";
    }
}