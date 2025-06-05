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
        $sql = "SELECT clearance FROM users WHERE user_id = '$user_id'";

        $result = $conn->query($sql);
        if($result->num_rows === 1) {
            $clearance = $result->fetch_assoc()['clearance'];
            if($clearance >= 1) {
                // (Soft) delete post
                $sql = "UPDATE posts SET deleted = 1 WHERE post_id = '$id'"
                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [DP0]";
                } else {
                    echo "Deleted post: $id";
                }
            } else {
                echo "Clearance level too low";
            }
        } else {
            echo "An error has occured DP1";
        }
    } else {
        echo "An error has occured DP2";
    }

} else {
    echo "Please login";
}