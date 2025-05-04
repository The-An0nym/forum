<?php
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
?>