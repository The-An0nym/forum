<?php $path = $_SERVER['DOCUMENT_ROOT']; 

require_once $path . '/functions/.connect.php' ;

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(!session_id()) {
  session_start();
}

require_once $path . "/assets/menu.php";
require_once $path . '/functions/require/moderationHistory.php' ;
require_once $path . '/functions/validateSession.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quir | Settings</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="stylesheet" href="/styles/profile/settings.css"/>
</head>
<body>
    <?php generateMenu() ?>

    <div id="global">
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
            <div id="profile-picture">
                <img id="preview" src="/images/profiles/<?= $image_dir; ?>">
                <label for="pfp"><span class="button">Change pfp...</span></label>
                <input onchange="loadPreview()" type="file" id="pfp" name="avatar" accept="image/png, image/jpeg, image/jpg" />
                <div class="button-container" id="imageButtons">
                    <button class="button action-button" onclick="uploadImage()">Save</button>
                    <button class="button" onclick="revertImage()">Cancel</button>
                </div>
            </div>
            <div id="user-settings">
                <div class="setting-item">
                    <label for="username">Username</label>
                    <input class="setting-input" oninput="usernameChange()" id="username" value="<?= $username; ?>" placeholder="Change username..." />
                    <span class="setting-input-buttons" id="usernameButtons">
                        <button class="button action-button" onclick="changeUsername()">Save</button>
                        <button class="button" onclick="revertUsername()">Cancel</button>
                    </span>
                </div>
                <div class="setting-item">
                    <label for="handle">Handle</label>
                    <input class="setting-input" oninput="handleChange()" id="handle" value="<?= $handle; ?>" placeholder="Change handle..." />
                    <span class="setting-input-buttons" id="handleButtons">
                        <button class="button action-button" onclick="changeHandle()">Save</button>
                        <button class="button" onclick="revertHandle()">Cancel</button>
                    </span>
                </div>
                <div class="setting-item">
                    <label for="currPassword">Password</label>
                    <span class="setting-input">
                        <input oninput="passwordChange()" type="password" id="currPassword" placeholder="Current password...">
                        <input oninput="passwordChange()" type="password" id="newPassword" placeholder="New password...">
                        <input oninput="passwordChange()" type="password" id="confPassword" placeholder="Confirm password...">
                    </span>
                    <span class="setting-input-buttons" id="passwordButtons">
                        <button class="button action-button" onclick="changePassword()" id="passwordSave">Save</button>
                        <button class="button" onclick="revertPassword()" id="usernameSave">Cancel</button>
                    </span>
                </div>
                <div class="posts">Posts: <?php echo $posts; ?></div>
                <div class="threads">Threads: <?php echo $threads; ?></div>
                <div class="delete-account">
                    <button class="deleted" onclick="createConfirmation('delete your account', '<?= $handle ?>', deleteAccount, '<?= $user_id ?>')">Delete account</button>
                </div>
            </div>
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


    <?php require_once $path . "/assets/footer.php"; ?>

    <script src="/scripts/profile.js"></script>
</body>
</html>