<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/moderation.php';
include $path . '/functions/statCount.php';
include $path . '/functions/validateSession.php';

// Get connection
$conn = getConn();

if(!session_id()) {
  session_start();
} 

if(validateSession()) {
    if(isset($_POST['i'])) {
        $id = $_POST['i'];

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
                countForPost($id, true);

                $type = 1;

                if($post_user_id !== $user_id) {
                    $type = 2;
                    // Push onto history
                    $sql = "INSERT INTO history (id, type, judgement, sender_id)
                    VALUES ('$id', 0, 1, '$user_id')";
                    if ($conn->query($sql) === FALSE) {
                        echo "ERROR: Please try again later [RP2]";
                    }
                }

                deletePost($id, $type, true);
                
            } else {
                echo "Clearance level too low";
            }
        } else {
            echo "An error has occured RP4";
        }
    } else {
        echo "An error has occured RP5";
    }

} else {
    echo "Please login";
}