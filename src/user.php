<?php 
$path = $_SERVER['DOCUMENT_ROOT']; 
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/time.php' ;
require_once $path . '/functions/validateSession.php';
require_once $path . "/assets/menu.php";
require_once $path . "/assets/generateSVG.php";
require_once $path . '/functions/external/parsedown/parsedown.php';

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

if(validateSession()) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT `clearance`, `handle` FROM `users` WHERE `user_id` = '$user_id'";
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

$sql = "SELECT `username`, `user_id`, `about_me`, `image_dir`, `posts`, `threads`, `created`, `clearance` FROM `users` WHERE `handle` = '$handle' AND `deleted` = 0 LIMIT 1";
$result = $conn->query($sql);
if($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $username = $row["username"];
    $user_id= $row["user_id"];
    $about_me = $row["about_me"];
    $image_dir = $row["image_dir"];
    $posts = $row["posts"];
    $threads = $row["threads"];
    $user_created = $row["created"];
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
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="stylesheet" href="/styles/user.css" />
    <link rel="stylesheet" href="/styles/markdown-formatting.css" />
    <link rel="stylesheet" href="/styles/tab-menu.css" />
</head>
<body>
    <?php generateMenu() ?>

    <?php if($load) {?>

    <div id="global">
        <div id="user-info">
            <img class="user-image" src="/images/profiles/<?= $image_dir; ?>">
            <span class="username"><?= $username ?></span>
            <span class="handle">@<?= $handle ?></span>
            <span class="joined">Member since <?= dateStamp($user_created) ?></span>
            <span class="auth">
                <?php
                    for($i = 0; $i < 5; $i++) {
                        echo generateStar($user_clearance > $i);
                    }
                ?>
            </span>
            <span class="user-about-me"><?= $about_me ?></span>
            <span class="user-stats">
                <span class="posts">Posts: <?= $posts ?></span>
                <span class="threads">Threads: <?= $threads ?></span>
            </span>
            <?php 
            if($clearance >= 0 && $user_clearance <= $clearance) {
                echo '<button class="moderation" onclick="createReport(2, \'' . $user_id . '\')">Report</button>';
            }
            if($clearance >= 4 && $user_clearance < $clearance && $user_clearance > 0) {
                echo '<button class="moderation" onclick="createModeration(\'demote ' . $username . '\', demoteUser, \'' . $user_id .'\')">Demote User</button>';
            }
            if($clearance >= 4 && $user_clearance < ($clearance - 1)) {
                echo '<button class="moderation" onclick="createModeration(\'promote ' . $username . '\', promoteUser, \'' . $user_id .'\')">Promote User</button>';
            }
            if($clearance >= 3 && $user_clearance < $clearance) {
                echo '<button class="moderation danger-button" onclick="setupBan(\'' . $user_id .'\')">Ban</button>';
            }
            ?>
        </div>

        <div id="history">
            <div id="tab-menu-bar">
                <span class="menu-tab selected" onclick="switchTab(0)">Posts</span>
                <span class="menu-tab" onclick="switchTab(1)">Threads</span>
            </div>
        
        <!-- POST HISTORY -->
        <div class="tab-content history-block">
        <?php
        $sql = "SELECT p.content, p.created, t.name, t.slug FROM posts p 
                LEFT JOIN threads t ON t.id = p.thread_id
                WHERE p.deleted = 0 AND t.deleted = 0 AND p.user_id = '$user_id' 
                ORDER BY p.created DESC
                LIMIT 10";
        $result = $conn->query($sql);

        $Parsedown = new Parsedown();
        $Parsedown->setSafeMode(true);
        $Parsedown->setBreaksEnabled(true);

        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $content = $Parsedown->text(htmlspecialchars_decode($row["content"]));
                ?>
            <span class="post history-item">
                <span class="datetime"><?= timeAgo($row["created"]); ?></span>
                <span class="thread">
                    <a href="/thread/<?= $row['slug']; ?>"><?= $row["name"]; ?></a>
                </span>
                <span class="content"><?= $content ?></span>
            </span>
            <?php }
        } else {
            echo "No posts yet...";
        }
        ?>
        </div>
        
        <!-- THREAD HISTORY -->
        <div class="tab-content history-block" style="display: none;">
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
                <span class="datetime"><?= timeAgo($row["created"]); ?></span>
                <span class="topic">
                    <a href="/topic/<?= $row['cat_slug']; ?>"><?= $row["cat_name"]; ?></a>
                </span>
                <span class="thread">
                    &gt; <a href="/thread/<?= $row['slug']; ?>"><?= $row["name"]; ?></a>
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

    <?php require_once $path . "/assets/footer.php"; ?>
</body>
</html>