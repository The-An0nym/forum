<?php
$configs = include('functions/.config.php');
extract($configs);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

session_start();
if (isset($_POST["submit"])) {
   $_SESSION['_username'] = $_POST['username'];
   $_SESSION['_password'] = $_POST['password']; 
   header("Location: login.php");
   exit;
}
if (isset($_SESSION['_username'])) {
    $usernameS = $_SESSION['_username'];
    $password = $_SESSION['_password'];
            
    if(!empty($usernameS) && !empty($password)) {
         // idk about mysql_real_escape_string ??
        $usr = htmlspecialchars($usernameS);

        $sql = "SELECT password, user_id FROM users WHERE username='$usr'";
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
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['session_id'] = $session_id;
                    include('functions/deleteExpiredSessions.php');
                    header('Location: https://quir.free.nf');
                    unset($_SESSION['_username']); unset($_SESSION['_password']);
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
        echo "no input<br>";
    }
   unset($_SESSION['_username']); unset($_SESSION['_password']);
}

$conn->close();
?>
<html>
<body>

<form action="/login.php" method="POST">
    <label for="username">Username:</label>
    <input id="username" name="username"><br />
    <label for="password">Password:</label>
    <input type="password" id="password" name="password">
    <button type="submit" name="submit">Submit</button>

</form>

<div id="Error" style="display:none;">AN ERROR HAS OCCURED</div>


</body>
</html>