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
    if(isset($_GET['i'])) {
        $id = $_GET['i'];

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

            if($post_user_id === $user_id || $clearance >= 1) {
                // Increment post count of user
                $sql = "UPDATE users SET posts = posts +1 WHERE user_id = '$post_user_id'";
                if ($conn->query($sql) === FALSE) {
                    echo "An error has occured [DP0]";
                }

                // Increment post count of category and thread
                $sql = "UPDATE posts p
                        INNER JOIN threads t ON p.thread_id = t.id
                        INNER JOIN categories c ON t.category_id = c.id
                        SET c.posts = c.posts +1, t.posts = t.posts +1 
                        WHERE p.post_id = '$id'";
                if ($conn->query($sql) === FALSE) {
                    echo "An error has occured [DP1]";
                }   

                $type = 1;

                if($post_user_id !== $user_id) {
                    $type = 2;
                    // Push onto history
                    $sql = "INSERT INTO history (id, type, judgement, sender_id)
                    VALUES ('$id', 0, 1, '$user_id')";
                    if ($conn->query($sql) === FALSE) {
                        echo "ERROR: Please try again later [DP2]";
                    }
                }

                // (Soft) restore post
                $dtime = date('Y-m-d H:i:s');
                $sql = "UPDATE posts SET deleted = deleted & ~$type WHERE post_id = '$id'";
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