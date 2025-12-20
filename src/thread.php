<?php 
$path = $_SERVER['DOCUMENT_ROOT']; 

if(!session_id()) {
    session_start();
}

require_once $path . "/assets/menu.php";

// Initial threads load
require_once $path . "/functions/require/posts.php";
if(isset($_GET["s"])) {
    $slug = $_GET["s"];
} else {
    $slug = "";
}

$postCount = getPostCount($slug);

if(isset($_GET["p"])) {
    $page = min((int)$_GET["p"], ceil($postCount / 20));
} else {
    $page = 1;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quir | Thread</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="stylesheet" href="/styles/posts.css" />
</head>
<body>
    <?php generateMenu(); ?>

    <div id="global">
        <div id="menu-path">
            <?= generateMenuPath(2, $slug); ?>

        <?php
        $autoSub = "false";
        $subscribed = false;

        if(isset($_SESSION["user_id"])) {
            $user_id = $_SESSION["user_id"];

            require_once $path . "/functions/.connect.php";

            $conn = getConn();

            $sql = "SELECT s.subscribed FROM subscribed s
                    JOIN threads t ON t.id = s.thread_id
                    WHERE t.slug = '$slug' AND s.user_id = '$user_id' AND t.deleted = 0";
            
            $result = $conn->query($sql);
            if($result->num_rows > 0) {
                if($result->fetch_assoc()["subscribed"] == 0) {
                    $subscribed = true;
                }
            } else {
                $subscribed = true;
                $autoSub = "true";
            }
        }
        ?>

        <button id="subscribed" onclick="unSubscribe(0)" <?= ($subscribed ? '' : 'style="display: none;"'); ?>><?= generateBell(true); ?></button>
        <button id="unsubscribed" onclick="unSubscribe(1)" <?= ($subscribed ? 'style="display: none;"' : '') ?>><?= generateBell(false); ?></button>
        
        </div>

        <div id="post-container" class="container">
            <?= generateHTMLFromPosts($slug, $page); ?>
        </div>

        <div id="page-menu"></div>

        <?php if(isset($_SESSION['user_id']) && $postCount !== 0) { 
            require_once $path . '/assets/generateSVG.php';
            ?>
            <textarea id="post-content" placeholder="Type your post here..."></textarea>
            <button class="action-button send-button" onclick="sendPost()"><?= generateSend() ?></button>
        <?php } ?>
    </div>

    <script> 
        const slug = "<?= $slug ?>";
        let page = <?= $page ?>;
        <?php if(isset($_SESSION['user_id'])) {
            echo "const autoSub = $autoSub;\n";
        } ?>
        let totalPosts = <?= $postCount; ?>;
        createPageMenu("gotoThreadPage", page, totalPosts);
    </script>
    <script src="/scripts/posts.js"></script>
    <?php require_once $path . "/assets/footer.php"; ?>
</body>
</html>