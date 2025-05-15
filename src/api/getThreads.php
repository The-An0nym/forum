<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/require/threads.php';

if(isset($_GET['s'], $_GET['p'])) {
    $slug = $_GET['s'];
    $page = $_GET['p'] * 20;
    
    $threads = getThreads($slug, $page);

    if($threads > 1) {
        $data = [];

        // output data of each thread
        foreach($threads as $thread) {
            $t = new stdClass();
            $t->name = $thread["name"];
            $t->slug = $thread["slug"];
            $t->created = $thread["created"];
            $t->postCount = $thread["posts"];
            $t->lastUser = $thread["lastUser"];
            $t->lastPost = $thread["lastPost"];
            $data[] = $t;
        }

        $dataJSON = json_encode($data);
        echo $dataJSON;
    } else {
        echo "An error has occured";
    }
} else {
    echo "An error has occured";
}