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

include $path . "/basic/menu.php";
include $path . '/functions/require/moderationHistory.php' ;
include $path . '/functions/validateSession.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quir | Notifications</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="stylesheet" href="/styles/profile.css"/>
</head>
<body>
    <?php generateMenu() ?>
    <?php generateProfileMenu() ?>

    <div class="container">
    <?php
        if(validateSession()) {
            $user_id = $_SESSION["user_id"];

            $sql = "SELECT username, handle, image_dir, posts, threads, clearance FROM users WHERE user_id = '$user_id' LIMIT 1";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $username = $row["username"];
            $handle = $row["handle"];
            $image_dir = $row["image_dir"];
            $posts = $row["posts"];
            $threads = $row["threads"];
            $clearance = $row["clearance"];

            ?>  
            To come...
                <script>
                    const username = "<?= $username; ?>";
                    const handle = "<?= $handle; ?>"
                    const image_dir = "<?= $image_dir; ?>";
                </script>
            <?php

        } else {
            echo "Please Log in or Sign up to continue...";
        }        
    ?>
    </div>


    <?php include $path . "/basic/footer.php"; ?>

    <script src="/scripts/profile.js"></script>
</body>
</html>