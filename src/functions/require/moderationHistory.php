<?php

function getModHistory(int $page, array $params) {
    $path = $_SERVER['DOCUMENT_ROOT'];

    include $path . '/functions/.connect.php' ;

    // Get connection
    $conn = getConn();

    $offset = $page * 50;

    $sql = "SELECT
                s.username AS sender_username,
                s.handle AS sender_handle,
                c.username AS culp_username,
                c.handle AS culp_handle,
                c.clearance AS culp_clearance,
                mh.culp_id,
                mh.mod_id,
                mh.id,
                mh.type,
                mh.judgement,
                mh.summary,
                mh.reason,
                mh.message,
                mh.created,
                IF(
                    mh.created = sub.max_created,
                    1,
                    0
                ) AS is_latest
            FROM
                mod_history mh
            JOIN users s ON
                s.user_id = mh.sender_id
            JOIN users c ON
                c.user_id = mh.culp_id
            LEFT JOIN(
                SELECT id,
                    TYPE,
                    MAX(created) AS max_created
                FROM
                    mod_history
                WHERE
                    judgement >= 2
                GROUP BY
                    id,
                    TYPE
            ) sub
            ON
                sub.id = mh.id AND sub.type = mh.type AND mh.created = sub.max_created
            WHERE judgement > 1";

    $sql .= filter($params);

    $sort = "DESC";
    if(isset($params["reverse"])) {
        if((bool)$params["reverse"]) {
            $sort = "ASC";
        }
    }

    $sql .= "\nORDER BY mh.created $sort
            LIMIT 50 OFFSET $offset";

    $result = $conn->query($sql);

    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getReportHistory(int $page, int $clearance, array $params) {
    $path = $_SERVER['DOCUMENT_ROOT'];

    include $path . '/functions/.connect.php' ;

    // Get connection
    $conn = getConn();

    $offset = $page * 50;

    $sql = "SELECT 
                s.username AS sender_username,
                s.handle AS sender_handle,
                c.username AS culp_username,
                c.handle AS culp_handle,
                c.clearance AS culp_clearance,
                mh.culp_id,
                mh.mod_id,
                mh.id,
                mh.type,
                mh.judgement,
                mh.summary,
                mh.reason,
                mh.message,
                mh.created
            FROM mod_history mh
            JOIN users s ON s.user_id = mh.sender_id
            JOIN users c ON c.user_id = mh.culp_id
            WHERE mh.judgement < 2 AND mh.type < $clearance";

    $sql .= filter($params);

    $sort = "DESC";
    if(isset($params["reverse"])) {
        if((bool)$params["reverse"]) {
            $sort = "ASC";
        }
    }

    $sql .= "\nORDER BY mh.created $sort
            LIMIT 50 OFFSET $offset";
    
    $result = $conn->query($sql);

    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
        
    }
    return $data;
}

function countModHistory(array $params = []) {
    $conn = getConn();

    $sql = "SELECT COUNT(*) AS count FROM mod_history mh
            JOIN users s ON s.user_id = mh.sender_id
            JOIN users c ON c.user_id = mh.culp_id
            WHERE judgement > 1";

    $sql .= filter($params);

    $result = $conn->query($sql);
    return (int)$result->fetch_assoc()["count"];
}

function countReportHistory(bool $unread = false, int $clearance = 0, array $params = []) {
    $conn = getConn();

    if($unread) {
        $symbol = "<";
    } else {
        $symbol = "<=";
    }

    $sql = "SELECT COUNT(*) AS count FROM mod_history mh
            JOIN users s ON s.user_id = mh.sender_id
            JOIN users c ON c.user_id = mh.culp_id
            WHERE judgement $symbol 1 AND type < $clearance";

    $sql .= filter($params);
    
    $result = $conn->query($sql);
    return (int)$result->fetch_assoc()["count"];
}

function filter(array $params = []) {
    $filt = "";
    if(isset($params["culp_handle"])) {
        $culp_handle = $params['culp_handle'];
        $filt .= " AND c.handle = '$culp_handle'";
    }
    if(isset($params["sender_handle"])) {
        $sender_handle = $params['sender_handle'];
        $filt .= " AND s.handle = '$sender_handle'";
    }
    if(isset($params["type"])) {
        $type = (int)$params['type'];
        $filt .= " AND mh.type = $type";
    }
    if(isset($params["id"])) {
        $id = $params['id'];
        $filt .= " AND mh.id = '$id'";
    }   
    return $filt; 
}

