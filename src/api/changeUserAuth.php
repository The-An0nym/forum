<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/moderation.php' ;
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

    $json_params = file_get_contents("php://input");

    if (strlen($json_params) === 0 || !json_validate($json_params)) {
        return jsonErr("args");
    }

    $json_obj = json_decode($json_params);

    if(!isset($json_obj->i, $json_obj->p, $json_obj->r, $json_obj->m)) {
        return jsonErr("args");
    }

    $id = $json_obj->i;
    $promote = (bool)$json_obj->p;
    $reason = (int)$json_obj->r;

    $message = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($json_obj->m));
    if(strlen($message) < 20 || strlen($message) > 200) {
        return jsonErr("msgMinMax");
    }

    $conn = getConn();
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT u.clearance, b.clearance AS user_clearance 
                FROM users u 
            JOIN users b 
                ON b.user_id = '$id' 
            WHERE u.user_id = '$user_id'
            LIMIT 1";

    $result = $conn->query($sql);

    if($result->num_rows !== 1) {
        return jsonErr("404user");
    }

    $row = $result->fetch_assoc();
    $clearance = $row['clearance'];
    $user_clearance = $row['user_clearance'];

    // To make sure you cannot promote to your own auth level
    $const = 0;
    if($promote) {
        $const = 1;
    }

    if($clearance >= 4 && $user_clearance < $clearance - $const) {
        // Push onto history
        createHistory(2, 6 + $const, $id, $user_id, $reason, $message);
        
        if($promote) {
            $sy = "+";
        } else {
            $sy = "-";
        }

        // Demote user
        $sql = "UPDATE users SET clearance = clearance $sy 1 WHERE user_id = '$id'";
        if ($conn->query($sql) === FALSE) {
            return jsonErr("", "[CUA0]");
        }
    } else {
        return jsonErr("auth");
    }
    return pass();
}