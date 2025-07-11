<?php

function createHistory(int $type, int $judgement, $id, $sender_id, int $reason, string $message) : array {
    $path = $_SERVER['DOCUMENT_ROOT'];
    include $path . '/functions/.connect.php' ;
    $conn = getConn();
    
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
        $result = $conn->query($sql);
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
        return ["", "[M0]"];
    }

    return ["pass"];
}

function createReport(int $type, $id, $user_id, int $reason, string $message) : array {
    $path = $_SERVER['DOCUMENT_ROOT'];
    include $path . '/functions/.connect.php' ;
    $conn = getConn();

    $sql = "SELECT NULL FROM mod_history WHERE id = '$id' AND sender_id = '$user_id' AND judgement < 2";

    $result = $conn->query($sql);
    if($result->num_rows !== 0) {
        return ["tReport"];
    }
    
    $err = createHistory($type, 0, $id, $user_id, $reason, $message);
    if($err[0] !== "pass") {
        return $err;
    }
    
    return ["pass"];
}

// DELETE THINGS

function deletePost($id, int $cause, bool $rest) : array {
    $path = $_SERVER['DOCUMENT_ROOT'];
    include $path . '/functions/.connect.php' ;
    $conn = getConn();

    if($rest) {
        $op = "& ~";
    } else {
        $op = "|";
    }

    $dtime = date('Y-m-d H:i:s');
    // Soft delete post
    $sql = "UPDATE posts SET deleted = deleted $op $cause, deleted_datetime = '$dtime' WHERE post_id = '$id'";
    if ($conn->query($sql) === FALSE) {
        return ["", "M1"];
    }

    return ["pass"];
}

function deleteThread($id, int $cause, bool $rest) : array {
    $path = $_SERVER['DOCUMENT_ROOT'];
    include $path . '/functions/.connect.php' ;
    $conn = getConn();

    if($rest) {
        $op = "& ~";
    } else {
        $op = "|";
    }

    $dtime = date('Y-m-d H:i:s');

    // (Soft) delete thread
    $sql = "UPDATE threads SET deleted = deleted $op $cause, deleted_datetime = '$dtime' WHERE id = '$id'";
    if ($conn->query($sql) === FALSE) {
        return ["", "[M2]"];
    }

    // (Soft) delete posts
    $sql = "UPDATE posts SET deleted = deleted $op $cause, deleted_datetime = '$dtime' WHERE thread_id = '$id'";
    if ($conn->query($sql) === FALSE) {
        return ["", "[M3]"];
    }

    return ["pass"];
}

function deleteAccount($id, bool $rest, bool $del_threads) : array {
    $path = $_SERVER['DOCUMENT_ROOT'];
    include $path . '/functions/.connect.php' ;
    $conn = getConn();

    if($rest) {
        $op = "& ~";
    } else {
        $op = "|";
    }

    $dtime = date('Y-m-d H:i:s');

    // Flag user as banned or self-deleted
    $sql = "UPDATE users SET deleted = deleted $op 8, deleted_datetime = '$dtime' WHERE user_id = '$id'";
    if ($conn->query($sql) === FALSE) {
        return ["", "M4"];
    }

    if($del_threads) {
        // Soft delete threads
        $sql = "UPDATE threads SET deleted = deleted $op 8, deleted_datetime = '$dtime' WHERE user_id = '$id'";
        if ($conn->query($sql) === FALSE) {
            return ["", "M5"];
        }
    }

    // Soft delete posts
    $sql = "UPDATE posts SET deleted = deleted $op 8, deleted_datetime = '$dtime' WHERE user_id = '$id'";
    if ($conn->query($sql) === FALSE) {
        return ["", "M6"];
    }

    // Delete session
    $sql = "DELETE FROM sessions WHERE user_id = '$id'";
    if ($conn->query($sql) === FALSE) {
        return ["", "M7"];
    }

    return ["pass"];
}