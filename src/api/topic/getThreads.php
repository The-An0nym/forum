<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/require/threads.php';

echo response();

function response() {
    if(!isset($_GET['s'])) {
        return "Invalid or missing argument(s)";
    }
    $slug = $_GET['s'];

    if(!isset($_GET['p'])) {
        $page = 1;
    } else {
        $page = (int)$_GET['p'];
    }
        
    if(!session_id()) {
        session_start();
    }

    $threadCount = (int)getThreadCount($slug);

    if($threadCount === 0) {
        return "This thread is empty";
    }

    $page = min($page, floor($threadCount / 20));

    $threads = getThreads($slug, $page);

    $data = [];
    $data[] = $thread_count

    // output data of each thread
    foreach($threads as $thread) {
        $t = new stdClass();
        $t->name = $thread["name"];
        $t->slug = $thread["slug"];
        $t->id = $thread["id"];
        $t->created = $thread["created"];
        $t->postCount = $thread["posts"];
        $t->lastUser = $thread["lastUser"];
        $t->lastHandle = $thread["lastHandle"];
        $t->lastPost = $thread["lastPost"];
        $t->creator = $thread["username"];
        $t->creatorHandle = $thread["handle"];
        $t->deletable = $thread["clearance"];
        $data[] = $t;
    }

    $dataJSON = json_encode($data);
    return $dataJSON;
}