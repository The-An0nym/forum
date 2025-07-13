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

    if(!validateSession()) {
        return jsonErr("login");
    }

    if (!isset($_POST["i"])) {
        return jsonErr("args");
    }

    $id = $_POST["i"];

    $conn = getConn();
        
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT u.clearance FROM users u 
            WHERE u.user_id = '$user_id'
            LIMIT 1";

    $result = $conn->query($sql);
    if($result->num_rows !== 1) {
        return jsonErr("", "[GTS0]");
    }

    $clearance = (int)$result->fetch_assoc()["clearance"];
    if($clearance < 1) {
        return jsonErr("auth");
    }

    $sql = "SELECT slug FROM threads WHERE id = '$id'";
        
    $result = $conn->query($sql);
    if($result->num_rows !== 1) {
        return jsonErr("404thread");
    }

    $cont = $result->fetch_assoc()["slug"];
    return json_encode(
        array(
            "status" => "pass",
            "data" => array(
                "cont" => $cont
            )
        )
    );
}