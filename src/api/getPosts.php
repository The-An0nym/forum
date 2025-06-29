<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/require/posts.php';

if(!isset($_GET['s'], $_GET['p'])) {
    echo "Invalid argument(s)";
    die();
 }

$slug = $_GET['s'];
$page = (int)$_GET['p'];

if(!session_id()) {
    session_start();
}

$postCount = getPostCount($slug);

if($postCount === 0) { 
    echo "This thread is empty...";
    die();
}

$page = min($page, floor($postCount / 20));

$posts = getPosts($slug, $page);
$data = [];
$data[] = $postCount;

// output data of each post
foreach($posts as $post) {
    $p = new stdClass();
    $p->username = $post["username"];
    $p->handle = $post["handle"];
    $p->imageSrc = $post["image_dir"];
    $p->userPostCount = $post["posts"];
    $p->id = $post["post_id"];
    $p->content = $post["content"];
    $p->created = $post["created"];
    $p->edited = $post["edited"];
    if(isset($_SESSION["user_id"])) {
        if($post["user_id"] == $_SESSION["user_id"]) {
            $p->editable = true;
        } else {
            $p->editable = false;
        }
    } else {
        $p->editable = false;
    }
    $p->deletable = $post["clearance"];
    $data[] = $p;
}

$dataJSON = json_encode($data);
echo $dataJSON;