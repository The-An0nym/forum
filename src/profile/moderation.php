<?php $path = $_SERVER['DOCUMENT_ROOT']; 

require_once $path . '/functions/.connect.php' ;

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(!session_id()) {
  session_start();
}

require_once $path . "/assets/menu.php";
require_once $path . '/functions/require/moderationHistory.php' ;
require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/time.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quir | Moderation</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="stylesheet" href="/styles/profile/moderation.css"/>
    <link rel="stylesheet" href="/styles/tab-menu.css"/>

</head>
<body>
    <?php generateMenu() ?>

    <div id="global">
    <?php
        if(validateSession()) {
            $user_id = $_SESSION["user_id"];

            $sql = "SELECT `username`, `handle`, `image_dir`, `posts`, `threads`, `clearance` FROM `users` WHERE `user_id` = '$user_id' LIMIT 1";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $username = $row["username"];
            $handle = $row["handle"];
            $image_dir = $row["image_dir"];
            $posts = $row["posts"];
            $threads = $row["threads"];
            $clearance = $row["clearance"];
    ?>

            <div id="tab-menu-bar">
                <span class="menu-tab selected" id="sessions-tab" onclick="switchTab(0)">Session</span>
                <span class="menu-tab" id="sessions-tab" onclick="switchTab(1)">Deleted posts</span>
                <?php 
                    if($clearance > 0) { ?>
                    <span class="menu-tab" id="moderation-tab" onclick="switchTab(2)">Moderation</span>
                    <span class="menu-tab" id="report-tab" onclick="switchTab(3)">Reports</span>
                <?php
                    }
                ?>
            </div>

            <!-- SESSIONS -->
            <div id="sessions" class="tab-content">
            <?php

            $sql = "SELECT `ip`, `user_agent`, `datetime`, `session_id` FROM `sessions` WHERE `user_id` = '$user_id'";
            $result = $conn->query($sql);

                $session_id = $_SESSION["session_id"];
                while($row = $result->fetch_assoc()) {
                    $row_session_id = $row["session_id"];
                    $revokeButton = "<button class=\"delete-session\" onclick=\"deleteSession('$row_session_id')\">Revoke session</button>";
                    
                    // Cannot delete own session
                    if($row_session_id === $session_id) {
                        $revokeButton = "<button class=\"delete-session disabled\">Revoke session</button>";
                    }
                    ?>
                    <span class="session-item" id="<?= $row_session_id; ?>">
                        <span class="user-agent"><?= getDeviceSVG($row["user_agent"]); ?></span>
                        <span class="location">unknown</span>
                        <span class="ip"><?= $row["ip"]; ?></span>
                        <span class="session-datetime"><?= timeAgo($row["datetime"]); ?></span>
                        <?= $revokeButton; ?>
                    </span>
                <?php
                }

                ?>
            </div>

            <!-- DELETED POSTS -->
            <div id="deleted-posts" class="tab-content" style="display: none;">
                <?php 
                $sql = "SELECT 
                            p.content, 
                            p.created, 
                            p.deleted_datetime, 
                            p.post_id,
                            t.slug, 
                            t.name
                        FROM 
                            posts p 
                        INNER JOIN 
                            threads t
                        ON 
                            t.id = p.thread_id
                        WHERE 
                            p.deleted = 1 AND t.deleted = 0 AND p.user_id = '$user_id'
                        ORDER BY p.deleted_datetime DESC";
                $result = $conn->query($sql);
                if($result->num_rows > 0) {
                    while($post = $result->fetch_assoc()) {?>
                        <div class="deleted-post">
                            <span class="deleted-post-data">
                                <a class="deleted-thread" href="/thread/<?= $post['slug']; ?>"><?= $post['name']; ?></a>
                                <span class="deleted-content"><?= $post['content']; ?></span>
                                <span class="deleted-created"><?= dateStamp($post['created']); ?></span>
                            </span>
                            <span class="deleted-post-mod">
                                <span class="deleted-datetime"><?= dateStamp($post['deleted_datetime']); ?></span>
                                <button class="restore" onclick="restorePost('<?= $post['post_id']; ?>')">restore</button>
                            <span>
                        </div>
                <?php }
                } else {
                    echo "<span class=\"no-results\">No deleted posts</span>"; // TODO langauge support
                }
                ?>
            </div>
            <?php
            // MODERATION
            if($clearance > 0) {
                $totalMod = (int)countModHistory();
                $totalReport = (int)countReportHistory(false, $clearance);
                ?>
                <div class="tab-content" style="display: none;">
                    <div id="mod-filter">
                        <input id="mod-sender" placeholder="sender handle">
                        <input id="mod-culp" placeholder="culprit handle">
                        <input id="mod-id" placeholder="id">
                        <select id="mod-type">
                            <option value="">All</option>
                            <option value="0">Post</option>
                            <option value="1">Thread</option>
                            <option value="2">User</option>
                        </select>
                        <label for="mod-sort">Reverse Order</label>
                        <input id="mod-sort" type="checkbox">
                        <button onclick="getModerationHistory()">Filter</button>
                        <span id="mod-result"><?= $totalMod; ?></span> results
                    </div>
                    <div id="moderation-header">
                        <div>Date</div>
                        <div>Action</div>
                        <div>Summary</div>
                        <div>Reason</div>
                        <div>Message</div>
                        <div>Action</div>
                    </div>
                    <div id="moderation-history">
                    <?= getHistoryHTML(false, 0, $clearance, []); ?>
                    </div>
            </div>
            <div class="tab-content" style="display: none;">                   
                    <div id="report-filter">
                        <input id="report-sender" placeholder="sender handle">
                        <input id="report-culp" placeholder="culprit handle">
                        <input id="report-id" placeholder="id">
                        <select id="report-type">
                            <option value="">All</option>
                            <option value="0">Post</option>
                            <option value="1">Thread</option>
                            <option value="2">User</option>
                        </select>
                        <label for="report-sort">Reverse Order</label>
                        <input id="report-sort" type="checkbox">
                        <button onclick="getModerationHistory(0, true)">Filter</button>
                        <span id="report-result"><?= $totalReport; ?></span> results
                        <span id="report-unread"><?= countReportHistory(true, $clearance); ?></span> unread
                    </div>
                    <div id="report-header">
                        <div>Date</div>
                        <div>Action</div>
                        <div>Summary</div>
                        <div>Reason</div>
                        <div>Message</div>
                        <div>Action</div>
                    </div>
                    <div id="report-history">
                    <?= getHistoryHTML(true, 0, $clearance, []); ?>
                    </div>
            </div>
            <script src="/scripts/moderation.js"></script>
            <script>
                let modTotalPage = <?= $totalMod; ?>;
                let modPage = 0;
                let reportTotalPage = <?= $totalReport; ?>;
                let reportPage = 0;
                paginateMod();
                paginateReport()
            </script>
            <?php } ?>
            <script>
                const username = "<?= $username; ?>";
                const handle = "<?= $handle; ?>"
                const image_dir = "<?= $image_dir; ?>";
            </script>
            <?php
            } else {
                echo "Please Log in or Sign up to continue...";
            }        
        ?>
    </div>


    <?php require_once $path . "/assets/footer.php"; ?>

    <script src="/scripts/profile.js"></script>
</body>
</html>