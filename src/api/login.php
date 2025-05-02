<?php

$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
    
if(isset($_POST["u"], $_POST["p"])) {
   
    $username = htmlspecialchars($_POST["u"]); // idk about mysql_real_escape_string ??
    $password = $_POST["p"];

    $sql = "SELECT password, user_id FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $res = $result->fetch_assoc();
        $hashedPassword = $res["password"];
        $user_id = $res["user_id"];

        if(password_verify($password, $hashedPassword)) {
            $ip = $_SERVER['REMOTE_ADDR'];
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $session_id = base64_encode(random_bytes(64));
            $dtime = date('Y-m-d H:i:s');
            $sql = "INSERT INTO sessions (user_id, ip, user_agent, session_id, datetime)
            VALUES ('$user_id', '$ip', '$user_agent', '$session_id', '$dtime')";

            if ($conn->query($sql) === TRUE) {
                session_start();
                $_SESSION['user_id'] = $user_id;
                $_SESSION['session_id'] = $session_id;
                include($path . '/functions/deleteExpiredSessions.php');
                exit;

            } else {
              echo "Login attempt failed, please try again";
            }

        } else {
            echo "Wrong password";
        }

    } else {
        echo "This account does not exist!<br>Try signing up instead?";
    }

} else {
    echo "ERROR: L0";
}

$conn->close();
?>