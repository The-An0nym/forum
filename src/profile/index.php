<?php $path = $_SERVER['DOCUMENT_ROOT']; ?>
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

    Username here
    <br>

    <label for="pfp">Choose a profile picture:</label>
    <input type="file" id="pfp" name="avatar" accept="image/png, image/jpeg" />
    <button onclick="uploadImage()">Submit</button>

    <?php
    
    
    
    // Get username and offer option to edit username
    // Give option to upload pfp
    // Other stuff
    
    ?>

    <?php include $path . "/basic/footer.php"; ?>

    <script src="/scripts/profile.js"></script>
    <script src="/scripts/main.js"></script>
</body>
</html>