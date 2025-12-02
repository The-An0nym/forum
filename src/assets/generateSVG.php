<?php

/**
 * Generate home icon SVG
 */
function generateHome() : string {
    $str = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
                <path d="M30 190 L30 90 L10 90 L10 70 L100 10 L190 70 L190 90 L170 90 L170 190 L125 190 L125 125 L75 125 L75 190 Z"></path>
            </svg>';

    return $str;
}