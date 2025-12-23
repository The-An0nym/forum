<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/errors.php' ;

echo response();

function response() {
    // Get connection
    $conn = getConn();

    if (!isset($_POST["i"])) {
        return jsonErr("args");
    }

    $id = $_POST["i"];

    if(!validateSession()) {
        return jsonErr("login");
    }

    $sql = "DELETE FROM `sessions` WHERE session_id = '$id'";
    if($conn->query($sql) === FALSE) {
        return jsonErr();
    }

    return getPass("delSess");
}