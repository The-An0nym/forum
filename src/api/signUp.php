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
        
if (isset($_POST['u'], $_POST['p1'], $_POST['p2'])) {
    $username = htmlspecialchars($_POST["u"]); // idk about mysql_real_escape_string ??
    $password = $_POST["p1"];
    $password2 = $_POST["p2"];
            
    if(strlen($username) <= 16 && strlen($username) >= 4 && strlen($password) <= 64 && strlen($password) >= 8 && preg_match('/^[A-z0-9.\-+]*$/i', $username) == 1 && $password === $password2) {

        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = $conn->query($sql);
                
        if ($result->num_rows === 0) {
            $user_id = uniqid(rand(), true);
            $secretId = $user_id . base64_encode(random_bytes(64));
            $pswrd = password_hash($password, PASSWORD_DEFAULT);
            $dtime = date('Y-m-d H:i:s');

            $sql = "INSERT INTO users (user_id, image_id, username, password, created)
            VALUES ('$user_id', '_default.png', '$username', '$pswrd', '$dtime')";

            if ($conn->query($sql) === TRUE) {
                $ip = $_SERVER['REMOTE_ADDR'];
                $user_agent = $_SERVER['HTTP_USER_AGENT'];
                $session_id = base64_encode(random_bytes(64));
                $dtime = date('Y-m-d H:i:s');
                $sql = "INSERT INTO sessions (user_id, ip, user_agent, session_id, datetime)
                VALUES ('$user_id', '$ip', '$user_agent', '$session_id', '$dtime')";

                if ($conn->query($sql) === TRUE) {
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['session_id'] = $session_id;
                    include($path . '/functions/deleteExpiredSessions.php');
                    exit;

                } else {
                    echo "Sign-up failed: Please try again later";
                }
            } else {
              echo "Sign-up failed: Please try again later";
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
    } else if(strlen($username) > 80) {
        echo "Max 50. chars allowed for your password";
    } else if(strlen($username) < 8) {
        echo "Min. 8 chars needed for password";
    } else if($password !== $password2) {
        echo "Passwords do not match";
    } else {
        echo "No input";
    }
} else {
    echo "Sign-up failed: Please try again later";
}

$conn->close();