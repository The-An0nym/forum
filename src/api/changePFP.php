<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include($path . '/functions/slug.php');

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(!session_id()) {
  session_start();
}

if(include($path . '/functions/validateSession.php')) {
    if(isset($_FILES['i']))
    {
        $target_dir = $path . "/images/profiles/";
        $image_id = uniqid(rand(), true);
        $imageFileType = strtolower(pathinfo($_FILES["i"]["name"],PATHINFO_EXTENSION));

        $target_file = $target_dir . $image_id . "." . $imageFileType;
        $pass = true;
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["i"]["tmp_name"]);
        if($check !== false) {
            echo "File is not an image.";
            $pass = false;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $pass = false;
        }

        // Check file size
        if ($_FILES["i"]["size"] > 2 * 1024 * 1024) {
            echo "Sorry, your file is too large.";
            $pass = false;
        }

        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            echo "Only JPG, JPEG & PNG files are allowed.";
            $pass = false;
        }

        // Add file to server
        if ($pass) {
            if (move_uploaded_file($_FILES["i"]["tmp_name"], $target_file)) {
                echo "The file ". htmlspecialchars( basename( $_FILES["i"]["name"])). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
                $pass = false;
            }
        }
        
        // Database stuff
        if($pass) {
            $user_id = $_SESSION["user_id"];

            // Get previous image path
            $sql = "SELECT image_id FROM users WHERE user_id = '$user_id'";
            $conn->query($sql);
            $previous_image_id = $result->fetch_assoc()["image_id"];

            // Delete previous image
            unlink($target_dir . $previous_image_id);

            // Update image path
            $sql = "UPDATE users SET image_id = '$image_id'";
            $conn->query($sql);
            if ($conn->query($sql) === FALSE) {
                echo "An error has occured [CP0]";
            }
        }

    }


} else {
    echo "Please Login to change your profile picture";
}

$conn->close();