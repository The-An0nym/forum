<html>
<body>

<form id="signup-form" action="/signup.php" method="POST">
    <label for="username">Username:</label>
    <input id="username" name="username"><br />
    <label for="password">Password:</label>
    <input type="password" id="password" name="password">
    <button type="submit" name="submit">Submit</button>

</form>

<div id="Error" style="display:none;">AN ERROR HAS OCCURED</div>

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
           header("Location: signup.php");
           exit;
        }
        if (isset($_SESSION['_username'])) {
            $username = $_SESSION['_username'];
            $password = $_SESSION['_password'];
            
            if(!empty($username) && !empty($password) && strlen($username) <= 20 && strlen($username) >= 4 && strlen($password) <= 80 && strlen($password) >= 8 && preg_match('/^[A-z0-9.\-+]*$/i', $username) == 1) {
                 // idk about mysql_real_escape_string ??
                $usr = htmlspecialchars($username);

                $sql = "SELECT * FROM users WHERE username='$usr'";
                $result = $conn->query($sql);

                if ($result->num_rows === 0) {
                    $usr_id = uniqid(rand(), true);
                    $secretId = $usr_id . base64_encode(random_bytes(64));
                    $pswrd = password_hash($password, PASSWORD_DEFAULT);
                    $dtime = date('Y-m-d H:i:s');

                    $sql = "INSERT INTO users (user_id, username, password, created)
                    VALUES ('$usr_id', '$usr', '$pswrd', '$dtime')";

                    if ($conn->query($sql) === TRUE) {
                        header('Location: https://quir.free.nf/login.php');
                        exit;
                    } else {
                      echo "Error: " . $sql . "<br>" . $conn->error;
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
            } else {
                echo "no input<br>";
            }
           unset($_SESSION['_username']); unset($_SESSION['_password']);
        }

        $conn->close();
        ?>
</body>
</html>