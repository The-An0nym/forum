<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/errors.php' ;

echo response();

function reponse() {

    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    }

    $json_params = file_get_contents("php://input");

    if (strlen($json_params) === 0 || !json_validate($json_params)) {
        return getError("args");
    }

    $decoded_params = json_decode($json_params);

    $username = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->u));
    $handle = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->h));
    $password = $decoded_params->p;

    if(preg_match('/^[A-z0-9.\-_]*$/i', $handle) !== 1) {
        return getErr("handReg");
    } else if(strlen($username) > 24) {
        return getError("userMax");
    } else if(strlen($username) < 4) {
        return getError("userMin");
    } else if(strlen($handle) > 16) {
        return getError("handMax");
    } else if(strlen($handle) < 4) {
        return getError("handMin");
    } else if(strlen($password) > 64) {
        return getError("pswdMax");
    } else if(strlen($password) < 8) {
        return getError("pswdMin");
    }
            
    $sql = "SELECT username, handle FROM users WHERE username='$username' OR handle='$handle' LIMIT 1";
    $result = $conn->query($sql);
                
    if ($result->num_rows !== 0) {
        $row = $result->fetch_assoc();
        if($row["username"] === $username) {
            return getError("tUser");
        } else if($row["handle"] === $handle) {
            return getError("tHand");
        } else {
            return getError();
        }
    }

    $user_id = uniqid(rand(), true);
    $secretId = $user_id . base64_encode(random_bytes(64));
    $pswrd = password_hash($password, PASSWORD_DEFAULT);
    $dtime = date('Y-m-d H:i:s');

    $sql = "INSERT INTO users (user_id, image_dir, username, handle, password, created)
    VALUES ('$user_id', '_default.png', '$username', '$handle', '$pswrd', '$dtime')";

    if ($conn->query($sql) === FALSE) {
        return getError("sigFail");
    }

    /* SESSION */
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $session_id = base64_encode(random_bytes(64));
    $dtime = date('Y-m-d H:i:s');
    $sql = "INSERT INTO sessions (user_id, ip, user_agent, session_id, datetime)
    VALUES ('$user_id', '$ip', '$user_agent', '$session_id', '$dtime')";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['session_id'] = $session_id;
        include($path . '/functions/deleteExpiredSessions.php');
    } else {
        return getError("sigFail");
    }
}