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
        // Implement random UUID for image name
        // Make sure the path is saved in the database
        // Delete the previous image that served for that user
        $target_file = $target_dir . basename($_FILES["i"]["name"]);
        $uploadOk = true;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["i"]["tmp_name"]);
        if($check !== false) {
            echo "File is not an image.";
            $uploadOk = false;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = false;
        }

        // Check file size
        if ($_FILES["i"]["size"] > 2 * 1024 * 1024) {
            echo "Sorry, your file is too large.";
            $uploadOk = false;
        }

        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = false;
        }

        if (!$uploadOk) {
            echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["i"]["tmp_name"], $target_file)) {
                echo "The file ". htmlspecialchars( basename( $_FILES["i"]["name"])). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }

    }


} else {
    echo "Please Login to change your profile picture";
}

$conn->close();