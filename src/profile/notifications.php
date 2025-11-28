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

include $path . "/assets/menu.php";
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

            echo '<div id="notifications-container">';
            echo generateNotifsHTML($user_id);
            markAsRead($user_id);
            echo "</div>";

        } else {
            echo "Please Log in or Sign up to continue...";
        }        
    ?>
    </div>


    <?php include $path . "/assets/footer.php"; ?>

    <script src="/scripts/profile.js"></script>
</body>
</html>