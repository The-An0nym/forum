<?php
include $_SERVER['DOCUMENT_ROOT'] . '/functions/.connect.php';

function NewNotifCount(string $user_id = "") : array {
    if($user_id === "") {
        return [false, "args"];
    }

    $conn = getConn();

    $sql = "SELECT COUNT(*) AS total FROM `notifications` WHERE `receiver_id` = '$user_id' AND `read` = 0 AND `deleted` = 0";
    $result = $conn->query($sql);
    return [true, (int)$result->fetch_assoc()["total"]];
}

function getNotifications(string $user_id = "", int $page = 0) : array {
    if($user_id === "") {
        return [false, "args"];
    }

    $conn = getConn();

    $offset = $page * 20;

    $sql = "SELECT 
                n.type, 
                n.read,
                n.datetime,
                u.handle, 
                u.username,
                t.name,
                t.slug
            FROM 
                notifications n
            JOIN 
                users u
            ON
                u.user_id = n.sender_id
            LEFT JOIN
                threads t
            ON
                t.id = n.thread_id
            WHERE n.receiver_id = '$user_id' AND n.deleted = 0
            ORDER BY n.datetime DESC
            LIMIT 20 OFFSET $offset";

    $result = $conn->query($sql);

    return [true, $result];
}

function generateNotifsHTML(string $user_id = "", int $page = 0) : string {
    $res = getNotifications($user_id, $page);
    if(!$res[0]) {
        return "An error has occured";
    }

    $html = "";
    
    foreach($res[1] as $item) {
        switch($item["type"]) {
            case 0:
                $html .= genForPost($item);
                break;
            case 1:
                $html .= genForDelPost($item);
                break;
            case 2:
                $html .= genForDelThread($item);
                break;
            case 3:
                $html .= genForAuth($item, true); // Promotion
                break;
            case 4:
                $html .= genForAuth($item, false); // Demotion
        }
    }

    return $html;
}

function genForPost(array $item) : string {
    $handle = $item["handle"];
    $username = $item["username"];
    $slug = $item["slug"];
    $name = $item["name"];
    $dt = $item["datetime"];
    return "<span class=\"notification-item post\">
                <span class=\"datetime\">$dt</span>
                <a href=\"/user/$handle\">$username</a>
                posted on
                <a href=\"/thread/$slug\">$name</a>
            </span>";
}

function genForDelPost(array $item) : string {
    return "Del post: todo";
}

function genForDelThread(array $item) : string {
    return "Del thread: todo";
}

function genForAuth(array $item, bool $promote) : string {
    return "Del prom: todo" . $promote;
}

function markAsRead(string $user_id = "") : array {
    if($user_id === "") {
        return [false, "args"];
    }

    $res = NewNotifCount($user_id);

    if(!$res[0]) {
        return [false, $res[1]];
    }

    if($res[1] === 0) {
        return [true];
    }

    $conn = getConn();

    $sql = "UPDATE `notifications` SET `read` = 1 WHERE `read` = 0 AND receiver_id = '$user_id'";
    if($conn->query($sql) === FALSE) {
        return [false, "", "RN0"];
    }

    return [true];
}

function setDelNotif(string $assoc_id = "", int $type = 10, bool $del = true) : array {
    if($assoc_id === "") {
        return [false, "args"];
    }

    if($type > 3 || $type < 0) {
        return [false, "args"];
    }

    $conn = getConn();

    if($del) {
        $delVal = 1;
    } else {
        $delVal = 0;
    }

    $sql = "UPDATE `notifications` SET deleted = $delVal WHERE `assoc_id` = '$assoc_id' AND `type` = $type";
    if($conn->query($sql) === FALSE) {
        return [false, "", "RN1"];
    }

    return [true];
}