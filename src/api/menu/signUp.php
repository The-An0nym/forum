<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/errors.php' ;

echo response();

function response() {

    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    }

    $json_params = file_get_contents("php://input");

    if (strlen($json_params) === 0 || !json_validate($json_params)) {
        return jsonErr("args");
    }

    $json_obj = json_decode($json_params);

    if(!isset($json_obj->u, $json_obj->h, $json_obj->p)) {
        return jsonErr("args");
    }

    $username = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($json_obj->u));
    $handle = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($json_obj->h));
    $password = $json_obj->p;

    if(preg_match('/^[A-z0-9.\-_]*$/i', $handle) !== 1) {
        return getErr("handReg");
    } else if(strlen($username) > 24) {
        return jsonErr("userMax");
    } else if(strlen($username) < 4) {
        return jsonErr("userMin");
    } else if(strlen($handle) > 16) {
        return jsonErr("handMax");
    } else if(strlen($handle) < 4) {
        return jsonErr("handMin");
    } else if(strlen($password) > 64) {
        return jsonErr("pswdMax");
    } else if(strlen($password) < 8) {
        return jsonErr("pswdMin");
    }
            
    $sql = "SELECT `username`, `handle` FROM `users` WHERE `username` = '$username' OR `handle` = '$handle' LIMIT 1";
    $result = $conn->query($sql);
                
    if ($result->num_rows !== 0) {
        $row = $result->fetch_assoc();
        if($row["username"] === $username) {
            return jsonErr("tUser");
        } else if($row["handle"] === $handle) {
            return jsonErr("tHand");
        } else {
            return jsonErr();
        }
    }

    $user_id = uniqid(rand(), true);
    $secretId = $user_id . base64_encode(random_bytes(64));
    $pswrd = password_hash($password, PASSWORD_DEFAULT);
    $dtime = date('Y-m-d H:i:s');

    $sql = "INSERT INTO `users` (`user_id`, `image_dir`, `username`, `handle`, `password`, `created`)
    VALUES ('$user_id', '_default.png', '$username', '$handle', '$pswrd', '$dtime')";

    if ($conn->query($sql) === FALSE) {
        return jsonErr("sigFail");
    }

    /* SESSION */
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $session_id = base64_encode(random_bytes(64));
    $dtime = date('Y-m-d H:i:s');
    $sql = "INSERT INTO `sessions` (`user_id`, `ip`, `user_agent`, `session_id`, `datetime`)
    VALUES ('$user_id', '$ip', '$user_agent', '$session_id', '$dtime')";

    if ($conn->query($sql) === FALSE) {
        return jsonErr("sigFail");
    }

    $_SESSION['user_id'] = $user_id;
    $_SESSION['session_id'] = $session_id;
    $_SESSION['user_auth'] = 0; // New users have auth 0
    return pass();
}