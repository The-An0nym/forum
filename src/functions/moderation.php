<?php

function createHistory($conn, int $type, int $judgement, $id, $sender_id, int $reason, string $message) {
    $mod_id = uniqid(rand(), true);

    $summary = "";
    $culp_id = "";

    if($type === 0) {
        $sql = "SELECT content, user_id FROM posts WHERE post_id = '$id'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $summary = $row["content"];
        $culp_id = $row["user_id"];
    } else if($type === 1) {
        $sql = "SELECT name, user_id FROM threads WHERE id = '$id'";
        $row = $result->fetch_assoc();
        $summary = $row["name"];
        $culp_id = $row["user_id"];
    } else if($type === 2) {
        $culp_id = $id;
    }

    $summary = substr($summary, 0, 64);

    $sql = "INSERT INTO mod_history (mod_id, culp_id, id, summary, type, judgement, sender_id, reason, message)
            VALUES ('$mod_id', '$culp_id', '$id', '$summary', $type, $judgement, '$sender_id', $reason, '$message')";
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