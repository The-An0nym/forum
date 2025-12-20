<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/functions/.connect.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/functions/time.php';

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
                g.latest_time AS `datetime`,
                u.handle,
                u.username,
                t.name,
                t.slug,
                t.posts,
                g.usercount,
                g.notifscount
            FROM
                (
                SELECT
                    `type`,
                    `read`,
                    COALESCE(thread_id, notification_id) AS `group_key`,
                    MAX(datetime) AS `latest_time`,
                    COUNT(DISTINCT `sender_id`) AS `usercount`,
                    COUNT(*) AS `notifscount`
                FROM
                    `notifications`
                WHERE
                    `receiver_id` = '$user_id' AND `deleted` = 0
                GROUP BY 
                    `type`,
                    `read`,
                    `group_key`
            ) g
            JOIN notifications n ON 
                n.type = g.type AND n.read = g.read 
                AND COALESCE(n.thread_id, n.notification_id) = g.group_key AND n.datetime = g.latest_time
                AND n.notification_id =
                    (
                        SELECT MAX(notification_id)
                        FROM notifications n2
                        WHERE n2.type = g.type
                            AND n2.read = g.read
                            AND COALESCE(n2.thread_id, n2.notification_id) = g.group_key
                            AND n2.datetime = g.latest_time
                    )
            LEFT JOIN users u ON
                u.user_id = n.sender_id
            LEFT JOIN threads t ON
                t.id = n.thread_id
            ORDER BY
                g.latest_time
            DESC
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
                $html .= genForAuth($item, false); // Demotion
                break;
            case 7:
                $html .= genForAuth($item, true); // Promotion
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
    $dt = timeAgo($item["datetime"]);
    $page = ceil(($item["posts"] - $item["notifscount"] + 1) / 20);

    $usersText = "<a href=\"/user/$handle\">$username</a>";
    if($item["usercount"] !== '1') {
        $usersText .= " and " . ($item["usercount"] - 1) . " other";
        if($item["usercount"] !== '2') {
            $usersText .= "s";
        }
    }

    $unread = "";
    if($item["read"] === '0') {
        $unread = "<span class=\"notification-count\">" . $item['notifscount'] . " notification";
        if($item['notifscount'] !== '1') {
            $unread .= "s";
        }
        $unread .= "</span>";
    }

    return "<span class=\"notification-item post\">
                $unread
                <span class=\"notification-datetime\">$dt</span>
                <a class=\"/notification-title\" href=\"/thread/$slug/$page\">$name</a>
                <span class=\"notification-initiators\">$usersText</span>
            </span>";
}

function genForAuth(array $item, bool $promote) : string {
    $dt = timeAgo($item["datetime"]);

    $unread = "";
    if($item["read"] == 0) {
        $unread = "<span class=\"notification-count\">1 notification</span>";
    }

    $title = "You have been " . ($promote ? "promoted" : "demoted");

    $initiator = "<a href=\"/user/" . $item['handle'] . "\">" . $item['username'] . "</a>";

    // TODO Promotion and demotion
    return "<span class=\"notification-item demot\">
                $unread
                <span class=\"notification-datetime\">$dt</span>
                <span class=\"/notification-title\">$title</span>
                <span class=\"notification-initiators\">By $initiator</span>
            </span>";
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

    $sql = "UPDATE `notifications` SET `read` = 1 WHERE `read` = 0 AND `deleted` = 0 AND receiver_id = '$user_id'";
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