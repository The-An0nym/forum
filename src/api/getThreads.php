<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/require/threads.php';

if(isset($_GET['s'], $_GET['p'])) {
    getThreadCount();
    echo "\0";
    getThreads($_GET['s'], $_GET['p'] * 20);
} else {
    echo "An error has occured";
}