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
    <link rel="stylesheet" href="/styles/profile.css"/>
</head>
<body>
    <?php include $path . "/basic/menu.php"; ?>

    <?php
        if(include($path . '/functions/validateSession.php')) {
            $user_id = $_SESSION["user_id"];

            $sql = "SELECT username, image_dir, posts FROM users WHERE user_id = '$user_id' LIMIT 1";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $username = $row["username"];
            $image_dir = $row["image_dir"];
            $posts = $row["posts"];

            ?>  
            <div class="profile-picture">
                <input onchange="loadPreview()" type="file" id="pfp" name="avatar" accept="image/png, image/jpeg, image/jpg" />
                <div class="button-container" id="imageButtons">
                    <button class="save button" onclick="uploadImage()">Save</button>
                    <button class="clear button" onclick="revertImage()">Cancel</button>
                </div>
                <img id="preview" src="/images/profiles/<?php echo $image_dir; ?>">
            </div>
            <div class="username">
                <input oninput="usernameChange()" id="username" value="<?php echo $username; ?>" placeholder="Change username..." />
                <div class="button-container" id="usernameButtons">
                    <button class="save button" onclick="changeUsername()">Save</button>
                    <button class="save button" onclick="revertUsername()">Cancel</button>
                </div>
            </div>
            <div class="password">
                <input oninput="passwordChange()" type="password" id="currPassword" placeholder="Current password...">
                <input oninput="passwordChange()" type="password" id="newPassword" placeholder="New password...">
                <input oninput="passwordChange()" type="password" id="confPassword" placeholder="Confirm password...">
                <div class="button-container" id="passwordButtons">
                    <button class="save button" onclick="changePassword()" id="passwordSave">Save</button>
                    <button class="save button" onclick="revertPassword()" id="usernameSave">Cancel</button>
                </div>
            </div>
            <!-- DELETE ACCOUNT -->
            <div class="posts"><?php echo $posts; ?></div>
                <script>
                    const username = "<?php echo $username; ?>";
                    const image_dir = "<?php echo $image_dir; ?>";
                </script>
            <?php

        } else {
            echo "Please Log in or Sign up to continue...";
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