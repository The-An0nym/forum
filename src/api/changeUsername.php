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
    $username = htmlspecialchars($_POST["u"]); // idk about mysql_real_escape_string ??
            
    if(strlen($username) <= 16 && strlen($username) >= 4 && preg_match('/^[A-z0-9.\-+]*$/i', $username) == 1) {

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
    } else if(preg_match('/^[A-z0-9.\-+]*$/i', $username) != 1) {
        echo "Only characters <b>a-Z 0-9 + - _ .</b> are allowed";
    } else if(strlen($username) > 20) {
        echo "Max 20. chars allowed for username";
    } else if(strlen($username) < 4) {
        echo "Min. 4 chars needed for username";
    } else {
        echo "No input";
    }
} else {
    echo "ERROR: Please try again later [CU1]";
}

$conn->close();