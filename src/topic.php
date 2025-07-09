<?php 
$path = $_SERVER['DOCUMENT_ROOT']; 

if(!session_id()) {
    session_start();
} 

include $path . "/basic/menu.php";

include $path . "/functions/require/threads.php";

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

    <div id="menu-path">
        <?= generateMenuPath(1, $slug) ?>
    </div>

    <div id="thread-container" class="container">
        <?php generateHTMLFromThreads($slug, $page) ?>
    </div>

    <div id="page-menu"></div>

    <?php if(isset($_SESSION['user_id'])) { ?>
        <input id="thread-name" placeholder="Thread title..."></input>
        <textarea id="post-content" placeholder="Type your post here..."></textarea>
        <button onclick="createThread()">Submit</button>
    <?php } ?>

    <script> 
        const slug = "<?= $slug; ?>";
        let page = <?= $page; ?>;
        let threadCount = <?= $threadCount ?>;
        createPageMenu("gotoTopicPage", page, threadCount);
    </script>
    <script src="/scripts/threads.js"></script>

    <?php include $path . "/basic/footer.php"; ?>
</body>
</html>