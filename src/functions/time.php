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

    if($years > 0) {
        if($years > 3 || $months === 0) {
            $ret .= $years . " years";
        } else {
            $ret .= $years . " year(s) and " . $months . " month(s)";
        }
    } else if($months > 0) {
        if($months > 3 || $weeks === 0) {
            $ret .= $months . " months";
        } else {
            $ret .= $months . " month(s) and " . $weeks . " week(s)";
        }
    } else if($weeks > 0) {
        if($days === 0) {
            $ret .= $weeks . " weeks";
        } else {
            $ret .= $weeks . " week(s) and " . $days . " day(s)";
        }
    } else if($days > 0) {
        if($days > 3 || $hours === 0) {
            $ret .= $days . " days";
        } else {
            $ret .= $days . " day(s) and " . $hours . " hour(s)";
        }
    } else if($hours > 0) {
        if($hours > 5 || $minutes === 0) {
            $ret .= $hours . " hours";
        } else {
            $ret .= $hours . " hour(s) and " . $minutes . " minute(s)";
        }
    } else if($minutes > 0) {
        if($minutes > 5 || $seconds === 0) {
            $ret .= $minutes . " minutes";
        } else {
            $ret .= $minutes . " minute(s) and " . $seconds . " second(s)";
        }
    } else {
        $ret .= $seconds . " second(s)";
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