<?php

function getHistoryPosts() {
    return "SELECT 
                mh.username,
                mh.handle,
                mh.created, 
                mh.judgement, 
                mh.reason, 
                mh.message,
                0 AS type,
                mp.post_id AS id,
                p.content AS cont,
                p.username AS sender_username,
                p.handle AS sender_handle
            FROM 
                mod_history_posts mp
            JOIN (
                SELECT u.username, u.handle, mh.created, mh.judgement, mh.reason, mh.message, mh.mod_id FROM mod_history mh
                JOIN users u ON u.user_id = mh.sender_id
            ) mh
            ON mp.mod_id = mh.mod_id
            JOIN (
                SELECT u.username, u.handle, p.content, p.post_id FROM posts p
                JOIN users u ON u.user_id = p.user_id
            ) p
            ON p.post_id = mp.post_id";
}

function getHistoryThreads() {
    return "SELECT 
                mh.username, 
                mh.handle,
                mh.created, 
                mh.judgement, 
                mh.reason, 
                mh.message,
                1 AS type,
                mt.thread_id AS id,
                t.name AS cont,
                t.username AS sender_username,
                t.handle AS sender_handle
            FROM 
                mod_history_threads mt
            JOIN (
                SELECT u.username, u.handle, mh.created, mh.judgement, mh.reason, mh.message, mh.mod_id FROM mod_history mh
                JOIN users u ON u.user_id = mh.sender_id
            ) mh
            ON mt.mod_id = mh.mod_id
            JOIN (
                SELECT u.username, u.handle, t.name, t.id FROM threads t
                JOIN users u ON u.user_id = t.user_id
            ) t
            ON t.id = mt.thread_id";
}

function getHistoryUsers() {
    return "SELECT 
                mh.username, 
                mh.handle,
                mh.created, 
                mh.judgement, 
                mh.reason, 
                mh.message,
                2 AS type,
                mu.user_id AS id,
                u.username AS cont,
                u.username AS sender_username,
                u.handle AS sender_handle
            FROM 
                mod_history_users mu
            JOIN (
                SELECT u.username, u.handle, mh.created, mh.judgement, mh.reason, mh.message, mh.mod_id FROM mod_history mh
                JOIN users u ON u.user_id = mh.sender_id
            ) mh
            ON mu.mod_id = mh.mod_id
            JOIN users u ON u.user_id = mu.user_id";
}

function getHistory(bool $reports, int $page) {
    $path = $_SERVER['DOCUMENT_ROOT'];

    if($reports) {
        $judgement = 1;
    } else {
        $judgement = 6;
    }

    include $path . '/functions/.connect.php' ;

    // Get connection
    $conn = getConn();

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $offset = $page * 50;

    $sql = "(
                ". getHistoryPosts($judgement) . "
                WHERE mh.judgement < $judgement 
            ) UNION ALL (
                ". getHistoryThreads($judgement) ."
                WHERE mh.judgement < $judgement 
            ) UNION ALL (
                ". getHistoryUsers($judgement) ."
                WHERE mh.judgement < $judgement 
            )
            ORDER BY created DESC
            LIMIT 50 OFFSET $offset";
    
    $result = $conn->query($sql);

    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getHistoryHTML(bool $report, int $page, int $clearance) {
    $data = getHistory($report, $page);
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
        <span class="datetime-history"><?= $row["created"]; ?></span>
        <span class="creator-username">
            <a href="/user/<?= $row["sender_handle"]; ?>"><?= $row["sender_username"]; ?></a>
        </span>
        <span class="content-history"> <?= $row["cont"]; ?></span>
        <span class="sender-username">
            <?= $judgement; ?> by
            <a href="/user/<?= $row["handle"]; ?>"><?= $row["username"]; ?></a>
        </span>
        <span class="reason-history"> <?= $reason; ?></span>
        <span class="message-history"> <?= $row["message"]; ?></span>
        <?php if($row["judgement"] !== 0) { echo '<button class="undo-history">Undo</span>';} ?>
    </div>
<?php
}

function typeThreadHTML($row, $clearance) {
    $judgement = judge($row["judgement"]);
    $reason = reason($row["reason"]);

    ?>
    <div class="thread-history">
        <span class="datetime-history"><?= $row["created"]; ?></span>
        <span class="creator-username">
            <a href="/user/<?= $row["sender_handle"]; ?>"><?= $row["sender_username"]; ?></a>
        </span>
        <span class="thread-name"><?= $row["cont"] ?></span>
        <span class="sender-username">
            <?= $judgement; ?> by
            <a href="/user/<?= $row["handle"]; ?>"><?= $row["username"]; ?></a>
        </span>
        <span class="reason-history"> <?= $reason; ?></span>
        <span class="message-history"> <?= $row["message"]; ?></span>
        <?php if($clearance > 1 && $row["judgement"] !== 0) { echo '<button class="undo-history">Undo</span>';} ?>
    </div>
<?php
}

