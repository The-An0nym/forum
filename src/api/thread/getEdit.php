<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/errors.php' ;
require_once $path . '/functions/require/posts.php' ;

echo response();

function response() : string {
    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    }

    if(!validateSession()) { 
        return jsonErr("login");
    }

    if(!isset($_POST["i"])) {
        return jsonErr("args");
    }

    $id = $_POST["i"];

    $sql = "SELECT `content` FROM `posts` WHERE `post_id` = '$id'";
    $result = $conn->query($sql);
    if($result->num_rows === 0) {
        return jsonErr("404post");
    }
    
    $data = $result->fetch_assoc()["content"];
    $data = nl2br($data); // Set new lines

    return json_encode(array(
        "status" => "pass", 
        "data" => "$data"
    ));
}