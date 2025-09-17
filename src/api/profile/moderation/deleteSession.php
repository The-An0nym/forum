<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/validateSession.php';
include $path . '/functions/errors.php' ;

echo response();

function response() {
    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    } 

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

    return json_encode(
        array(
            "status" => "pass")
    );;
}