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
        $imageFileType = strtolower(pathinfo(basename($_FILES["i"]["name"]),PATHINFO_EXTENSION));

        $target_file = $target_dir . $image_id . "." . $imageFileType;
        $pass = true;
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["i"]["tmp_name"]);
        if($check === false) {
            echo "File is not an image.";
            $pass = false;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $pass = false;
        }

        // Check file size
        if ($_FILES["i"]["size"] > 1024 * 1024) {
            echo "Image must be less than 1MB";
            $pass = false;
        }

        // Check file type
        if($imageFileType == "png") {
            $image = imageCreateFromPng($_FILES["i"]["tmp_name"]);
        } else if($imageFileType == "jpg" || $imageFileType == "jpeg") {
            $image = imageCreateFromJpeg($_FILES["i"]["tmp_name"]);
        } else {
            echo "Image must be jpg or png";
            $pass = false;
        }

        list($width, $height, $type, $attr) = $check;

        // Check resolution
        if($pass) {
            if($width > 512 || $height > 512) {
                echo "Image size must be below 512 x 512px";
                $pass = false;
            }
            if($width < 128 || $height < 128) {
                echo "Image size must be bigger than 128 x 128px";
                $pass = false;
            }
        }

        // Crop to square
        if($pass) {
            if($width < $height) {
                $image = imagecrop($image, ['x' => 0, 'y' => 0, 'width' => $width, 'height' => $width]);
            } else if($width > $height) {
                $image = imagecrop($image, ['x' => 0, 'y' => 0, 'width' => $height, 'height' => $height]);
            }
        }

        // Add file to server
        if ($pass) {
            if($imageFileType == "png") {
                imagepng($image, $target_file);
            } else if($imageFileType == "jpg" || $imageFileType == "jpeg") {
                imagejpeg($image, $target_file);
            }
        }
        
        // Database stuff
        if($pass) {
            $user_id = $_SESSION["user_id"];

            // Get previous image path
            $sql = "SELECT image_dir FROM users WHERE user_id = '$user_id'";
            $result = $conn->query($sql);
            $previous_image_dir = $result->fetch_assoc()["image_dir"];

            if($previous_image_dir != "" && $previous_image_dir != "_default.png") {
                // Delete previous image
                $previous_dir = $target_dir . $previous_image_dir;
                unlink($previous_dir);
            }

            $image_dir = $image_id . "." . $imageFileType;

            // Update image path
            $sql = "UPDATE users SET image_dir = '$image_dir' WHERE user_id = '$user_id'";
            if ($conn->query($sql) === FALSE) {
                echo "An error has occured [CP1]";
            }
        }

    }


} else {
    echo "Please Login to change your profile picture";
}