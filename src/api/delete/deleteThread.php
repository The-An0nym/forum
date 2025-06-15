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
        $reason = (int)$decoded_params->r;

        $message = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->m));
        if(strlen($message) < 20 || strlen($message) > 200) {
            echo "Message needs to be between 20 to 200 chars";
            die();
        }

        $conn = getConn();
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT u.clearance, t.user_id 
                    FROM users u 
                JOIN threads t 
                    ON t.id = '$id' 
                WHERE u.user_id = '$user_id'
                LIMIT 1";

        $result = $conn->query($sql);

        if($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $clearance = $row['clearance'];
            $creator_user_id = $row['user_id'];
            $user_id === $_SESSION["user_id"];            

            if($clearance >= 1) {
                // Get amount of posts (which are now hidden)
                $sql = "SELECT 
                            user_id
                        FROM posts 
                        WHERE thread_id = '$id'";
                
                $result = $conn->query($sql);
                $post_count = 0;
                // Decrement user posts
                while($post = $result->fetch_assoc()) {
                    $post_count++;
                    $user_id = $post["user_id"];
                    $sql = "UPDATE users SET posts = posts - 1 WHERE user_id = '$user_id'";
                    if ($conn->query($sql) === FALSE) {
                        echo "An error has occured [DT0]";
                    }
                }

                // Decrement user thread count
                $sql = "UPDATE users
                        SET threads = threads - 1 
                        WHERE user_id = '$creator_user_id'";
                if ($conn->query($sql) === FALSE) {
                    echo "An error has occured [DT1]";
                }

                // Decrement post and thread count of category
                $sql = "UPDATE threads t
                        INNER JOIN categories c ON t.category_id = c.id
                        SET c.threads = c.threads -1,
                            c.posts = c.posts - $post_count
                        WHERE t.id = '$id'";
                if ($conn->query($sql) === FALSE) {
                    echo "An error has occured [DT2]";
                }

                // Push onto history
                createHistory($conn, 1, 0, $id, $user_id, $reason, $message)
                
                // (Soft) delete thread
                $dtime = date('Y-m-d H:i:s');
                $sql = "UPDATE threads SET deleted | 2, deleted_datetime = '$dtime' WHERE id = '$id'";
                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [DT4]";
                }

                // (Soft) delete posts
                $dtime = date('Y-m-d H:i:s');
                $sql = "UPDATE posts SET deleted | 3, deleted_datetime = '$dtime' WHERE thread_id = '$id'";
                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [DT4]";
                }
            } else {
                echo "Clearance level too low";
            }
        } else {
            echo "An error has occured DT5";
        }
    } else {
        echo "An error has occured DT6";
    }

} else {
    echo "Please login";
}