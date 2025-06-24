<?php

function getHistory(bool $report, int $page, int $clearance) {
    $path = $_SERVER['DOCUMENT_ROOT'];

    include $path . '/functions/.connect.php' ;

    // Get connection
    $conn = getConn();

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

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
            JOIN users c ON c.user_id = mh.culp_id";

    if($report) {
        $sql .= "\nWHERE mh.judgement < 2 AND mh.type < $clearance";
    } else {
        $sql .= "\nWHERE mh.judgement > 1";
    }

    $sql .= "\nORDER BY mh.created DESC
            LIMIT 50 OFFSET $offset";
    
    $result = $conn->query($sql);

    $data = [];
    $cache = [];
    while($row = $result->fetch_assoc()) {
        if(isset($cache[$row["id"]])) {
            if($row["type"] === "2") {
                if((int)$row["judgement"] > 3 && $cache[$row["id"]] > 3) {
                    $row["repeat"] = true;
                } else if((int)$row["judgement"] < 4 && $cache[$row["id"]] < 4) {
                    $row["repeat"] = true;
                } else {
                    $cache[$row["id"]] = (int)$row["judgement"];
                    $row["repeat"] = false;
                }
            } else {
                $row["repeat"] = true;
            }
        } else {
            $cache[$row["id"]] = (int)$row["judgement"];
            $row["repeat"] = false;
        }
        $data[] = $row;
    }
    return $data;
}

function getHistoryHTML(bool $report, int $page, int $clearance) {
    $data = getHistory($report, $page, $clearance);
    foreach($data as $row) {
        generateHTML($row, $clearance);
    }
}

function generateHTML($row, $clearance) {
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
        <?= generateButton($row['mod_id'], $row['culp_id'], $clearance, $row['culp_clearance'], $row['type'], $row['judgement'], $row['repeat']); ?>
    </div>
    <?php
}

function generateButton($mod_id, $culp_id, int $clearance, int $culp_clearance, int $type, int $judgement, bool $repeat) {
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
        if($culp_id === $user_id || $repeat) {
            $button .= "disabled>undo";
        } else if($type === 0 || ($type === 1 && $clearance > 1)) {
            if($judgement === 4) {
                $button .= "onclick=\"undo('$id', false)\">undo";
            } else {
                $button .= "onclick=\"undo('$id')\">undo";
            }
        } else if($type === 2) {
            // Deleted or restored
            if($judgement < 6 && $clearance > 2) {
                if($judgement === 4 || $judgement === 5) {
                    $button .= "onclick=\"undo('$id', false)\">undo";
                } else {
                    $button .= "onclick=\"undo('$id')\">undo";
                }
            // demotion
            } else if($judgement === 6 && $culp_clearance < $clearance && $clearance > 3) {
                $button .= "onclick=\"undo('$id')\">undo";
            // promotion
            } else if($judgement === 7 && $culp_clearance + 1 < $clearance && $clearance > 3) {
                $button .= "onclick=\"undo('$id')\">undo";
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