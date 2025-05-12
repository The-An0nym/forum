<?php 
$path = $_SERVER['DOCUMENT_ROOT']; 

if(!session_id()) {
    session_start();
} 

// Initial threads load
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

$threads = getThreads($slug, $page);
$totalThreads = array_shift($threads);
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
        <?php 
        if($threads !== []) {
        foreach ($threads as $thread): 
        ?>
            <a class="thread-wrapper" href="/thread/<?= $thread['slug'] ?>">
                <div class="thread">
                    <span class="main-wrapper">
                        <span class="thread-name"><?= $thread['name'] ?></span>
                        <span class="created"><?= $thread['created'] ?></span>
                    </span>
                    <span class="details-wrapper">
                        <span class="last-wrapper">
                            <span class="last-post"><?= $thread['lastPost'] ?></span>
                            <span class="last-user"><?= $thread['lastUser'] ?></span>
                        </span>
                        <span class="count"><?= $thread['posts'] ?></span>
                    </span>
                </div>
            </a>
        <?php 
        endforeach;
        } else {
            echo "An error has occured";
        }
        ?>
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
        createPageMenu("topic", "<?= $slug ?>", <?= $page ?>, <?= $totalThreads ?>);
    </script>
    <script src="/scripts/threads.js"></script>

    <?php include $path . "/basic/footer.php"; ?>
</body>
</html>