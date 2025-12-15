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

            ?>  
            <div id="profile-picture">
                <img id="preview" src="/images/profiles/<?= $image_dir; ?>">
                <label for="pfp-input"><div class="button">Select file</div></label>
                <input onchange="loadPreview()" type="file" id="pfp-input" name="avatar" accept="image/png, image/jpeg, image/jpg" />
                <button class="action-button disabled" id="imageButton" onclick="uploadImage()">Save</button>
            </div>
            <div id="user-settings">
                <div class="setting-item">
                    <span>Username</span>
                    <span class="setting-input">
                        <input oninput="usernameChange()" id="username" value="<?= $username; ?>" placeholder="Change username..." />
                        <button class="action-button disabled" id="usernameButton" onclick="changeUsername()">Save</button>
                    </span>
                </div>
                <div class="setting-item">
                    <span>Handle</span>
                    <span class="setting-input">
                        <input oninput="handleChange()" id="handle" value="<?= $handle; ?>" placeholder="Change handle..." />
                        <button class="action-button disabled" id="handleButton" onclick="changeHandle()">Save</button>
                    </span>
                </div>
                <div class="setting-item">
                    <span>Password</span>
                    <span class="setting-input">
                        <input oninput="passwordChange()" type="password" id="currPassword" placeholder="Current password...">
                        <input oninput="passwordChange()" type="password" id="newPassword" placeholder="New password...">
                        <input oninput="passwordChange()" type="password" id="confPassword" placeholder="Confirm password...">
                        <button class="action-button disabled" onclick="changePassword()" id="passwordButton">Save</button>
                    </span>
                </div>
                <div class="setting-item">
                    <span>Appearance</span>
                    <span class="setting-input">
                        <select onchange="appearanceChange()" id="appearanceSelect">
                            <option value="0">System Preference</option>
                            <option value="1">Light</option>
                            <option value="2">Dark</option>
                        </select>
                    </span>
                </div>
                <div id="danger-zone">Danger zone</div>
                <div class="setting-item">
                    <span>Delete account?</span>
                    <button class="danger-button" onclick="createConfirmation('delete your account', '<?= $handle ?>', deleteAccount, '<?= $user_id ?>')">Delete account</button>
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