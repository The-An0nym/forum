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

    if (!isset($_POST['p'], $_POST['np'])) {
        return jsonErr("args");
    }

    $password = htmlspecialchars($_POST["p"]);
    $newPassword = htmlspecialchars($_POST["np"]);
    $user_id = $_SESSION["user_id"];

    if(strlen($password) > 50 || strlen($newPassword) > 50) {
        return jsonErr("pswdMax");
    } else if(strlen($password) < 8 || strlen($newPassword) < 8) {
        return jsonErr("pswdMin");
    }

    $sql = "SELECT password FROM users WHERE user_id='$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows !== 1) {
        return jsonErr("404user");
    }

    $res = $result->fetch_assoc();
    $hashedPassword = $res["password"];

    if(!password_verify($password, $hashedPassword)) {
        return jsonErr("pswdFail");
    }
    
    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $sql = "UPDATE users SET password = '$newHashedPassword' WHERE user_id='$user_id'";

    if ($conn->query($sql) === FALSE) {
        return jsonErr("", "[CP0]");
    }

    return pass();
}