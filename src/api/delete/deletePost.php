<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/moderation.php' ;

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

        $conn = getConn();
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT u.clearance, p.user_id 
                    FROM users u 
                JOIN posts p 
                    ON p.post_id = '$id' 
                WHERE u.user_id = '$user_id'
                LIMIT 1";

        $result = $conn->query($sql);
        if($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $clearance = $row['clearance'];
            $post_user_id = $row['user_id'];
            $user_id === $_SESSION["user_id"];

            if($post_user_id !== $user_id || $clearance >= 1) {
                if(isset($decoded_params->m, $decoded_params->r)) {
                    $reason = (int)$decoded_params->r;
                    $message = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->m));
                    if(strlen($message) < 20 || strlen($message) > 200) {
                        echo "Message needs to be between 20 to 200 chars";
                        die();
                    }
                } else {
                    echo "Message and reason required";
                    die();
                }
            }

            if($post_user_id === $user_id || $clearance >= 1) {
                // Decrement post count of user
                $sql = "UPDATE users SET posts = posts -1 WHERE user_id = '$post_user_id'";
                if ($conn->query($sql) === FALSE) {
                    echo "An error has occured [DP0]";
                }

                // Decrement post count of category and thread
                $sql = "UPDATE posts p
                        INNER JOIN threads t ON p.thread_id = t.id
                        INNER JOIN categories c ON t.category_id = c.id
                        SET c.posts = c.posts -1, t.posts = t.posts -1 
                        WHERE p.post_id = '$id'";
                if ($conn->query($sql) === FALSE) {
                    echo "An error has occured [DP1]";
                }   

                $type = 1;

                if($post_user_id !== $user_id) {
                    $type = 2;
                    // Push onto history
                    createHistory($conn, 0, 0, $id, $user_id, $reason, $message)
                }

                // (Soft) delete post
                $dtime = date('Y-m-d H:i:s');
                $sql = "UPDATE posts SET deleted = deleted | $type, deleted_datetime = '$dtime' WHERE post_id = '$id'";
                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [DP3]";
                }
                
            } else {
                echo "Clearance level too low";
            }
        } else {
            echo "An error has occured DP4";
        }
    } else {
        echo "An error has occured DP5";
    }

} else {
    echo "Please login";
}