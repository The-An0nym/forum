<?php $path = $_SERVER['DOCUMENT_ROOT']; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forums</title>
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="stylesheet" href="/styles/posts.css" />
</head>
<body>
    <?php include $path . "/basic/menu.php"; ?>

    <div id="post-container"></div>

    <textarea id="post-content" placeholder="Type your post here..."></textarea>
    <button onclick="send()">Submit</button>
    
    <script> const thread = "<?php str_replace("-", " ", $_GET["n"]) ?>" </script>
    <script src="/scripts/sendPost.js"></script>
    <script src="/scripts/sendEdit.js"></script>
    <script src="/scripts/getPosts.js"></script>
    <script src="/scripts/editPost.js"></script>
    <?php include $path . "/basic/footer.php"; ?>
</body>
</html>