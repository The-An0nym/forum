<?php
include $_SERVER['DOCUMENT_ROOT'] . '/functions/.connect.php';

function NewNotifCount(string $user_id = "") : array {
    if($user_id === "") {
        return ["args"];
    }

    $conn = getConn();

    $sql = "SELECT COUNT(*) AS total FROM `notifications` WHERE `receiver_id` = '$user_id' AND `read` = 0 AND `deleted` = 0";
    $result = $conn->query($sql);
    return ["pass", (int)$result->fetch_assoc()["total"]];
}

function getNotifications(string $user_id = "", int $page = 0) : array {
    if($user_id === "") {
        return ["args"];
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
                t.slug,
                t.posts
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

    return ["pass", $result];
}

function generateNotifsHTML(string $user_id = "", int $page = 0) : string {
    $res = getNotifications($user_id, $page);
    if($res[0] !== "pass") {
        return '<span id="error">An error has occured</span>';
    }

    $html = "";
    
    foreach($res[1] as $item) {
        switch($item["type"]) {
            case 0:
                $html .= genForPost($item);
                break;
            case 6:
                $html .= genForAuth($item, true); // Promotion
                break;
            case 7:
                $html .= genForAuth($item, false); // Demotion
                break;
            default:
                $html .= genErr($item);
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
    $page = ceil($item["posts"] / 20);
    return "<span class=\"notification-item post\">
                <span class=\"datetime\">$dt</span>
                <a href=\"/user/$handle\">$username</a>
                posted on
                <a href=\"/thread/$slug\">$name</a>
            </span>";
}

function genForAuth(array $item, bool $promote) : string {
    return "Del prom: todo" . $promote;
}

function genErr(array $item) : string {
    return "Error";
}

function markAsRead(string $user_id = "") : array {
    if($user_id === "") {
        return ["args"];
    }

    $res = NewNotifCount($user_id);

    if($res[0] !== "pass") {
        return [$res[1]];
    }

    if($res[1] === 0) {
        return ["pass"];
    }

    $conn = getConn();

    $sql = "UPDATE `notifications` SET `read` = 1 WHERE `read` = 0 AND receiver_id = '$user_id'";
    if($conn->query($sql) === FALSE) {
        return ["", "RN0"];
    }

    return ["pass"];
}

function setDelNotif(string $assoc_id = "", int $type = 10, bool $del = true) : array {
    if($assoc_id === "") {
        return ["args"];
    }

    if($type > 3 || $type < 0) {
        return ["args"];
    }

    $conn = getConn();

    if($del) {
        $delVal = 1;
    } else {
        $delVal = 0;
    }

    $sql = "UPDATE `notifications` SET deleted = $delVal WHERE `assoc_id` = '$assoc_id' AND `type` = $type";
    if($conn->query($sql) === FALSE) {
        return ["", "RN1"];
    }

    $r = rand(0, 99);
    if($r === 0) {
        $res = deleteOldNotifs();
        if($res[0] !== "pass") {
            return $res;
        }
    }

    return ["pass"];
}

function deleteOldNotifs() {
    $conn = getConn();

    $delDatetime = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')) - 60 * 60 * 24 * 60); // 60 days

    $sql = "DELETE FROM `notifications` WHERE `datetime` < '$delDatetime'";
    if($conn->query($sql) === FALSE) {
        return ["", "[RN2]"];
    }

    return ["pass"];
}