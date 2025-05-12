<?php

$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/require/threads.php';

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_GET['s'], $_GET['p'])) {
    $slug = $_GET['s'];
    $page = $_GET['p'] * 20;
    
    $threads = getThreads($slug, $page);

    if($threads > 1) {
        $data = [];
        $data[] = array_shift($threads);

        // output data of each thread
        foreach($threads as $thread) {
            $post = new stdClass();
            $post->name = $thread["name"];
            $post->slug = $thread["slug"];
            $post->created = $thread["created"];
            $post->postCount = $thread["posts"];
            $post->lastUser = $thread["lastUser"];
            $post->lastPost = $thread["lastPost"];
            $data[] = $post;
        }

        $dataJSON = json_encode($data);
        echo $dataJSON;
    } else {
        echo "ERROR: Invalid or missing argument";
    }
} else {
    echo "ERROR: Invalid or missing arguments";
}

$conn->close();