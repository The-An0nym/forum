<?php

$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/errors.php' ;

echo response();

function response() {

    // Get connection
    $conn = getConn();
        
    if(!isset($_POST["h"], $_POST["p"])) {
        return getError("args");
    }

    $handle = htmlspecialchars($_POST["h"]);
    $password = $_POST["p"];

    $sql = "SELECT password, user_id FROM users WHERE handle='$handle' AND deleted = 0";
    $result = $conn->query($sql);

    if ($result->num_rows !== 1) {
        return getError("acc");
    }

    $res = $result->fetch_assoc();
    $hashedPassword = $res["password"];
    $user_id = $res["user_id"];

    if(!password_verify($password, $hashedPassword)) {
        return getError("logPswd");
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $session_id = base64_encode(random_bytes(64));
    $dtime = date('Y-m-d H:i:s');
    $sql = "INSERT INTO sessions (user_id, ip, user_agent, session_id, datetime)
    VALUES ('$user_id', '$ip', '$user_agent', '$session_id', '$dtime')";

    if ($conn->query($sql) === TRUE) {
        session_start();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['session_id'] = $session_id;
    } else {
      return getError();
    }
}