<?php 
$path = $_SERVER['DOCUMENT_ROOT']; 

if(!session_id()) {
    session_start();
}

include $path . "/basic/menu.php";

// Initial threads load
include $path . "/functions/require/posts.php";
if(isset($_GET["s"])) {
    $slug = $_GET["s"];
} else {
    $slug = "";
}

$postCount = getPostCount($slug);

if(isset($_GET["p"])) {
    $page = min((int)$_GET["p"], floor($postCount / 20));
} else {
    $page = 0;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quir | Thread</title>
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="stylesheet" href="/styles/posts.css" />
</head>
<body>
    <?php generateMenu(getPathNames($slug));

    $autoSub = "false";

    if(isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];

        include $path . "/functions/.connect.php";

        $conn = getConn();

        $sql = "SELECT s.subscribed FROM subscribed s
                JOIN threads t ON t.id = s.thread_id
                WHERE t.slug = '$slug' AND s.user_id = '$user_id' AND t.deleted = 0";
        
        $result = $conn->query($sql);
        if($result->num_rows > 0) {
            if($result->fetch_assoc()["subscribed"] == 0) {
                echo '<button id="subscribe" onclick="unSubscribe()">Subscribe</button>';
            } else {
                echo '<button id="subscribe" onclick="unSubscribe(0)">Unsubscribe</button>';
            }
        } else { 
            echo '<button id="subscribe" onclick="unSubscribe()">Subscribe</button>';
            $autoSub = "true";
        }
    }
    
    ?>

    <div id="post-container" class="container">
        <?= generateHTMLFromPosts($slug, $page); ?>
    </div>

    <div id="page-menu"></div>

    <?php if(isset($_SESSION['user_id']) && $postCount !== 0) { ?>
        <textarea id="post-content" placeholder="Type your post here..."></textarea>
        <button onclick="sendPost()">Submit</button>
    <?php } ?>

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
    <?php include $path . "/basic/footer.php"; ?>
</body>
</html>