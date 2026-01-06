<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/moderation.php';
require_once $path . '/functions/statCount.php';
require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/require/notifications.php' ;
require_once $path . '/functions/errors.php' ;

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

    if(!isset($_POST['i'])) {
        return jsonErr("args");
    }
        
    $id = $_POST['i'];

    $conn = getConn();
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT u.clearance, p.user_id 
                FROM users u 
            JOIN posts p 
                ON p.post_id = '$id' 
            WHERE u.user_id = '$user_id'
            LIMIT 1";

    $result = $conn->query($sql);
    if($result->num_rows !== 1) {
        return jsonErr("404user");
    }

    $row = $result->fetch_assoc();
    $clearance = $row['clearance'];
    $post_user_id = $row['user_id'];
    $user_id === $_SESSION["user_id"];

    if($post_user_id !== $user_id && $clearance < 1) {
        return jsonErr("auth");
    }
    
    $err = jsonEncodeErrors(countForPost($id, true));
    if($err !== "") {
        return $err;
    }

    $type = 1;

    if($post_user_id !== $user_id) {
        $type = 2;
        // Push onto history
        $sql = "INSERT INTO `history` (`id`, `type`, `judgement`, `sender_id`)
        VALUES ('$id', 0, 1, '$user_id')";
        if ($conn->query($sql) === FALSE) {
            return jsonErr("", "[RP0]");
        }
    }

    if($post_user_id === $user_id) {
        $err = jsonEncodeErrors(setDelNotifByAssoc($id, 0, false));
        if($err !== "") {
            return $err;
        }
    }

    $err = jsonEncodeErrors(deletePost($id, $type, true));
    if($err !== "") {
        return $err;
    }

    return pass();
}