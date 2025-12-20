<?php

// Time is always formatted as "Y-m-d H:i:s" within Database
// TODO Maybe time should be formatted client side? At least the localized one.
// TODO Dynamically set plural of time ago

/**
 * Formats DateTime string to XX, XX ago
 */
function timeAgo(string $dateString) : string {
    $now = time();
    $date = strtotime($dateString);
    $dateDiff = ($now - $date);

    $ret = "";
    
    $years = floor($dateDiff / (60 * 60 * 24 * 365));
    $months = floor($dateDiff / (60 * 60 * 24 * 30)) % 12; // 12 months a year
    $weeks = floor($dateDiff / ((60 * 60 * 24) * 7)) % 4; // ~4 weeks a month
    $days = floor($dateDiff / (60 * 60 * 24)) % 7; // 7 days a week
    $hours = floor($dateDiff / (60 * 60)) % 24; // 24 hours a day
    $minutes = floor($dateDiff / 60) % 60; // 60 minuts an hour
    $seconds = $dateDiff % 60; // 60 seconds a minute

    $values = [$years, $months, $weeks, $days, $hours, $minutes, $seconds];
    $units = ["year", "month", "week", "day", "hour", "minute", "second"];

    $len = count($units);

    for($i = 0; $i < $len; $i++) {
        if($values[$i] === 0) {
            continue;
        }

        if($values[$i] === 1) {
            $ret .= $values[$i] . $units[$i];
        } else {
            $ret .= $values[$i] . $units[$i] . "s";
        }

        break;
    }

    $ret .= " ago";

    return $ret;
}

/**
 * Formats to HH:MM, D.MMM.YYYY
 */
function dateTimeStamp(string $dateString) : string {
    $dt = new DateTime($dateString, new DateTimeZone('UTC'));

    // TODO Local time still not working
    $loc = (new DateTime)->getTimezone();

    $dt->setTimezone($loc);

    return $dt->format('j. M. Y G:i');
}

/**
 * Formats to D. MMM. YYYY
 */
function dateStamp(string $dateString) : string {
    $dt = new DateTime($dateString, new DateTimeZone('UTC'));
    return $dt->format('j. M. Y');
}