<?php $path = $_SERVER['DOCUMENT_ROOT']; 

include $path . '/functions/.connect.php' ;

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(!session_id()) {
  session_start();
}

include $path . "/basic/menu.php";
include $path . '/functions/require/moderationHistory.php' ;
include $path . '/functions/validateSession.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quir | Moderation</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="stylesheet" href="/styles/profile.css"/>
</head>
<body>
    <?php generateMenu() ?>
    <?php generateProfileMenu() ?>

    <div class="container">
    <?php
        if(validateSession()) {
            $user_id = $_SESSION["user_id"];

            $sql = "SELECT username, handle, image_dir, posts, threads, clearance FROM users WHERE user_id = '$user_id' LIMIT 1";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $username = $row["username"];
            $handle = $row["handle"];
            $image_dir = $row["image_dir"];
            $posts = $row["posts"];
            $threads = $row["threads"];
            $clearance = $row["clearance"];

            ?>  
            <!-- DELETED THINGS -->
            <div class="deleted-posts">
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
                            <span class="deleted-datetime"><?= $post['deleted_datetime']; ?></span>
                            <span class="deleted-created"><?= $post['created']; ?></span>
                            <span class="deleted-thread">
                                <a href="/thread/<?= $post['slug']; ?>"><?= $post['name']; ?></a>
                            </span>
                            <span class="deleted-content"><?= $post['content']; ?></span>
                            <button class="restore" onclick="restorePost('<?= $post['post_id']; ?>')">restore</button>
                        </div>
                <?php }
                } else {
                    echo "No deleted posts";
                }
                ?>
        </div>
                <?php
                // MODERATION
                if($clearance > 0) {
                    $totalMod = (int)countModHistory();
                    $totalReport = (int)countReportHistory(false, $clearance);
                    ?>
                    Moderation History
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
                    
                    Report History
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


    <?php include $path . "/basic/footer.php"; ?>

    <script src="/scripts/profile.js"></script>
</body>
</html>