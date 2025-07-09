<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/require/threads.php';
include $path . '/functions/errors.php' ;

echo response();

function response() {
    if(!isset($_GET['s'])) {
        return jsonErr("args");
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

    $thread_count = (int)getThreadCount($slug);

    if($thread_count === 0) {
        return jsonErr("emptyCat");
    }

    $page = min($page, ceil($thread_count / 20));

    $threads = getThreads($slug, $page);

    $data = [];

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

    $dataJSON = json_encode(
        array(
            "status" => "pass",
            "data" => array(
                "threads" => $data,
                "amount" => $thread_count
            )
        )
    );
    return $dataJSON;
}