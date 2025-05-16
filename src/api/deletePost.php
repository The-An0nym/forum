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

if(include $path . "/validateSession.php") {
    if(isset($_POST['i'])) {
        $id = $_POST['i'];

        $conn = getConn();
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT clearance FROM users WHERE user_id = '$user_id'";

        $result = $conn->query($sql);
        if($result->num_rows === 1) {
            $clearance = $result->fetch_assoc()['clearance'];
            if($clearance > 1) {
                // Push deleted post onto a history
                // Delete post
                echo "Deleted post: $id";
            } else {
                echo "Clearance level too low";
            }
        } else {
            echo "An error has occured DP0";
        }
    } else {
        echo "An error has occured DP1";
    }

} else {
    echo "Please login";
}