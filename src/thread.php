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

if(isset($_GET["p"])) {
    $page = $_GET["p"];
} else {
    $page = 0;
}

$posts = getPosts($slug, $page);
$totalPosts = array_shift($posts);

if(!isset($totalPosts)) {
    $totalPosts = 0;
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
        <?php 
        if($posts !== []) {
        foreach ($posts as $post): 
        ?>
        <div class="post" id="<?= $post['post_id'] ?>">
            <span class="user-details">
                <img class="profile-picture" src="/images/profiles/<?= $post['image_dir'] ?>">
                <span class="username"><a href="/user/<?= $post['handle'] ?>"><?= $post['username'] ?></a></span>
                <span class="user-post-count"><?= $post['posts'] ?></span>
            </span>
            <span class="post-data">
                <span class="content"><?= $post['content'] ?></span>
                <span class="post-metadata">
                    <?php 
                    echo '<span class="created">' . $post['created'] . '</span>';
                    if($post['edited'] === "1") {
                        echo '<span class="edited">edited</span>';
                    }
                    if(isset($_SESSION["user_id"])) {
                        if($post["user_id"] === $_SESSION["user_id"]) {
                            echo '<button class="edit-button" onclick="editPost(\'' . $post['post_id'] . '\')">edit</button>';
                            echo '<button class="delete-button" onclick="createConfirmation(\'delete ' . $post['username'] . '\\\'s post\', \'\', deletePost, \'' . $post['post_id'] . '\')">delete</button>';
                        } else if($post['clearance'] === 1) {
                            echo '<button class="delete-button" onclick="createModeration(\'deleting ' . $post['username'] . '\\\'s post\', deletePost, \'' . $post['post_id'] . '\')">delete</button>';
                        } else {
                            echo '<button class="report-button" onclick="createReport(0, \'' . $post['post_id'] . '\')">Report</button>';
                        }
                    } ?>
                </span>
            </span>
        </div>
        <?php 
        endforeach;
        }
        ?>
    </div>

    <div id="pageMenu"></div>

    <?php if(isset($_SESSION['user_id']) && $posts !== []) { ?>
        <textarea id="post-content" placeholder="Type your post here..."></textarea>
        <button onclick="sendPost()">Submit</button>
    <?php } ?>

    <script> 
        const slug = "<?= $slug ?>";
        const page = <?= $page ?>;
        <?php if(isset($_SESSION['user_id'])) {
            echo "const autoSub = $autoSub;\n";
        } ?>
        createPageMenu("thread", slug, page, <?= $totalPosts?>);
    </script>
    <script src="/scripts/posts.js"></script>
    <?php include $path . "/basic/footer.php"; ?>
</body>
</html>