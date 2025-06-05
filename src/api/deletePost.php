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
            $row = $result->fetch_assoc()
            $clearance = $row['clearance'];
            $post_user_id = $row['user_id'];
            $user_id === $_SESSION["user_id"];

            if($post_user_id === $user_id) {
                // (Soft) delete post
                $sql = "UPDATE posts SET deleted = 1 WHERE post_id = '$id'";
                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [DP0]";
                }
            } else if($clearance >= 1) {
                // (Soft) delete post
                $sql = "UPDATE posts SET deleted = 1 WHERE post_id = '$id'";
                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [DP1]";
                }
                
                // Push onto history
                $sql = "INSERT INTO history (id, type, judgement, sender_id)
                VALUES ('$id', 0, 0, '$user_id')"
                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [DP2]";
                }
            } else {
                echo "Clearance level too low";
            }
        } else {
            echo "An error has occured DP3";
        }
    } else {
        echo "An error has occured DP4";
    }

} else {
    echo "Please login";
}