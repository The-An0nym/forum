<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if(!session_id()) {
  session_start();
} 

if(include($path . "/functions/validateSession.php")) {
    $json_params = file_get_contents("php://input");

    if (strlen($json_params) > 0 && json_validate($json_params)) {
        $decoded_params = json_decode($json_params);

        $id = $decoded_params->i;
        $reason = (int)$decoded_params->r;

        $message = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->m));
        if(strlen($message) < 20 || strlen($message) > 200) {
            echo "Message needs to be between 20 to 200 chars";
            die();
        }

        $type = (int)$decoded_params->t;

        $conn = getConn();
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT clearance FROM users
                WHERE user_id = '$user_id'
                LIMIT 1";

        $result = $conn->query($sql);

        if($result->num_rows !== 1) {
            echo "An error has occured UM0";
            die();
        }

        if($type < 0 || $type > 2) {
            echo "Invalid type";
            die();
        }

        $clearance = (int)$result->fetch_assoc()["clearance"];

        if($clearance < $type) {
            echo "Insufficient clearance";
            die();
        }

        // Check type
        if($type === 0) {
            $sql = "SELECT * FROM mod_history_posts mp
                    JOIN mod_history mh ON mh.mod_id = mp.mod_id 
                    WHERE mp.post_id = '$id' LIMIT 1";
        } else if($type === 1) {
            $sql = "SELECT * FROM mod_history_threads mt
                    JOIN mod_history mh ON mh.mod_id = mt.mod_id 
                    WHERE mt.thread_id = '$id' LIMIT 1";
        } else if($type === 2) {
            $sql = "SELECT * FROM mod_history_users mu
                    JOIN mod_history mh ON mh.mod_id = mu.mod_id 
                    WHERE mu.post_id = '$id' LIMIT 1";
        }
        
        $result = $conn->query($sql);

        if($result->num_rows !== 1) {
            echo "An error has occured MR1";
            die();
        }

        $row = $result->fetch_assoc();

        if($type === 0) {
            $sql = "UPDATE posts SET deleted = deleted & ~2 WHERE post_id = '$id'";
            if($conn->query($sql) === FALSE) {
                echo "An error has occured MR2";
            }
        }
        if($type === 1) {
            $sql = "UPDATE threads SET deleted = deleted & ~2 WHERE post_id = '$id'";
            if($conn->query($sql) === FALSE) {
                echo "An error has occured MR3";
            }

            $sql = "UPDATE posts SET deleted = deleted & ~4 WHERE thread_id = '$id'";
            if($conn->query($sql) === FALSE) {
                echo "An error has occured MR4";
            }
        }
        if($type === 2) {
            if($row["judgement"] === 4) {

            } else if($row["judgement"] === 5) {

            } else {

            }
        }

        // I give up for now...

        // Do stuff...  
        
    } else {
        echo "An error has occured MR3";
    }

} else {
    echo "Please login";
}