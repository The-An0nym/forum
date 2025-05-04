<?php 
$path = $_SERVER['DOCUMENT_ROOT']; 

if(!session_id()) {
    session_start();
} ?>
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

    <?php if(isset($_SESSION['user_id'])) { ?>
        <textarea id="post-content" placeholder="Type your post here..."></textarea>
        <button onclick="sendPost()">Submit</button>
    <?php } ?>
    
    <script> 
        const slug = "<?php echo $_GET["s"] ?>";
        const page = "<?php if(isset($_GET["p"])) {echo $_GET["p"];} else {echo 0;} ?>" 
    </script>
    <script src="/scripts/errorMessage.js"></script>
    <script src="/scripts/sendPost.js"></script>
    <script src="/scripts/sendEdit.js"></script>
    <script src="/scripts/getPosts.js"></script>
    <script src="/scripts/editPost.js"></script>
    <?php include $path . "/basic/footer.php"; ?>
</body>
</html>