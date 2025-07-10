<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/validateSession.php';
include $path . '/functions/moderation.php' ;
include $path . '/functions/statCount.php';
include $path . '/functions/errors.php' ;

// Get connection
$conn = getConn();

if(!session_id()) {
  session_start();
}

echo response();

function response() {
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

    if(isset($json_obj->t)) {
        $del_threads = (bool)$json_obj->t;
    } else {
        $del_threads = false;
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

    if($result->num_rows === 0) {
        return jsonErr("404user");
    }
        
    $row = $result->fetch_assoc();
    $clearance = $row['clearance'];
    $user_clearance = $row['user_clearance'];

    if($id !== $user_id || $clearance >= 3) {
        if(!isset($json_obj->m, $json_obj->r)) {
            return jsonErr("args");
        }

        $reason = (int)$json_obj->r;
        $message = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($json_obj->m));
        if(strlen($message) < 20 || strlen($message) > 200) {
            return jsonErr("msgMinMax");
        }
    }

    if(($clearance >= 3 && $user_clearance < $clearance) || $id === $user_id) {
        $type = 1; // Self-deleted
        if($id !== $user_id) {
            // Push onto history
            if($del_threads) {
                $err = jsonEncodeErrors(createHistory(2, 3, $id, $user_id, $reason, $message));
            } else {
                $err = jsonEncodeErrors(createHistory(2, 2, $id, $user_id, $reason, $message));
            }

            if($err !== "") {
                return $err;
            }

            $type = 8; // Banned
        } else {
            $del_threads = false;
        }

        $err = jsonEncodeErrors(countForUser($id, false, $del_threads));
        if($err !== "") {
            return $err;
        }

        $err = jsonEncodeErrors(deleteAccount($id, false, $del_threads));
        if($err !== "") {
            return $err;
        }
    } else {
        return jsonErr("auth");
    }

    return pass();
}