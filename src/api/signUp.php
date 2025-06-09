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

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && json_validate($json_params)) {
    $decoded_params = json_decode($json_params);

    $slug = $decoded_params->s;
    $username = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->u));
    $handle = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->h));
    $password = $decoded_params->p;
            
    if(strlen($username) <= 16 && strlen($username) >= 4 && strlen($password) <= 64 && strlen($password) >= 8 && preg_match('/^[A-z0-9.\-_]*$/i', $handle) === 1 && strlen($handle) <= 16 && strlen($handle) >= 4) {

        $sql = "SELECT username, handle FROM users WHERE username='$username' OR handle='$handle' LIMIT 1";
        $result = $conn->query($sql);
                
        if ($result->num_rows === 0) {
            $user_id = uniqid(rand(), true);
            $secretId = $user_id . base64_encode(random_bytes(64));
            $pswrd = password_hash($password, PASSWORD_DEFAULT);
            $dtime = date('Y-m-d H:i:s');

            $sql = "INSERT INTO users (user_id, image_dir, username, handle, password, created)
            VALUES ('$user_id', '_default.png', '$username', '$handle', '$pswrd', '$dtime')";

            /* SESSION */
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
            $row = $result->fetch_assoc();
            if($row["username"] === $username) {
                echo "Username is already taken!";
            } else if($row["handle"] === $handle) {
                echo "Handle is already taken!"
            } else {
                echo "An error has occured.";
            }
        }
    } else if(preg_match('/^[A-z0-9.\-_]*$/i', $handle) != 1) {
        echo "Only characters <b>a-Z 0-9 - _ .</b> are allowed for the handle";
    } else if(strlen($username) > 16) {
        echo "Max 16. chars allowed for username";
    } else if(strlen($username) < 4) {
        echo "Min. 4 chars needed for username";
    } else if(strlen($handle) > 16) {
        echo "Max 16. chars allowed for handle";
    } else if(strlen($handle) < 4) {
        echo "Min. 4 chars needed for handle";
    } else if(strlen($password) > 64) {
        echo "Max 50. chars allowed for your password";
    } else if(strlen($password) < 8) {
        echo "Min. 8 chars needed for password";
    } else {
        echo "No input";
    }
} else {
    echo "Sign-up failed: Please try again later";
}