<?php

/**
 * Generate home icon SVG
 */
function generateHome() : string {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
                <path d="M100 115 L85 115 A10 10 0 0 0 75 125 L75 190 L40 190 A10 10 0 0 1 30 180 L30 90 M5 80 L95 10 A10 10 0 0 1 105 10" />
                <path transform="translate(200) scale(-1, 1)" d="M100 115 L85 115 A10 10 0 0 0 75 125 L75 190 L40 190 A10 10 0 0 1 30 180 L30 90 M5 80 L95 10" />
            </svg>';

    return $svg;
}

/**
 * Generate logout icon SVG
 */
function generateLogout() : string {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" style="width:400px;stroke:black; stroke-width: 5px; fill:none;">
                <path d="M120 50 L120 30 A20 20 0 0 0 100 10 L30 10 A20 20 0 0 0 10 30 L10 170 A20 20 0 0 0 30 190 L100 190 A20 20 0 0 0 120 170 L120 150" />
                <path d="M50 100 L150 100 M150 60 L185 95 A12 10 0 0 1 185 105 L150 140" />
            </svg>';

    return $svg;
}