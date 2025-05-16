<?php

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') { 
    $body = file_get_contents('php://input'); //Be aware that the stream can only be read once
    echo $body
} else {
    echo "Invalid call";
}