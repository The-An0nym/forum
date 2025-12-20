<?php 
$path = $_SERVER['DOCUMENT_ROOT']; 

if(!session_id()) {
    session_start();
} 

require_once $path . "/assets/menu.php";

require_once $path . "/functions/require/threads.php";

if(isset($_GET["s"])) {
    $slug = $_GET["s"];
} else {
    $slug = "";
}

$threadCount = getThreadCount($slug);

if(isset($_GET["p"])) {
    $page = min((int)$_GET["p"], ceil($threadCount / 20));
} else {
    $page = 1;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quir | Threads</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="stylesheet" href="/styles/threads.css" />
</head>
<body>
    <?php generateMenu() ?>

    <div id="global">
        <div id="menu-path-wrapper">
            <?= generateMenuPath(1, $slug) ?>
        </div>

        <div id="thread-container" class="container">
            <?php generateHTMLFromThreads($slug, $page) ?>
        </div>

        <div id="page-menu"></div>

        <?php if(isset($_SESSION['user_id'])) { 
            require_once $path . '/assets/generateSVG.php';
            ?>
            <input id="thread-name" placeholder="Thread title..."></input>
            <textarea id="post-content" placeholder="Type your post here..."></textarea>
            <button class="action-button send-button" onclick="createThread()"><?= generateSend() ?></button>
        <?php } ?>
    </div>

    <script> 
        const slug = "<?= $slug; ?>";
        let page = <?= $page; ?>;
        let threadCount = <?= $threadCount ?>;
        createPageMenu("gotoTopicPage", page, threadCount);
    </script>
    <script src="/scripts/threads.js"></script>

    <?php require_once $path . "/assets/footer.php"; ?>
</body>
</html>