function getHistoryHTML(bool $report, int $page, int $clearance, array $params) {
    if($report) {
        $data = getReportHistory($page, $clearance, $params);
    } else {
        $data = getModHistory($page, $params);
    }

    foreach($data as $row) {
        generateHTML($row, $clearance, $report);
    }
}

function generateHTML($row, int $clearance, bool $report) {
    if($row["type"] == 0) {
        $type = "post";
    } else if($row["type"] == 1) {
        $type = "thread";
    } else if($row["type"] == 2) {
        $type = "user";
    }

    $judgement = judge($row["judgement"]);
    $reason = reason($row["reason"]);

    // For reports
    $read = "";
    if($row["judgement"] === "1") {
        $read = " read";
    }

    if(!$report) {
        if($row["is_latest"] === "1") {
            $is_latest = true;
        } else {
            $is_latest = false;
        }
    } else {
        $is_latest = false;
    }

    ?>
    <div class="history <?= $type; ?> <?= $read; ?>">
        <span class="datetime-history"><?= $row["created"]; ?></span>
        <span class="creator-username">
            <a href="/user/<?= $row["sender_handle"]; ?>"><?= $row["sender_username"]; ?></a>
            <?= $judgement; ?>
            <a href="/user/<?= $row["culp_handle"]; ?>"><?= $row["culp_username"]; ?></a>'s
            <?= $type; ?>
        </span>
        <span class="history-summary" onclick="showContent(<?= $row['type']; ?>, '<?= $row['id']; ?>')"> 
            <?= $row["summary"]; ?>
        </span>
        <span class="reason-history"> <?= $reason; ?></span>
        <span class="message-history"> <?= $row["message"]; ?></span>
        <?= generateButton($row['mod_id'], $row['culp_id'], $clearance, $row['culp_clearance'], $row['type'], $row['judgement'], $is_latest); ?>
    </div>
    <?php
}

function generateButton($mod_id, $culp_id, int $clearance, int $culp_clearance, int $type, int $judgement, bool $is_latest) {
    if(!session_id()) {
       session_start();
    } 
    
    $button = '<button ';

    $user_id = $_SESSION["user_id"];

    
    if($judgement < 2) {
        $button .= 'onclick="markReport(';
        if($judgement === 0) {
            $button .= "1, '$mod_id')\">Mark read";
        } else {
            $button .= "0, '$mod_id')\">Mark unread";
        }
    } else {
        if($culp_id === $user_id || !$is_latest) {
            $button .= "disabled>undo";
        } else if($type === 0 || ($type === 1 && $clearance > 1)) {
            if($judgement === 4) {
                $button .= "onclick=\"undo('$mod_id')\">undo";
            } else {
                $button .= "onclick=\"undo('$mod_id', false)\">undo";
            }
        } else if($type === 2) {
            // Deleted or restored
            if($judgement < 6 && $clearance > 3) {
                if($judgement === 4 || $judgement === 5) {
                    $button .= "onclick=\"undo('$mod_id')\">undo";
                } else {
                    $button .= "onclick=\"undo('$mod_id', false)\">undo";
                }
            // demotion
            } else if($judgement === 6 && $culp_clearance < $clearance && $clearance > 3) {
                $button .= "onclick=\"undo('$mod_id')\">undo";
            // promotion
            } else if($judgement === 7 && $culp_clearance + 1 < $clearance && $clearance > 3) {
                $button .= "onclick=\"undo('$mod_id')\">undo";
            } else {
                $button .= "disabled>undo";
            }
        } else {
            $button .= "disabled>undo";
        }
    }

    $button .= '</button>';
    return $button;
}

function judge($i) {
    return ["reported", "reported", "deleted", "deleted w threads", "restored", "restored w threads", "demoted", "promoted"][$i];
}

function reason($i) {
    return ["Spam", "Inappropriate", "Copyright", "Other", "Restored"][$i];
}

function buttonMarkRead($id) {
    echo '<button onclick="markReport(1, \'' . $id . '\')">Mark read</button>';
}

function buttonMarkUnread($id) {
    echo '<button onclick="markReport(0, \'' . $id . '\')">Mark unread</button>';
}