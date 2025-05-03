<?php $path = $_SERVER['DOCUMENT_ROOT']; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forums</title>
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="stylesheet" href="/styles/threads.css" />
</head>
<body>
    <?php include $path . "/basic/menu.php"; ?>

    <div id="thread-container"></div>

    <?php if(include $path . "/functions/validSession.php") { ?>
        <input id="thread-name" placeholder="Thread title..."></input>
        <textarea id="post-content" placeholder="Type your post here..."></textarea>
        <button onclick="createThread()">Submit</button>
    <?php } ?>

    <script> const category = "<?php echo $_GET["n"] ?>" </script>
    <script src="/scripts/errorMessage.js"></script>
    <script src="/scripts/getThreads.js"></script>
    <script src="/scripts/sendThread.js"></script>


    <?php include $path . "/basic/footer.php"; ?>
</body>
</html>