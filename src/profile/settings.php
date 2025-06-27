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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quir | Settings</title>
    <link rel="stylesheet" href="/styles/main.css" />
    <link rel="stylesheet" href="/styles/profile.css"/>
</head>
<body>
    <?php generateMenu([]) ?>

    <div class="container">
    <?php
        if(include($path . '/functions/validateSession.php')) {
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
            <div class="profile-picture">
            <label for="pfp"><span class="button">Change pfp...</span></label>
                <input onchange="loadPreview()" type="file" id="pfp" name="avatar" accept="image/png, image/jpeg, image/jpg" />
                <div class="button-container" id="imageButtons">
                    <button class="save button" onclick="uploadImage()">Save</button>
                    <button class="clear button" onclick="revertImage()">Cancel</button>
                </div>
                <img id="preview" src="/images/profiles/<?= $image_dir; ?>">
            </div>
            <div class="username">
                <input oninput="usernameChange()" id="username" value="<?= $username; ?>" placeholder="Change username..." />
                <div class="button-container" id="usernameButtons">
                    <button class="save button" onclick="changeUsername()">Save</button>
                    <button class="save button" onclick="revertUsername()">Cancel</button>
                </div>
            </div>
            <div class="handle">
                <input oninput="handleChange()" id="handle" value="<?= $handle; ?>" placeholder="Change handle..." />
                <div class="button-container" id="handleButtons">
                    <button class="save button" onclick="changeHandle()">Save</button>
                    <button class="save button" onclick="revertHandle()">Cancel</button>
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
            <div class="posts">Posts: <?php echo $posts; ?></div>
            <div class="threads">Threads: <?php echo $threads; ?></div>
            <div class="delete-account">
                <button class="deleted" onclick="createConfirmation('delete your account', '<?= $handle ?>', deleteAccount, '<?= $user_id ?>')">Delete account</button>
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


    <?php include $path . "/basic/footer.php"; ?>

    <script src="/scripts/profile.js"></script>
</body>
</html>