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
    $conn = getConn();
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT clearance FROM users 
            WHERE user_id = '$user_id'
            LIMIT 1";

    $result = $conn->query($sql);

    if($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $clearance = $row['clearance'];

        if($clearance == 5) {
            // Update threads
            $sql = "UPDATE threads t
                    JOIN (
                        SELECT thread_id, COUNT(*) AS cnt
                        FROM posts
                        WHERE deleted = 0
                        GROUP BY thread_id
                    ) p ON t.id = p.thread_id
                    SET t.posts = p.cnt";
            if ($conn->query($sql) === FALSE) {
                echo "ERROR: Please try again later [SN0]";
            }

            // Update category
            $sql = "UPDATE categories c
                    JOIN (
                        SELECT category_id, COUNT(*) AS cnt, SUM(posts) AS sum
                        FROM threads
                        WHERE deleted = 0
                        GROUP BY category_id
                    ) t ON c.id = t.category_id
                    SET c.threads = t.cnt, c.posts = t.sum";
            if ($conn->query($sql) === FALSE) {
                echo "ERROR: Please try again later [SN1]";
            }

            // Update user post count
            $sql = "UPDATE users u
                    JOIN (
                        SELECT user_id, COUNT(*) AS cnt
                        FROM posts
                        WHERE deleted = 0
                        GROUP BY user_id
                    ) p ON u.user_id = p.user_id
                    JOIN (
                        SELECT user_id, COUNT(*) AS cnt
                        FROM threads
                        WHERE deleted = 0
                        GROUP BY user_id
                    ) t ON u.user_id = t.user_id
                    SET u.posts = p.cnt, u.threads = t.cnt";
            if ($conn->query($sql) === FALSE) {
                echo "ERROR: Please try again later [SN1]";
            }
        } else {
            echo "Clearance level too low";
        }
    } else {
        echo "An error has occured SN2";
    }

} else {
    echo "Please login";
}