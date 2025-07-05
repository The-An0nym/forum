<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/require/threads.php';

if(!isset($_GET['s'], $_GET['p'])) {

}
    
$slug = $_GET['s'];
$page = (int)$_GET['p'];

if(!session_id()) {
    session_start();
} 

$threadCount = (int)getThreadCount($slug);

if($threadCount === 0) {
    echo "This thread is empty";
    die();
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
echo $dataJSON;