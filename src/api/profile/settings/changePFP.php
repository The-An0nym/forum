<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/validateSession.php';
include($path . '/functions/slug.php');

echo response();

function response() {
    // Get connection
    $conn = getConn();

    if(!session_id()) {
    session_start();
    }

    if(!validateSession()) {
        return "Please login to continue";
    }

    if(!isset($_FILES['i'])) {
        return "Invalid or missing arguments";
    }

    $target_dir = $path . "/images/profiles/";
    $image_id = uniqid(rand(), true);
    $imageFileType = strtolower(pathinfo(basename($_FILES["i"]["name"]),PATHINFO_EXTENSION));

    $target_file = $target_dir . $image_id . "." . $imageFileType;
    $pass = true;
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["i"]["tmp_name"]);
    if($check === false) {
        return "File is not an image.";
    }

        // Check if file already exists
        if (file_exists($target_file)) {
            return "This file already exists.";
        }

        // Check file size
        if ($_FILES["i"]["size"] > 1024 * 1024) {
            return "Image must be less than 1MB";
        }

        // Check file type
        if($imageFileType == "png") {
            $image = imageCreateFromPng($_FILES["i"]["tmp_name"]);
        } else if($imageFileType == "jpg" || $imageFileType == "jpeg") {
            $image = imageCreateFromJpeg($_FILES["i"]["tmp_name"]);
        } else {
            return "Image must be jpg or png";
        }

        list($width, $height, $type, $attr) = $check;

        // Check resolution
        if($width > 1024 || $height > 1024) {
            return "Image size must be below or equal to 1024 x 1024px";
        }
        if($width < 128 || $height < 128) {
            return "Image size must be bigger or equal to than 128 x 128px";
        }

        // Crop to square
        if($width < $height) {
            $image = imagecrop($image, ['x' => 0, 'y' => 0, 'width' => $width, 'height' => $width]);
        } else if($width > $height) {
            $image = imagecrop($image, ['x' => 0, 'y' => 0, 'width' => $height, 'height' => $height]);
        }
        

        // Add file to server
        if($imageFileType == "png") {
            imagepng($image, $target_file);
        } else if($imageFileType == "jpg" || $imageFileType == "jpeg") {
            imagejpeg($image, $target_file);
        }
        
        
        // Append path to user
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
            return "An error has occured while trying to update your profile picture";
        }
}