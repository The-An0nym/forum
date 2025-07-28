<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/validateSession.php';
include $path . '/functions/moderation.php' ;
include $path . '/functions/statCount.php';
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

    if(!isset($json_obj->i, $json_obj->r, $json_obj->m)) {
        return jsonErr("args");
    }

    $mod_id = $json_obj->i;
    $reason = (int)$json_obj->r;

    $message = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($json_obj->m));
    if(strlen($message) < 20 || strlen($message) > 200) {
        return jsonErr("msgMinMax");
    }
        
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT clearance FROM users
            WHERE user_id = '$user_id'
            LIMIT 1";

    $result = $conn->query($sql);
    if($result->num_rows !== 1) {
        return jsonErr("404user");
    }

    $clearance = (int)$result->fetch_assoc()["clearance"];

    $sql = "SELECT mh.type, mh.judgement, mh.id, mh.culp_id, mh.created, u.clearance
            FROM mod_history mh
            JOIN users u ON u.user_id = mh.culp_id
            WHERE mh.mod_id = '$mod_id'";
        
    $result = $conn->query($sql);
    if($result->num_rows === 0) {
        return jsonErr("404mod");
    }

    $row = $result->fetch_assoc();
    $judgement = (int)$row["judgement"];
    $type = (int)$row["type"];
    $id = $row["id"];
    $culp_auth = (int)$row["clearance"];
    $culp_id = $row["culp_id"];
    $created = $row["created"];

    if($clearance < $type) {
        return jsonErr("auth");
    }

    if($judgement < 2) {
        return jsonErr("undoRepo");
    }

    if($culp_id === $user_id) {
        return jsonErr("undoOwn");
    }

    $sql = "SELECT max(created) AS created FROM mod_history WHERE type = $type AND id = '$id'";
    $result = $conn->query($sql);

    if($result->num_rows !== 1) {
        return jsonErr("404mod");
    }

    $maxCreated = $result->fetch_assoc()["created"];
    if($maxCreated !== $created) {
        return jsonErr("nLastAction");
    }

    $delDatetime = strtotime(date('Y-m-d H:i:s')) - 60 * 60 * 24 * 60; // 60 days ago
    if(strtotime($maxCreated) < $delDatetime) {
       return jsonErr("expired");
    }

    if($type === 0) {
        // POSTS
        if($judgement === 2) {
            $dec = 4;
            $restore = true;
            $reason = 4;
        } else if($judgement === 4) {
            $dec = 2;
            $restore = false; 
        }
        
        $err = jsonEncodeErrors(createHistory(0, $dec, $id, $user_id, $reason, $message));
        if($err !== "") {
            return $err;
        }

        $err = jsonEncodeErrors(deletePost($id, 2, $restore));
        if($err !== "") {
            return $err;
        }

        $err = jsonEncodeErrors(countForPost($id, $restore));
        if($err !== "") {
            return $err;
        }
    } else if($type === 1) {
        // THREADS
        if($judgement === 2) {
            $dec = 4;
            $restore = true;
            $reason = 4;
        } else if($judgement === 4) {
            $dec = 2;
            $restore = false;
        }

        $err = jsonEncodeErrors(createHistory(1, $dec, $id, $user_id, $reason, $message));
        if($err !== "") {
            return $err;
        }

        $err = jsonEncodeErrors(deleteThread($id, 4, $restore));
        if($err !== "") {
            return $err;
        }

        $err = jsonEncodeErrors(countForThread($id, $restore));
        if($err !== "") {
            return $err;
        }
    } else if($type === 2 && $judgement < 6) {
        // USERS
        if($judgement === 2) {
            // Restore again w/o threads
            $dec = 4;
            $restore = true;
            $rThreads = false;
        } else if($judgement === 3) {
            // Restore again w threads
            $dec = 5;
            $restore = true;
            $rThreads = true;
        } else if($judgement === 4) {
            // Delete agin w/o threads
            $dec = 2;
            $restore = false;
            $rThreads = false;
        } else if($judgement === 5) {
            // Delete again w threads
            $dec = 3;
            $restore = false;
            $rThreads = true;
        }

        $err = jsonEncodeErrors(createHistory(2, $dec, $id, $user_id, $reason, $message));
        if($err !== "") {
            return $err;
        }

        $err = jsonEncodeErrors(countForUser($id, $restore, $rThreads));
        if($err !== "") {
            return $err;
        }

        $err = jsonEncodeErrors(deleteAccount($id, $restore, $rThreads));
        if($err !== "") {
            return $err;
        }

    } else if($type === 2 && $judgement >= 6) {
        if($judgement === 6) {
            // Promote again
            if($clearance > $culp_auth + 1 && $clearance > 2) {
                $err = jsonEncodeErrors(createHistory(2, 7, $id, $user_id, $reason, $message));
                if($err !== "") {
                    return $err;
                }

                // PROMOTE
                $sql = "UPDATE users SET clearance = clearance + 1 WHERE user_id = '$culp_id'";
                if ($conn->query($sql) === FALSE) {
                    return jsonErr("", "[U0]");
                }
            } else {
                return jsonErr("auth");
            }
        } else if($judgement === 7) {
            // Demote again
            if($clearance > $culp_auth && $clearance > 2) {
                $err = jsonEncodeErrors(createHistory(2, 6, $id, $user_id, $reason, $message));
                if($err !== "") {
                    return $err;
                }

                // DEMOTE
                $sql = "UPDATE users SET clearance = clearance - 1 WHERE user_id = '$culp_id'";
                if ($conn->query($sql) === FALSE) {
                    return jsonErr("", "[U1]");
                }
            } else {
                return jsonErr("auth");
            }
        }
    }
    
    return pass();
}