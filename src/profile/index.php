<?php $path = $_SERVER['DOCUMENT_ROOT']; 

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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quir | Profile</title>
    <link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
    <?php include $path . "/basic/menu.php"; ?>

    <?php
        if(include($path . '/functions/validateSession.php')) {
            $user_id = $_SESSION["user_id"];

            $sql = "SELECT username, image_dir, posts FROM users WHERE user_id = '$user_id'";
            $result = $conn->query($sql);
            $username = $result->fetch_assoc()["username"];
            $image_dir = $result->fetch_assoc()["image_dir"];
            $posts = $result->fetch_assoc()["posts"];

            echo '<label for="pfp">Choose a profile picture:</label>
                <input type="file" id="pfp" name="avatar" accept="image/png, image/jpeg" />
                <button onclick="uploadImage()">Submit</button>
                <img id="preview" src="/images/profiles/' . $image_dir . '">
                <input id="username" value="' . $username . '" placeholder="Change username..." />
                <button onclick="changeUsername()">Change username</button>
                <div class="posts">' . $posts . '</div>';

        } else {
            echo "Please Log in or Sign up to continue..."
        }
    
    
    // Get username and offer option to edit username
    // Give option to upload pfp
    // Other stuff
    
    ?>

    <?php include $path . "/basic/footer.php"; ?>

    <script src="/scripts/profile.js"></script>
    <script src="/scripts/main.js"></script>
</body>
</html>