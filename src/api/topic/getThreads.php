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

    if(isset($_GET['p'])) {
        $page = (int)$_GET['p'];
    } else {
        $page = 1;
    }
        
    return getThreadsJson($slug, $page);
}