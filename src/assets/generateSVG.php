<?php

/**
 * Generate home icon SVG
 */
function generateHome() : string {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
                <path d="M100 115 L85 115 A10 10 0 0 0 75 125 L75 190 L40 190 A10 10 0 0 1 30 180 L30 90 M5 80 L95 10 A10 12 0 0 1 106 11" />
                <path transform="translate(200) scale(-1, 1)" d="M100 115 L85 115 A10 10 0 0 0 75 125 L75 190 L40 190 A10 10 0 0 1 30 180 L30 90 M5 80 L95 10" />
            </svg>';

    return $svg;
}

/**
 * Generate logout icon SVG
 */
function generateLogout() : string {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
                <path d="M120 50 L120 30 A20 20 0 0 0 100 10 L30 10 A20 20 0 0 0 10 30 L10 170 A20 20 0 0 0 30 190 L100 190 A20 20 0 0 0 120 170 L120 150" />
                <path d="M50 100 L150 100 M150 60 L185 95 A12 10 0 0 1 185 105 L150 140" />
            </svg>';

    return $svg;
}

/**
 * Generate send icon SVG
 */
function generateSend() : string {
    $svg = '<svg id="send-button" class="svg-send-button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
                <path d="M25 65 L10 30 A16 16 0 0 1 30 10 L180 90 A20 14 0 01 180 110 L30 190 A16 16 0 0 1 10 170 L25 135 A26 35 0 0 1 45 115 L80 105 A10 6 0 0 0 80 95 L45 85 A26 35 0 0 1 25 65 Z" />
            </svg>';

    return $svg;
}

/**
 * Generate star icon SVG 
 */
function generateStar(bool $filled = false) : string {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="-100 -100 200 200" class="auth-star' . ($filled ? " filled" : "") . '">
                <path d="M-22 -25 L-5 -75 A10 30 0 0 1 5 -75 L22 -25 L70 -25 A30 10 -15 0 1 80 -16 L35 17 L50 65 A10 40 -40 0 1 42 72 L0 43"></path>
                <path transform="scale(-1, 1)" d="M-22 -25 L-5 -75 A10 30 0 0 1 5 -75 L22 -25 L70 -25 A30 10 -15 0 1 80 -16 L35 17 L50 65 A10 40 -40 0 1 42 72 L0 43"></path>
            </svg>';

    return $svg;
}

/**
 * Checks whether particular user is on mobile or PC based on user_agent returns the appropriate SVG
 */
function getDeviceSVG(string $user_agent = "") : string {
    $exp = "/(android|avantgo|blackberry|bolt|boost|cricket|docomo
        |fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i";

    if(preg_match($exp, $user_agent)) {
        return generatePhone();
    } else {
        return generatePC();
    }
}

/**
 * Generates phone icon SVG
 */
function generatePhone() : string {
    $svg = '<svg class="svg-send-button" xmlns="http://www.w3.org/2000/svg" viewBox="-100 -100 200 200">
                <path d="M0 90 L-30 90 A20 20 0 0 1 -50 70 L-50 -70 A20 20 0 0 1 -30 -90 L0 -90" />
                <path transform="scale(-1)" d="M0 90 L-30 90 A20 20 0 0 1 -50 70 L-50 -70 A20 20 0 0 1 -30 -90 L0 -90" />
                <path d="M-30 -90 A15 15 0 0 0 -15 -75 L15 -75 A15 15 0 0 0 30 -90 M-8 75 L8 75" />
            </svg>';
    
    return $svg;
}

/**
 * Generates PC icon SVG
 */
function generatePC() : string {
    $svg = '<svg class="svg-send-button" xmlns="http://www.w3.org/2000/svg" viewBox="-100 -100 200 200">
                <path d="M0 30 L-70 30 A20 20 0 0 1 -90 10 L-90 -60 A20 20 0 0 1 -70 -80 L0 -80" />
                <path transform="scale(-1, 1)" d="M0 30 L-70 30 A20 20 0 0 1 -90 10 L-90 -60 A20 20 0 0 1 -70 -80 L0 -80" />
                <path d="M-18 80 L-15 30 L15 30 L18 80 M-34 80 L34 80" />
            </svg>';
    
    return $svg;
}

/**
 * Generates bell icon SVG
 */
function generateBell(bool $enabled = true) : string {
    $svg = '<svg viewBox="-100 -100 200 200" xmlns="http://www.w3.org/2000/svg">';
    if($enabled) {
        $svg .= '<path d="M0 -70 A50 50 0 0 0 -50 -20 L-50 20 A100 70 0 0 1 -70 60" />
                 <path d="M0 -70 A50 50 0 0 1 50 -20 L50 20 A100 70 0 0 0 70 60" />
                 <path d="M-70 60 L70 60 M-20 60 A10 10 0 0 0 20 60 M0 -70 L0 -80" />';
    } else {
        $svg .= '<defs>
                    <clipPath id="bell-cut-line">
                    <path d="M74 -86 L-86 74 L-86 -86 Z M86 -74 L-74 86 L86 86 Z" />
                    </clipPath>
                </defs>
                
                <path clip-path="url(#bell-cut-line)" d="M0 -70 A50 50 0 0 0 -50 -20 L-50 20 A100 70 0 0 1 -70 60" />
                <path clip-path="url(#bell-cut-line)" d="M0 -70 A50 50 0 0 1 50 -20 L50 20 A100 70 0 0 0 70 60" />
                <path clip-path="url(#bell-cut-line)" d="M-70 60 L70 60 M-20 60 A10 10 0 0 0 20 60 M0 -70 L0 -80" />
                <path d="M75 -75 L-75 75" />';
    }

    $svg .= "</svg>";

    return $svg;
}