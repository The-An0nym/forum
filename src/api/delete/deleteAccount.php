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
    if(isset($_POST['i'])) {
        $id = $_POST['i'];

        $conn = getConn();
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT u.clearance, b.clearance AS user_clearance 
                    FROM users u 
                JOIN users b 
                    ON b.user_id = '$id' 
                WHERE u.user_id = '$user_id'
                LIMIT 1";

        $result = $conn->query($sql);

        if($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $clearance = $row['clearance'];
            $user_clearance = $row['user_clearance'];

            if(($clearance >= 3 && $user_clearance < $clearance) || $id === $user_id) {
                $type = 1; // Self-deleted
                if($id !== $user_id) {
                    // Push onto history
                    $sql = "INSERT INTO history (id, type, judgement, sender_id)
                    VALUES ('$id', 3, 0, '$user_id')";
                    if ($conn->query($sql) === FALSE) {
                        echo "ERROR: Please try again later [BU0]";
                    }
                    $type = 4; // Banned
                }

                $dtime = date('Y-m-d H:i:s');

                // Flag user as banned or self-deleted
                $sql = "UPDATE users SET deleted = deleted | $type, deleted_datetime = '$dtime' WHERE user_id = '$id'";
                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [BU5]";
                }

                // Soft delete threads
                $sql = "UPDATE threads SET deleted = deleted | 4, deleted_datetime = '$dtime' WHERE user_id = '$id'";
                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [BU6]";
                }

                // Soft delete posts
                $sql = "UPDATE posts SET deleted = deleted | 4, deleted_datetime = '$dtime' WHERE user_id = '$id'";
                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [BU7]";
                }

                // Update ALL threads (posts)
                $sql = "UPDATE threads t
                        JOIN (
                            SELECT thread_id, COUNT(*) AS cnt
                            FROM posts
                            WHERE deleted = 0
                            GROUP BY thread_id
                        ) p ON t.id = p.thread_id
                        SET t.posts = p.cnt";
                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [BU1]";
                }

                // Update ALL category (threads and posts)
                $sql = "UPDATE categories c
                        JOIN (
                            SELECT category_id, COUNT(*) AS cnt, SUM(posts) AS sum
                            FROM threads
                            WHERE deleted = 0
                            GROUP BY category_id
                        ) t ON c.id = t.category_id
                        SET c.threads = t.cnt, c.posts = t.sum";
                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [BU2]";
                }

                // delete session
                $sql = "DELETE FROM sessions WHERE user_id = '$id'";
                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [BU8]";
                }
            } else {
                echo "Clearance level too low";
            }
        } else {
            echo "An error has occured BU8";
        }
    } else {
        echo "An error has occured BU9";
    }

} else {
    echo "Please login";
}