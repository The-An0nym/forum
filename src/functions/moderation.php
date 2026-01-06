<?php

$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;

function createHistory(int $type, int $judgement, string $id, string $sender_id, int $reason, string $message) : array {
    $conn = getConn();
    
    $mod_id = uniqid(rand(), true);

    $summary = "";
    $culp_id = "";

    if($type === 0) {
        $sql = "SELECT `content`, `user_id` FROM `posts` WHERE `post_id` = '$id'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $summary = $row["content"];
        $culp_id = $row["user_id"];
    } else if($type === 1) {
        $sql = "SELECT `name`, `user_id` FROM `threads` WHERE `id` = '$id'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $summary = $row["name"];
        $culp_id = $row["user_id"];
    } else if($type === 2) {
        $culp_id = $id;
    }

    $summary = substr($summary, 0, 64);

    $sql = "INSERT INTO `mod_history` (`mod_id`, `culp_id`, `id`, `summary`, `type`, `judgement`, `sender_id`, `reason`, `message`)
            VALUES ('$mod_id', '$culp_id', '$id', '$summary', $type, $judgement, '$sender_id', $reason, '$message')";
    if ($conn->query($sql) === FALSE) {
        return ["", "[M0]"];
    }

    if($judgement < 2) {
        return ["pass"]; // Report
    }

    $res = handleNotifications($sender_id, $culp_id, $id, $mod_id, $type, $judgement);
    if($res[0] !== "pass") {
        return $res;
    }

    $r = rand(0, 99);
    if($r === 0) {
        $res = permaDeleteExpired();
        if($res[0] !== "pass") {
            return $res;
        }
    }

    return ["pass"];
}

function createReport(int $type, string $id, string $user_id, int $reason, string $message) : array {
    $conn = getConn();

    $sql = "SELECT NULL FROM `mod_history` WHERE `id` = '$id' AND `sender_id` = '$user_id' AND `judgement` < 2";

    $result = $conn->query($sql);
    if($result->num_rows !== 0) {
        return ["tReport"]; // Already reported this
    }
    
    $err = createHistory($type, 0, $id, $user_id, $reason, $message);
    if($err[0] !== "pass") {
        return $err;
    }
    
    return ["pass"];
}

function handleNotifications(string $sender_id, string $culp_id, string $id, string $mod_id, int $type, int $judgement) : array {
    if($type === 0) {
        return handlePostNotification($id, $judgement);
    } else if($type === 1) {
        return handleThreadNotification($id, $judgement);
    } else if($type === 2) {
        return handleAuthChangeNotification($culp_id, $sender_id, $mod_id, $judgement);
    }
    
    return ["args"];
}

function handlePostNotification(string $id, int $judgement = 0) : array {
    if($judgement !== 2 && $judgement !== 4) {
        return ["pass"]; // Invalid judgement
    }

    $conn = getConn();

    $sql = "SELECT `read` FROM `notifications` WHERE `assoc_id` = '$id'";
    $result = $conn->query($sql);
    if($result->num_rows === 0) {
        return ["pass"]; // No notifications found
    }

    if($judgement === 2) {
       $sql = "UPDATE `notifications` SET `deleted` = 1 WHERE `assoc_id` = '$id' AND `type` = 0";
    } else if($judgement === 4) {
       $sql = "UPDATE `notifications` SET `deleted` = 0 WHERE `assoc_id` = '$id' AND `type` = 0";
    }

    if($conn->query($sql) === FALSE) {
        return ["generic"];
    }

    return ["pass"];
}

function handleThreadNotification(string $id, int $judgement = 0) : array {
    if($judgement !== 2 && $judgement !== 4) {
        return ["pass"]; // Invalid judgement
    }

    $conn = getConn();

    $sql = "SELECT `read` FROM `notifications` WHERE `thread_id` = '$id'";
    $result = $conn->query($sql);
    if($result->num_rows === 0) {
        return ["pass"]; // No notifications found
    }

    if($judgement === 2) {
       $sql = "UPDATE `notifications` SET `deleted` = 1 WHERE `thread_id` = '$id'";
    } else if($judgement === 4) {
       $sql = "UPDATE `notifications` SET `deleted` = 0 WHERE `thread_id` = '$id'";
    }

    if($conn->query($sql) === FALSE) {
        return ["generic"];
    }

    return ["pass"];
}

