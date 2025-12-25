<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/errors.php' ;

echo response();

function response() {
    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    } 

    if(!validateSession()) {
        return jsonErr("login");
    }

    if (!isset($_POST["i"])) {
        return jsonErr("args");
    }

    $id = $_POST["i"];

    $conn = getConn();
        
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT `clearance` FROM `users` WHERE `user_id` = '$user_id' LIMIT 1";

    $result = $conn->query($sql);
    if($result->num_rows !== 1) {
        return jsonErr("[GPC0]");
    }

    $clearance = (int)$result->fetch_assoc()["clearance"];

    if($clearance < 1) {
        return jsonErr("auth");
    }

    $sql = "SELECT `content`, `created`, `edited` FROM `posts` WHERE `post_id` = '$id'";
        
    $result = $conn->query($sql);
    if($result->num_rows !== 1) {
        return jsonErr("[GPC1]");
    }

    $row = $result->fetch_assoc();    

    return json_encode(
        array(
            "status" => "pass",
            "data" => array(
                "cont" => $row["content"],
                "dt" => $row["created"],
                "edited" => $row["edited"]
            )
        )
    );
}