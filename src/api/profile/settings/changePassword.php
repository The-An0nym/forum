<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/validateSession.php';

echo response();

function response() {
    // Get connection
    $conn = getConn();

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    if(!session_id()) {
    session_start();
    } 

    if(!validateSession()) {
        return "Please login to continue";
    }

    if (!isset($_POST['p'], $_POST['np'])) {
        return "Invalid or missing argument(s)";
    }

    $password = htmlspecialchars($_POST["p"]);
    $newPassword = htmlspecialchars($_POST["np"]);
    $user_id = $_SESSION["user_id"];

    if(strlen($password) > 50 || strlen($newPassword) > 50) {
        return "Max 50. chars allowed for password";
    } else if(strlen($password) < 8 || strlen($newPassword) < 8) {
        return "Min. 8 chars needed for password";
    }

    $sql = "SELECT password FROM users WHERE user_id='$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows !== 1) {
        return "Please login to continue";
    }

    $res = $result->fetch_assoc();
    $hashedPassword = $res["password"];

    if(!password_verify($password, $hashedPassword)) {
        return "Wrong password";
    }
    
    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $sql = "UPDATE users SET password = '$newHashedPassword' WHERE user_id='$user_id'";

    if ($conn->query($sql) === FALSE) {
        return "Changing password failed: Please try again later";
    }
}