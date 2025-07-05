<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include($path . '/functions/validateSession.php')
include $path . '/functions/moderation.php' ;
include $path . '/functions/statCount.php';

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if(!session_id()) {
  session_start();
} 

if(validateSession()) {
    $json_params = file_get_contents("php://input");

    if (strlen($json_params) > 0 && json_validate($json_params)) {
        $decoded_params = json_decode($json_params);

        $mod_id = $decoded_params->i;
        $reason = (int)$decoded_params->r;

        $message = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->m));
        if(strlen($message) < 20 || strlen($message) > 200) {
            echo "Message needs to be between 20 to 200 chars";
            die();
        }
        
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT clearance FROM users
                WHERE user_id = '$user_id'
                LIMIT 1";

        $result = $conn->query($sql);

        if($result->num_rows !== 1) {
            echo "User auth not found";
            die();
        }

        $clearance = (int)$result->fetch_assoc()["clearance"];

        $sql = "SELECT mh.type, mh.judgement, mh.id, mh.culp_id, u.clearance
                FROM mod_history mh
                JOIN users u ON u.user_id = mh.culp_id
                WHERE mh.mod_id = '$mod_id'";
        $result = $conn->query($sql);

        if($result->num_rows === 0) {
            echo "Invalid ID";
            die();
        }

        $row = $result->fetch_assoc();
        $judgement = (int)$row["judgement"];
        $type = (int)$row["type"];
        $id = $row["id"];
        $culp_auth = (int)$row["clearance"];
        $culp_id = $row["culp_id"];

        if($clearance < $type) {
            echo "Insufficient clearance";
            die();
        }

        if($judgement < 2) {
            echo "Cannot undo report";
            die();
        }

        if($culp_id === $user_id) {
           echo "Cannot undo as affectee";
           die(); 
        }

        // Clearance of sender?

        // CHECK IF IT WAS LATEST ACTION FOR THIS ID (& ~judgement)

        if($type === 0) {
            if($judgement === 2) {
                createHistory(0, 4, $id, $user_id, 4, $message);
                deletePost($id, 2, true);   
                countForPost($id, true);
            } else if($judgement === 4) {
                createHistory(0, 2, $id, $user_id, $reason, $message);
                deletePost($id, 2, false);  
                countForPost($id, false); 
            }
        } else if($type === 1) {
            if($judgement === 2) {
                createHistory(1, 4, $id, $user_id, 4, $message);
                deleteThread($id, 4, true);
                countForThread($id, true);
            } else if($judgement === 4) {
                createHistory(1, 2, $id, $user_id, $reason, $message);
                deleteThread($id, 4, false);
                countForThread($id, false);
            }
        } else if($type === 2) {
            if($judgement === 2) {
                // Restore again w/o threads
                createHistory(2, 4, $id, $user_id, $reason, $message);
                deleteAccount($id, true, false);
                countForUser($id, true, false);
            } else if($judgement === 3) {
                // Restore again w threads
                createHistory(2, 5, $id, $user_id, $reason, $message);
                deleteAccount($id, true, true);
                countForUser($id, true, true);
            } else if($judgement === 4) {
                // Delete agin w/o threads
                createHistory(2, 2, $id, $user_id, $reason, $message);
                deleteAccount($id, false, false);
                countForUser($id, false, false);
            } else if($judgement === 5) {
                // Delete again w threads
                createHistory(2, 3, $id, $user_id, $reason, $message);
                deleteAccount($id, false, true);
                countForUser($id, false, true);
            } else if($judgement === 6) {
                // Promote again
                if($clearance > $culp_auth + 1 && $clearance > 2) {
                    createHistory(2, 7, $id, $user_id, $reason, $message);
                    // PROMOTE
                    $sql = "UPDATE users SET clearance = clearance + 1 WHERE user_id = '$culp_id'";
                    if ($conn->query($sql) === FALSE) {
                        echo "An error has occured trying to re-promote this user";
                    }
                } else {
                    echo "Insufficient Autho";
                    die();
                }
            } else if($judgement === 7) {
                // Demote again
                if($clearance > $culp_auth && $clearance > 2) {
                    createHistory(2, 6, $id, $user_id, $reason, $message);
                    // DEMOTE
                    $sql = "UPDATE users SET clearance = clearance - 1 WHERE user_id = '$culp_id'";
                    if ($conn->query($sql) === FALSE) {
                        echo "An error has occured trying to re-demote this user";
                    }
                } else {
                    echo "Insufficient Autho";
                    die();
                }
            }
        }        
    } else {
        echo "An error has occured MR3";
    }

} else {
    echo "Please login";
}