<?php

// Time is always formatted as "Y-m-d H:i:s" within Database

/**
 * Formats DateTime string to XX, XX ago
 */
function timeAgo(string $dateString) : string {
    $now = time();
    $date = strtotime($dateString);
    $dateDiff = ($now - $your_date);

    $ret = "";
    
    $years = ceil($dateDiff / (60 * 60 * 24 * 365));
    $months = ceil($dateDiff / (60 * 60 * 24 * 30)) % 12; // 12 months a year
    $weeks = ceil($dateDiff / ((60 * 60 * 24) * 7)) % 4; // ~4 weeks a month
    $days = ceil($dateDiff / (60 * 60 * 24)) % 7; // 7 days a week
    $hours = ceil($dateDiff / (60 * 60)) % 24; // 24 hours a day
    $minutes = ceil($dateDiff / 60) % 60; // 60 minuts an hour
    $seconds = $dateDiff % 60; // 60 seconds a minute

    if($years >= 0) {
        if($years > 3 || $months === 0) {
            $ret .= $years . " years";
        } else {
            $ret .= $years . " years and " + $months + " months";
        }
    } else if($months >= 0) {
        if($months > 3 || $weeks === 0) {
            $ret .= $months . " months";
        } else {
            $ret .= $months . " months and " + $weeks + " weeks";
        }
    } else if($weeks >= 0) {
        if($days === 0) {
            $ret .= $weeks . " weeks";
        } else {
            $ret .= $weeks . " weeks and " + $days + " days";
        }
    } else if($days >= 0) {
        if($days > 3 || $hours === 0) {
            $ret .= $days . " days";
        } else {
            $ret .= $days . " days and " + $hours + " hours";
        }
    } else if($hours >= 0) {
        if($hours > 5 || $minutes === 0) {
            $ret .= $hours . " hours";
        } else {
            $ret .= $hours . " hours and " + $minutes + " minutes";
        }
    } else if($minutes >= 0) {
        if($minutes > 5 || $seconds === 0) {
            $ret .= $minutes . " minutes";
        } else {
            $ret .= $minutes . " minutes and " + $seconds + " seconds";
        }
    } else {
        $ret .= $seconds . " seconds";
    }

    $ret .= " ago";

    return $ret;
}

/**
 * Formats to HH:MM, D.MMM.YYYY
 */
function dateTimeStamp(string $dateString) : string {
    $now = time();
    $date = strtotime($dateString);

    return Date("G:i, j. M. Y");
}