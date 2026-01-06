<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/moderation.php' ;
require_once $path . '/functions/require/notifications.php' ;
require_once $path . '/functions/statCount.php';
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
    $json_params = file_get_contents("php://input");

    if (strlen($json_params) === 0 || !json_validate($json_params)) {
        return jsonErr("args");
    }
        
    $json_obj = json_decode($json_params);

    if(!isset($json_obj->i)) {
        return jsonErr("args");
    }

    $id = $json_obj->i;

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
    $clearance = (int)$row['clearance'];
    $post_user_id = $row['user_id'];
    $user_id === $_SESSION["user_id"];

    if($post_user_id !== $user_id && $clearance >= 1) {
        if(isset($json_obj->m, $json_obj->r)) {
            $reason = (int)$json_obj->r;
            $message = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($json_obj->m));
            if(strlen($message) < 20 || strlen($message) > 200) {
                return jsonErr("msgMinMax");
            }
        } else {
            return jsonErr("args");
        }
    }

    if($post_user_id !== $user_id && $clearance == 0) {
        return jsonErr("auth");
    }

    $err = jsonEncodeErrors(countForPost($id, false));
    if($err !== "") {
        return $err;
    }

    $type = 1;

    if($post_user_id !== $user_id) {
        $type = 2;
        // Push onto history
        $err = jsonEncodeErrors(createHistory(0, 2, $id, $user_id, $reason, $message));
        if($err !== "") {
            return $err;
        }
    }

    if($post_user_id === $user_id) {
        // Delete notification(s)
        $err = jsonEncodeErrors(setDelNotifByAssoc($id, 0, true));
        if($err !== "") {
            return $err;
        }
    }

    // (Soft) delete post
    $err = jsonEncodeErrors(deletePost($id, $type, false));
    if($err !== "") {
        return $err;
    }

    return pass();
}