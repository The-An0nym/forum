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
        $sql = "SELECT u.clearance, b.clearanse AS user_clearance 
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
            $post_user_id = $row['user_id'];
            $user_id === $_SESSION["user_id"];

            if($clearance >= 4 && $user_clearance < $clearance) {
                // Push onto history
                $sql = "INSERT INTO history (id, type, judgement, sender_id)
                VALUES ('$id', 2, 0, '$user_id')";
                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [DU0]";
                }
                
                // Demote user
                $sql = "UPDATE users SET clearance = clearance - 1 WHERE user_id = '$id'";
                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [DU1]";
                }
            } else {
                echo "Clearance level too low";
            }
        } else {
            echo "An error has occured DU2";
        }
    } else {
        echo "An error has occured DU3";
    }

} else {
    echo "Please login";
}