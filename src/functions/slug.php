<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;

function generateSlug($text) {
    $slug = $baseSlug = slugify($text)
    $i = 0;
    while (slugExistsInDb($slug)) {
        $slug = $baseSlug . '-' . $i;
        $i++;
    }
    return $slug;
}

function slugify($text) {
    $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
    $text = preg_replace('~[^\pL\d]+~u', '-', $text); // Replace non-letter or digits by hyphens
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text); // Transliterate > convert to ASCII
    $text = preg_replace('~[^-\w]+~', '', $text); // Remove unwanted characters
    $text = trim($text, '-'); // Trim
    $text = preg_replace('~-+~', '-', $text); // Remove duplicate hyphens
    $text = strtolower($text); // Lowercase
    return $text ?: 'n-a'; // n-a as fallback
}

function slugExistsInDb($slug): bool {
    $sql = ("SELECT COUNT(*) FROM threads WHERE slug = '$slug'");
    $result = $conn->query($sql);
    return $result->num_rows > 0;
}

?>