function handleAuthChangeNotification(string $culp_id, string $sender_id, string $mod_id, int $judgement = 0) : array {
    if($judgement === 6) { // Demotion
        $searchType = 7; // Promotion
    } else if($judgement === 7) { // Promotion
        $searchType = 6; // Demotion
    } else {
        return ["pass"]; // Invalid judgement
    }

    $conn = getConn();

    $sql = "SELECT `read` FROM `notifications` WHERE `receiver_id` = '$culp_id' AND `type` = '$searchType' AND `deleted` = 0 ORDER BY `datetime` DESC LIMIT 1";
    $result = $conn->query($sql);
    if($result->num_rows === 0) {
        $read = 1; // Notification doesn't exist yet
    } else {
        $read = (int)$result->fetch_assoc()['read'];
    }

    if($read === 0) {
       $sql = "UPDATE `notifications` SET `deleted` = 1 WHERE `receiver_id` = '$culp_id' AND `type` = '$searchType' AND `deleted` = 0 ORDER BY `datetime` DESC LIMIT 1";
    } else {
        $notif_id = uniqid(rand(), true);
        $datetime = date("Y-m-d H:i:s");

        $sql = "INSERT INTO `notifications`(
                    `notification_id`,
                    `sender_id`,
                    `receiver_id`,
                    `type`,
                    `thread_id`,
                    `assoc_id`,
                    `datetime`
                )
                VALUES(
                    '$notif_id',
                    '$sender_id',
                    '$culp_id',
                    '$judgement',
                    NULL,
                    '$mod_id',
                    '$datetime'
                )";
    }

    if($conn->query($sql) === FALSE) {
        return ["generic"];
    }

    return ["pass"];
}


/* DELETE */

function deletePost(string $id, int $cause, bool $rest) : array {
    $conn = getConn();

    if($rest) {
        $op = "& ~";
    } else {
        $op = "|";
    }

    $dtime = date('Y-m-d H:i:s');
    // Soft delete post
    $sql = "UPDATE `posts` SET `deleted` = deleted $op $cause, `deleted_datetime` = '$dtime' WHERE `post_id` = '$id'";
    if ($conn->query($sql) === FALSE) {
        return ["", "M1"];
    }

    return ["pass"];
}

function deleteThread(string $id, int $cause, bool $rest) : array {
    $conn = getConn();

    if($rest) {
        $op = "& ~";
    } else {
        $op = "|";
    }

    $dtime = date('Y-m-d H:i:s');

    // (Soft) delete thread
    $sql = "UPDATE `threads` SET `deleted` = deleted $op $cause, `deleted_datetime` = '$dtime' WHERE `id` = '$id'";
    if ($conn->query($sql) === FALSE) {
        return ["", "[M2]"];
    }

    // (Soft) delete posts
    $sql = "UPDATE `posts` SET `deleted` = deleted $op $cause, `deleted_datetime` = '$dtime' WHERE `thread_id` = '$id'";
    if ($conn->query($sql) === FALSE) {
        return ["", "[M3]"];
    }

    return ["pass"];
}

function deleteAccount(string $id, bool $rest, bool $del_threads) : array {
    $conn = getConn();

    if($rest) {
        $op = "& ~";
    } else {
        $op = "|";
    }

    $dtime = date('Y-m-d H:i:s');

    // Flag user as banned or self-deleted
    $sql = "UPDATE `users` SET `deleted` = deleted $op 8, `deleted_datetime` = '$dtime' WHERE `user_id` = '$id'";
    if ($conn->query($sql) === FALSE) {
        return ["", "M4"];
    }

    if($del_threads) {
        // Soft delete threads
        $sql = "UPDATE `threads` SET `deleted` = deleted $op 8, `deleted_datetime` = '$dtime' WHERE `user_id` = '$id'";
        if ($conn->query($sql) === FALSE) {
            return ["", "M5"];
        }
    }

    // Soft delete posts
    $sql = "UPDATE `posts` SET `deleted` = deleted $op 8, `deleted_datetime` = '$dtime' WHERE `user_id` = '$id'";
    if ($conn->query($sql) === FALSE) {
        return ["", "M6"];
    }

    // Delete session
    $sql = "DELETE FROM `sessions` WHERE `user_id` = '$id'";
    if ($conn->query($sql) === FALSE) {
        return ["", "M7"];
    }

    return ["pass"];
}

/* PERMENANT DELETION */

function permaDeleteExpired() : array {
    $delDatetime = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')) - 60 * 60 * 24 * 60); // 60 days

    $sql = "DELETE FROM `posts` WHERE `deleted_datetime` < '$detlDatetime' AND `deleted` != 0";
    if($conn->query($sql) === FALSE) {
        return ["", "[M8]"];
    }

    $sql = "DELETE FROM `threads` WHERE `deleted_datetime` < '$detlDatetime' AND `deleted` != 0";
    if($conn->query($sql) === FALSE) {
        return ["", "[M9]"];
    }

    $sql = "DELETE FROM `users` WHERE `deleted_datetime` < '$detlDatetime' AND `deleted` != 0";
    if($conn->query($sql) === FALSE) {
        return ["", "[M10]"];
    }

    return ["pass"];
}