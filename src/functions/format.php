<?php

// Regex based formatting - tis a mess
// TODO a lot
// Currently only supports bold and italics (untested)
// Need to be at least 2 chars long
function format(string $text = "") : string {
    if($text === "") {
        return "";
    }

    $pattern = '/(\*\*[^ ]([^*]*)[^ ]\*\*)/'; // Bold
    $text = preg_replace($pattern, '<b>${1}</b>', $text);
    // Cleanup
    $pattern = '/\<b\>\*\*/';
    $text = preg_replace($pattern, '<b>', $text);
    $pattern = '/\*\*\<\/b\>/';
    $text = preg_replace($pattern, '</b>', $text);


    $pattern = '/(\*[^ ]([^*]*)[^ ]\*)/'; // Italics
    $text = preg_replace($pattern, '<i>${1}</i>', $text);
    // Cleanup
    $pattern = '/\<i\>\*/';
    $text = preg_replace($pattern, '<i>', $text);
    $pattern = '/\*\<\/i\>/';
    $text = preg_replace($pattern, '</i>', $text);

    return $text;
}