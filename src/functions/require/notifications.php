<?php
include $_SERVER['DOCUMENT_ROOT'] . '/functions/.connect.php';

function NewNotifCount(string $user_id = "") : array {
    if($user_id === "") {
        return [false, "args"];
    }

    $conn = getConn();

    $sql = "SELECT COUNT(*) AS total FROM `notifications` WHERE `receiver_id` = '$user_id' AND `read` = 0";
    $result = $conn->query($sql);
    return [true, (int)$result->fetch_assoc()["total"]];
}

function getNotifications(string $user_id = "") : array {
    if($user_id === "") {
        return [false, "args"];
    }

    $conn = getConn();

    $sql = "SELECT 
                n.type, 
                n.read,
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
            WHERE n.receiver_id = '$user_id'";

    $result = $conn->query($sql);

    return [true, $result];
}

function generateNotifsHTML(string $user_id = "") : string {
    $res = getNotifications($user_id);
    if(!$res[0]) {
        return "An error has occured";
    }

    // For type 0 (which is the only type for now)

    $html = "";
    
    for($res[1] as $item) {
        if($item["type"] == "0") {
            $html .= genForPost($item);
        }
    }
}

function genForPost($item) : string {
    $handle = $item["handle"];
    $username = $item["username"];
    $slug = $item["slug"];
    $name = $item["name"];
    return "<span>
                <a href=\"/user/$handle\">$username</a>
                posted on
                <a href=\"/thread/$slug\">$name</a>
            </span>";
}