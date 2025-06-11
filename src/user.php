<?php 
$path = $_SERVER['DOCUMENT_ROOT']; 
include $path . '/functions/.connect.php' ;
include $path . "/basic/menu.php";

if(!session_id()) {
    session_start();
} 

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if(isset($_GET["s"])) {
    $handle = $_GET["s"];
} else {
    $handle = "";
}

$clearance = -1;

if(include($path . '/functions/validateSession.php')) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT clearance, handle FROM users WHERE user_id = '$user_id'";
    $result = $conn->query($sql);
    if($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if($row['handle'] === $handle) {
            $clearance = -1;
        } else {
            $clearance = $row['clearance'];
        }
    }
}

$load = true;

$sql = "SELECT username, user_id, image_dir, posts, threads, clearance FROM users WHERE handle = '$handle' AND deleted = 0 LIMIT 1";
$result = $conn->query($sql);
if($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $username = $row["username"];
    $user_id= $row["user_id"];
    $image_dir = $row["image_dir"];
    $posts = $row["posts"];
    $threads = $row["threads"];
    $user_clearance = $row["clearance"];
} else {
    $load = false;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quir | User</title>
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="stylesheet" href="/styles/user.css" />
</head>
<body>
    <?php generateMenu([]) ?>

    <?php if($load) {?>

    <div id="global">

        <div id="user-info">
            <img class="user-image" src="/images/profiles/<?= $image_dir; ?>">
            <span class="username"><?= $username ?></span>
            <span class="handle">@<?= $handle ?></span>
            <span class="posts">Posts: <?= $posts ?></span>
            <span class="threads">Threads: <?= $threads ?></span>
            <?php 
            if($clearance >= 0 && $user_clearance <= $clearance) {
                echo '<button class="moderation" onclick="createReport(2, \'' . $user_id . '\')">Report</button>';
            }
            if($clearance >= 3 && $user_clearance < $clearance) {
                echo '<button class="moderation" onclick="createModeration(\'ban ' . $username . '\', banUser, \'' . $user_id .'\')">Ban</button>';
            }
            if($clearance >= 4 && $user_clearance < $clearance && $user_clearance > 0) {
                echo '<button class="moderation" onclick="createModeration(\'demote ' . $username . '\', demoteUser, \'' . $user_id .'\')">Demote User</button>';
            }
            if($clearance >= 4 && $user_clearance < ($clearance - 1)) {
                echo '<button class="moderation" onclick="createModeration(\'promote ' . $username . '\', promoteUser, \'' . $user_id .'\')">Promote User</button>';
            }?>
        </div>

        <div id="history">

        Post History:
        <div id="post-history" class="history-block">
        <?php
        $sql = "SELECT p.content, p.created, t.name, t.slug FROM posts p 
                LEFT JOIN threads t ON t.id = p.thread_id
                WHERE p.deleted = 0 AND t.deleted = 0 AND p.user_id = '$user_id' 
                ORDER BY p.created DESC
                LIMIT 10";
        $result = $conn->query($sql);
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {?>
            <span class="post history-item">
                <span class="date"><?= $row["created"]; ?></span>
                <span class="thread">
                    <a href="/thread/<?= $row['slug']; ?>"><?= $row["name"]; ?></a>
                </span>
                <span class="content"><?= $row["content"]; ?></span>
            </span>
            <?php }
        } else {
            echo "No posts yet...";
        }
        ?>
        </div>
        
        Thread History:
        <div id="thread-history" class="history-block">
        <?php
        $sql = "SELECT 
                    t.name, 
                    t.slug, 
                    t.created, 
                    c.name AS cat_name, 
                    c.slug AS cat_slug 
                FROM threads t
                LEFT JOIN categories c ON c.id = t.category_id
                WHERE t.deleted = 0 AND t.user_id = '$user_id' 
                ORDER BY t.created DESC
                LIMIT 10";
        $result = $conn->query($sql);
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {?>
            <span class="thread history-item">
                <span class="date"><?= $row["created"]; ?></span>
                <span class="topic">
                    <a href="/topic/<?= $row['cat_slug']; ?>"><?= $row["cat_name"]; ?></a>
                </span>
                <span class="thread">
                    <a href="/thread/<?= $row['slug']; ?>"><?= $row["name"]; ?></a>
                </span>
            </span>
            <?php }
        } else {
            echo "No threads yet...";
        }
        ?>
        </div>

        </div>

    </div>

    <script> 
        const handle = "<?= $handle; ?>";
    </script>
    <script src="/scripts/user.js"></script>
    <?php } ?>
    <script src="/scripts/main.js"></script>

    <?php include $path . "/basic/footer.php"; ?>
</body>
</html>