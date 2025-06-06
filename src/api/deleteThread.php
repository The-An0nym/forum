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
            $post_user_id = $row['user_id'];
            $user_id === $_SESSION["user_id"];

            if($clearance >= 1) {
                // Get amount of posts (which are now hidden)
                $sql = "SELECT 
                            posts.user_id
                        FROM posts 
                        WHERE posts.thread_id = '$id'";
                
                $result = $conn->query($sql);
                $post_count = 0;
                // Decrement user posts
                while($post = $result->fetch_assoc()) {
                    $post_count++;
                    $sql = "UPDATE users SET posts = posts - 1 WHERE id = $post['user_id']";
                    if ($conn->query($sql) === FALSE) {
                        echo "An error has occured [DT0]";
                    }
                }

                // Decrement post and thread count of category
                $sql = "UPDATE threads t
                        INNER JOIN categories c ON t.category_id = c.id
                        SET c.threads = c.threads -1,
                            c.posts = posts - $post_count
                        WHERE t.id = '$id'";
                if ($conn->query($sql) === FALSE) {
                    echo "An error has occured [DT2]";
                }

                // Push onto history
                $sql = "INSERT INTO history (id, type, judgement, sender_id)
                VALUES ('$id', 1, 0, '$user_id')";
                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [DT3]";
                }
                
                // (Soft) delete post
                $sql = "UPDATE threads SET deleted = 1 WHERE id = '$id'";
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