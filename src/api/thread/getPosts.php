<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/require/posts.php';
require_once $path . '/functions/errors.php' ;

echo response();

function response() : string {
    if(!isset($_GET['s'])) {
        return jsonErr("args");
    }

    $slug = $_GET['s'];

    if(isset($_GET['p'])) {
        $page = (int)$_GET['p'];
    } else {
        $page = 1;
    }

    return getPostsJson($slug, $page);
}