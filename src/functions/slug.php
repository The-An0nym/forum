<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;

function generateSlug($text) {
    $baseSlug = slugify($text);

    $conn = getConn();

    $sql = "SELECT COUNT(*) AS num FROM threads WHERE slug = '$baseSlug' LIMIT 1";
    $result = $conn->query($sql);
    if ($result->fetch_assoc()["num"] == 0) {
        $slug = $baseSlug;
    } else {
        $slug = $baseSlug . '-%';

        $sql = "SELECT COUNT(*) AS num FROM threads WHERE slug LIKE '$slug'";
        $result = $conn->query($sql);
        if($result->num_rows > 0) {
            $slug = $baseSlug . "-" . $result->fetch_assoc()["num"];
        } else {
            $slug = $baseSlug . '-1';
        }
    }
    
    return $slug;
}

function slugify($text) {
    $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
    $text = preg_replace('~[^\pL\d]+~u', '-', $text); // Replace non-letter or digits by hyphens
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text); // Transliterate > convert to ASCII
    $text = preg_replace('~[^-\w]+~', '', $text); // Remove unwanted characters
    $text = trim($text, '-'); // Trim whitespace
    $text = preg_replace('~-+~', '-', $text); // Remove duplicate hyphens
    $text = strtolower($text); // Lowercase
    return $text ?: 'n-a'; // n-a as fallback
}