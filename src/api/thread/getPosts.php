<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/require/posts.php';
include $path . '/functions/errors.php' ;

echo response();

function response() {
    if(!isset($_GET['s'], $_GET['p'])) {
        return jsonErr("args");
    }

    $slug = $_GET['s'];
    $page = (int)$_GET['p'];

    if(!session_id()) {
        session_start();
    }

    $post_count = getPostCount($slug);

    if($post_count === 0) { 
        return jsonErr("emptyThrd");
    }

    $page = min($page, ceil($post_count / 20));

    $posts = getPosts($slug, $page);
    $data = [];

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

    $dataJSON = json_encode(
        array(
            "status" => "pass",
            "data" => array(
                "posts" => $data,
                "amount" => $post_count;
            )
        )
    );
    return $dataJSON;
}