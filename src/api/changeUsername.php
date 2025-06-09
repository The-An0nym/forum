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
        
if (isset($_POST['u'])) {
    $username = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($_POST['u']));
    
    if(strlen($username) <= 16 && strlen($username) >= 4) {

        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = $conn->query($sql);

        $user_id = $_SESSION["user_id"];
                
        if ($result->num_rows === 0) {
            $sql = "UPDATE users SET username = '$username' WHERE user_id = '$user_id'";

            if ($conn->query($sql) === FALSE) {
                echo "ERROR: Please try again later [CU0]";
            }
        } else {
            echo "Username is already taken!";
        }
    } else if(strlen($username) > 16) {
        echo "Max 16. chars allowed for username";
    } else if(strlen($username) < 4) {
        echo "Min. 4 chars needed for username";
    } else {
        echo "No input";
    }
} else {
    echo "ERROR: Please try again later [CU1]";
}