<?php

function createHistory(int $type, int $judgement, $id, $sender_id, int $reason, string $message) {
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

function createReport(int $type, $id, $user_id, int $reason, string $message) {
    $path = $_SERVER['DOCUMENT_ROOT'];
    include $path . '/functions/.connect.php' ;
    $conn = getConn();
    
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

// DELETE THINGS

function deletePost($id, int $cause, bool $rest) {
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
        echo "An error has occured while deleting this post";
    }
}

function deleteThread($id, int $cause, bool $rest) {
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
        echo "An error has occured while deleting this thread";
    }

    // (Soft) delete posts
    $sql = "UPDATE posts SET deleted = deleted $op $cause, deleted_datetime = '$dtime' WHERE thread_id = '$id'";
    if ($conn->query($sql) === FALSE) {
        echo "An error has occured while deleting the posts of this thread";
    }
}

function deleteAccount($id, int $cause, bool $del_threads, bool $rest) {
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
        echo "An error has occured while deleting this account";
    }

    if($del_threads) {
        // Soft delete threads
        $sql = "UPDATE threads SET deleted = deleted $op 8, deleted_datetime = '$dtime' WHERE user_id = '$id'";
        if ($conn->query($sql) === FALSE) {
            echo "ERROR: Please try again later [BU6]";
        }
    }

    // Soft delete posts
    $sql = "UPDATE posts SET deleted = deleted $op 8, deleted_datetime = '$dtime' WHERE user_id = '$id'";
    if ($conn->query($sql) === FALSE) {
        echo "ERROR: Please try again later [BU7]";
    }

    // Delete session
    $sql = "DELETE FROM sessions WHERE user_id = '$id'";
    if ($conn->query($sql) === FALSE) {
        echo "ERROR: Please try again later [BU8]";
    }
}