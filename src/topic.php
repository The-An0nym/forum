<?php 
$path = $_SERVER['DOCUMENT_ROOT']; 

if(!session_id()) {
    session_start();
} 

include $path . "/functions/require/threads.php";
if(isset($_GET["s"])) {
    $slug = $_GET["s"];
} else {
    $slug = "";
}

if(isset($_GET["p"])) {
    $page = $_GET["p"];
} else {
    $page = 0;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quir | Threads</title>
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="stylesheet" href="/styles/threads.css" />
</head>
<body>
    <?php include $path . "/basic/menu.php"; ?>

    <div id="thread-container">
        <?php getThreads($slug, $page * 20) ?>
    </div>

    <div id="pageMenu"></div>

    <?php if(isset($_SESSION['user_id'])) { ?>
        <input id="thread-name" placeholder="Thread title..."></input>
        <textarea id="post-content" placeholder="Type your post here..."></textarea>
        <button onclick="createThread()">Submit</button>
    <?php } ?>

    <script src="/scripts/main.js"></script>
    <script> 
        const slug = "<?= $slug; ?>";
        const page = <?= $page; ?>;
        createPageMenu("topic", slug, page, <?= getThreadCount($slug); ?>);
    </script>
    <script src="/scripts/threads.js"></script>

    <?php include $path . "/basic/footer.php"; ?>
</body>
</html>