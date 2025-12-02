<?php

/**
 * Generate home icon SVG
 */
function generateHome() : string {
    $str = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
        <rect width="50" height="100" x="30" y="80"></rect>
        <rect width="50" height="100" x="120" y="80"></rect>
        <rect width="140" height="30" x="30" y="90"></rect>
        <rect width="160" height="20" x="20" y="80"></rect>
        <polygon points="100,10 180,80 20,80" "=""></polygon>
    </svg>';

    return $str;
}