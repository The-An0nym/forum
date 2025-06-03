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
        
if (isset($_POST['p'], $_POST['np'])) {
    $password = htmlspecialchars($_POST["p"]);
    $newPassword = htmlspecialchars($_POST["np"]);
    $user_id = $_SESSION["user_id"];
            
    if(strlen($password) <= 50 && strlen($password) >= 8 && strlen($newPassword) <= 50 && strlen($newPassword) >= 8) {
        $sql = "SELECT password FROM users WHERE user_id='$user_id'";
        $result = $conn->query($sql);

        if ($result->num_rows === 1) {
            $res = $result->fetch_assoc();
            $hashedPassword = $res["password"];

            if(password_verify($password, $hashedPassword)) {
                $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                $sql = "UPDATE users SET password = '$newHashedPassword' WHERE user_id='$user_id'";

                if ($conn->query($sql) === FALSE) {
                    echo "Changing password failed: Please try again later";
                }

            } else {
                echo "Wrong password";
            }

        } else {
            echo "This account does not exist! Try signing up instead?";
        }
        // MAKE WAY

    } else if(strlen($password) > 50 || strlen($newPassword) > 50) {
        echo "Max 50. chars allowed for password";
    } else if(strlen($password) < 8 || strlen($newPassword) < 8) {
        echo "Min. 8 chars needed for password";
    } else {
        echo "No input";
    }
} else {
    echo "ERROR: Please try again later [CP1]";
}