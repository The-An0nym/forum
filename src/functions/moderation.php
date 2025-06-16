<?php

function createHistory($conn, int $type, int $judgement, $id, $sender_id, int $reason, string $message) {
    $mod_id = uniqid(rand(), true);

    if($type === 0) {
        $sql = "INSERT INTO mod_history_posts (post_id, mod_id)
                VALUES ('$id', '$mod_id')";
    } else if($type === 1) {
        $sql = "INSERT INTO mod_history_threads (thread_id, mod_id)
                VALUES ('$id', '$mod_id')";
    } else if($type === 2) {
        $sql = "INSERT INTO mod_history_users (user_id, mod_id)
                VALUES ('$id', '$mod_id')";
    } else {
        return;
    }

    if ($conn->query($sql) === FALSE) {
        echo "ERROR M0";
    }

    $sql = "INSERT INTO mod_history (mod_id, judgement, sender_id, reason, message)
            VALUES ('$mod_id', $judgement, '$sender_id', $reason, '$message')";
    if ($conn->query($sql) === FALSE) {
        echo "ERROR M1";
    }
}

function createReport($conn, int $type, $id, $user_id, int $reason, string $message) {
    if($type === 0) {
        $sql = "SELECT * FROM mod_history_posts mp
                JOIN mod_history mh ON mh.mod_id = mp.mod_id
                WHERE mp.post_id = '$id' AND mh.sender_id = '$user_id'";
    } else if($type === 1) {
        $sql = "SELECT * FROM mod_history_threads mt
                JOIN mod_history mh ON mh.mod_id = mt.mod_id
                WHERE mt.id = '$id' AND mh.sender_id = '$user_id'";
    } else if($type === 2) {
        $sql = "SELECT * FROM mod_history_users mu
                JOIN mod_history mh ON mh.mod_id = mu.mod_id
                WHERE mu.user_id = '$id' AND mh.sender_id = '$user_id'";
    } else {
        return;
    }

    $result = $conn->query($sql);
    if($result->num_rows === 0) {
        createHistory($conn, $type, 0, $id, $user_id, $reason, $message);
    } else {
        echo "You have already reported this item";
    }
}