<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/require/posts.php';

if(isset($_GET['s'], $_GET['p'])) {
    $slug = $_GET['s'];
    $page = $_GET['p'] * 20;

    $posts = getPosts($slug, $page);

    if($posts > 1) {
        $data = [];
        $data[] = array_shift($posts);

        session_start();

        // output data of each post
        foreach($posts as $post) {
            $p = new stdClass();
            $p->username = $post["username"];
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
            $data[] = $p;
        }

        $dataJSON = json_encode($data);
        echo $dataJSON;
    } else {
        echo "An error has occured";
    }

} else {
    echo "An error has occured";
    die();
}