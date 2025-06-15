<?php

function getHistory(int $page) {
    $path = $_SERVER['DOCUMENT_ROOT']; 

    include $path . '/functions/.connect.php' ;

    // Get connection
    $conn = getConn();

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $offset = $page * 20;

    $sql = "SELECT * FROM mod_history";
    
    $result = $conn->query($sql);

    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getHistoryHTML($page, $clearance) {
    $data = getHistory($page);
    return $data;
    foreach($data as $row) {
        if($row["type"] == 0) {
            typePostHTML($row);
        } else if($row["type"] == 1) {
            typeThreadHTML($row, $clearance);
        } else if($row["type"] == 2) {
            typeUserHTML($row, $clearance);
        }
    }
}

function typePostHTML($row) {
    $judgement = judge($row["judgement"]);
    $reason = reason($row["reason"]);

    ?>
    <div class="post-history">
        <span class="datetime-history"><?= $row["datetime"]; ?></span>
        <span class="creator-username">
            <a href="/user/<?= $row["direct_handle"]; ?>"><?= $row["direct_username"]; ?></a>
        </span>
        <span class="content-history"> <?= $row["direct_name"]; ?></span>
        <span class="sender-username">
            <?= $judgement; ?> by
            <a href="/user/<?= $row["handle"]; ?>"><?= $row["username"]; ?></a>
        </span>
        <span class="reason-history"> <?= $reason; ?></span>
        <span class="message-history"> <?= $row["message"]; ?></span>
        <button class="undo-history">Undo</span>
    </div>
<?php
}

function typeThreadHTML($row, $clearance) {
    $judgement = judge($row["judgement"]);
    $reason = reason($row["reason"]);

    ?>
    <div class="thread-history">
        <span class="datetime-history"><?= $row["datetime"]; ?></span>
        <span class="creator-username">
            <a href="/user/<?= $row["direct_handle"]; ?>"><?= $row["direct_username"]; ?></a>
        </span>
        <span class="thread-name">
            <a href="/thread/<?= $row["direct_slug"]; ?>"><?= $row["direct_name"]; ?></a>
        </span>
        <span class="sender-username">
            <?= $judgement; ?> by
            <a href="/user/<?= $row["handle"]; ?>"><?= $row["username"]; ?></a>
        </span>
        <span class="reason-history"> <?= $reason; ?></span>
        <span class="message-history"> <?= $row["message"]; ?></span>
        <?php if($clearance > 1) { echo '<button class="undo-history">Undo</span>';} ?>
    </div>
<?php
}

function typeUserHTML($row, $clearance) {
    $judgement = judge($row["judgement"]);
    $reason = reason($row["reason"]);
    ?>
    <div class="user-history">
        <span class="datetime-history"><?= $row["datetime"]; ?></span>
        <span class="creator-username">
            <a href="/user/<?= $row["direct_handle"]; ?>"><?= $row["direct_username"]; ?></a>
        </span>
        <span class="sender-username">
            <?= $judgement; ?> by
            <a href="/user/<?= $row["handle"]; ?>"><?= $row["username"]; ?></a>
        </span>
        <span class="reason-history"> <?= $reason; ?></span>
        <span class="message-history"> <?= $row["message"]; ?></span>
        <?php if(($clearance > 2 && $judgement < 2) || $clearance > 3) { 
            echo '<button class="undo-history">Undo</span>';
        } ?>
    </div>
<?php
}

function judge($i) {
    return ["deleted", "restored", "demoted", "promoted"][$i];
}

function reason($i) {
    return ["Spam", "Inappropriate", "Copyright", "Other"][$i];
}