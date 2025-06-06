<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/require/threads.php';

if(isset($_GET['s'], $_GET['p'])) {
    $slug = $_GET['s'];
    $page = $_GET['p'] * 20;

    $threads = getThreads($slug, $page);

    $data = [];
    $data[] = getThreadCount($slug);

    if($threads > 1) {
        // output data of each thread
        foreach($threads as $thread) {
            $t = new stdClass();
            $t->name = $thread["name"];
            $t->slug = $thread["slug"];
            $t->created = $thread["created"];
            $t->postCount = $thread["posts"];
            $t->lastUser = $thread["lastUser"];
            $t->lastPost = $thread["lastPost"];
            $t->creator = $thread["username"];
            $t->deletable = $thread["clearance"];
            $data[] = $t;
        }

        $dataJSON = json_encode($data);
        echo $dataJSON;
    } else {
        echo "No threads found...";
    }
} else {
    echo "An error has occured";
}