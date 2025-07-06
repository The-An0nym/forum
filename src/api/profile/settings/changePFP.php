<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/validateSession.php';
include($path . '/functions/slug.php');
include $path . '/functions/errors.php' ;

echo response();

function response() {
    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    }

    if(!validateSession()) {
        return getError("login");
    }

    if(!isset($_FILES['i'])) {
        return getError("args")
    }

    $target_dir = $path . "/images/profiles/";
    $image_id = uniqid(rand(), true);
    $imageFileType = strtolower(pathinfo(basename($_FILES["i"]["name"]),PATHINFO_EXTENSION));

    $target_file = $target_dir . $image_id . "." . $imageFileType;
    $pass = true;
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["i"]["tmp_name"]);
    if($check === false) {
        return getError("imgType");
    }

        // Check if file already exists
        if (file_exists($target_file)) {
            return getError("tImg");
        }

        // Check file size
        if ($_FILES["i"]["size"] > 1024 * 1024) {
            return getError("imgMaxMB");
        }

        // Check file type
        if($imageFileType == "png") {
            $image = imageCreateFromPng($_FILES["i"]["tmp_name"]);
        } else if($imageFileType == "jpg" || $imageFileType == "jpeg") {
            $image = imageCreateFromJpeg($_FILES["i"]["tmp_name"]);
        } else {
            return getError("imgType");
        }

        list($width, $height, $type, $attr) = $check;

        // Check resolution
        if($width > 1024 || $height > 1024) {
            return getError("imgMax");
        }
        if($width < 128 || $height < 128) {
            return getError("imgMax");
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
            return getError() . " [CPF0]";
        }
}