function typeUserHTML($row, $clearance) {
    $judgement = judge($row["judgement"]);
    $reason = reason($row["reason"]);
    ?>
    <div class="user-history">
        <span class="datetime-history"><?= $row["created"]; ?></span>
        <span class="creator-username">
            <a href="/user/<?= $row["sender_handle"]; ?>"><?= $row["sender_username"]; ?></a>
        </span>
        <span class="culprit-username"><?= $row["cont"]; ?></span>
        <span class="sender-username">
            <?= $judgement; ?> by
            <a href="/user/<?= $row["handle"]; ?>"><?= $row["username"]; ?></a>
        </span>
        <span class="reason-history"> <?= $reason; ?></span>
        <span class="message-history"> <?= $row["message"]; ?></span>
        <?php if((($clearance > 2 && $row["judgement"] < 3) || $clearance > 3) && $row["judgement"] !== 0) { 
            echo '<button class="undo-history">Undo</span>';
        } ?>
    </div>
<?php
}

function judge($i) {
    return ["reported", "deleted", "restored", "demoted", "promoted"][$i];
}

function reason($i) {
    return ["Spam", "Inappropriate", "Copyright", "Other"][$i];
}

/* UNCLEAN mysql statement for POSTS MOD HISTORY

SELECT * FROM mod_history_posts mp
JOIN (
    SELECT u.username, mh.created, mh.judgement, mh.reason, mh.message, mh.mod_id FROM mod_history mh
    JOIN users u ON u.user_id = mh.sender_id
) mh
ON mp.mod_id = mh.mod_id
JOIN (
    SELECT u.username, p.content, p.post_id FROM posts p
    JOIN users u ON u.user_id = p.user_id
) p
ON p.post_id = mp.post_id

*/

/* UNCLEAN mysql statement for THREAD MOD HISTORY

SELECT * FROM mod_history_threads mt
JOIN (
    SELECT u.username, mh.created, mh.judgement, mh.reason, mh.message, mh.mod_id FROM mod_history mh
    JOIN users u ON u.user_id = mh.sender_id
) mh
ON mt.mod_id = mh.mod_id
JOIN (
    SELECT u.username, t.name, t.id FROM threads t
    JOIN users u ON u.user_id = t.user_id
) t
ON t.id = mt.thread_id

*/

/* UNCLEAN mysql statement for USER MOD HISTORY

SELECT * FROM mod_history_users mu
JOIN (
    SELECT u.username, mh.created, mh.judgement, mh.reason, mh.message, mh.mod_id FROM mod_history mh
    JOIN users u ON u.user_id = mh.sender_id
) mh
ON mu.mod_id = mh.mod_id
JOIN users u ON u.user_id = mu.user_id

*/

/* JOINED with UNION STATEMENTS

(
    SELECT 
		mh.username,
    	mh.handle,
		mh.created, 
		mh.judgement, 
		mh.reason, 
		mh.message,
		0 AS type,
		mp.post_id AS id,
    	p.content AS cont,
        p.username AS sender_username,
    	p.handle AS sender_handle
	FROM 
		mod_history_posts mp
    JOIN (
        SELECT u.username, u.handle, mh.created, mh.judgement, mh.reason, mh.message, mh.mod_id FROM mod_history mh
        JOIN users u ON u.user_id = mh.sender_id
    ) mh
    ON mp.mod_id = mh.mod_id
    JOIN (
        SELECT u.username, u.handle, p.content, p.post_id FROM posts p
        JOIN users u ON u.user_id = p.user_id
    ) p
    ON p.post_id = mp.post_id
) UNION ALL (
    SELECT 
		mh.username, 
    	mh.handle,
		mh.created, 
		mh.judgement, 
		mh.reason, 
		mh.message,
		1 AS type,
		mt.thread_id AS id,
    	t.name AS cont,
    	t.username AS sender_username,
    	t.handle AS sender_handle
	FROM 
		mod_history_threads mt
    JOIN (
        SELECT u.username, u.handle, mh.created, mh.judgement, mh.reason, mh.message, mh.mod_id FROM mod_history mh
        JOIN users u ON u.user_id = mh.sender_id
    ) mh
    ON mt.mod_id = mh.mod_id
    JOIN (
        SELECT u.username, u.handle, t.name, t.id FROM threads t
        JOIN users u ON u.user_id = t.user_id
    ) t
    ON t.id = mt.thread_id
) UNION ALL (
    SELECT 
		mh.username, 
    	mh.handle,
		mh.created, 
		mh.judgement, 
		mh.reason, 
		mh.message,
		2 AS type,
		mu.user_id AS id,
    	u.username AS cont,
    	u.username AS sender_username,
    	u.handle AS sender_handle
	FROM 
		mod_history_users mu
    JOIN (
        SELECT u.username, u.handle, mh.created, mh.judgement, mh.reason, mh.message, mh.mod_id FROM mod_history mh
        JOIN users u ON u.user_id = mh.sender_id
    ) mh
    ON mu.mod_id = mh.mod_id
    JOIN users u ON u.user_id = mu.user_id
)
ORDER BY created DESC
LIMIT 50 OFFSET 0

*/