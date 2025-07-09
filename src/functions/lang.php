<?php

if(!function_exists('getLangArr')) {

function getLangArr(string $lang = "en") : array {
    $langArr = [
        "en" => [
            "post" => "post",
            "home" => "home",
            "togMode" => "toggle mode",
            "save" => "save",
            "cancel" => "cancel",
            "login" => "login",
            "logout" => "logout",
            "signUp" => "signup",
            "username" => "username",
            "handle" => "handle",
            "password" => "password,",
            "pfp" => "profile picture",
            "posts" => "posts",
            "threads" => "threads",
            "delAccount" => "delete account",
            /* And so on... */
            ]
    ];

    if(isset($langArr[$lang])) {
        return $langArr[$lang];
    } else {
        return $langArr["en"];
    }
}

function getLang(string $id) : string {
    if(!isset($id)) {
        return "";
    }

    if(!session_id()) {
        session_start();
    }

    if(isset($_SESSION['lang'])) {
        $lang = $_SESSION['lang'];
    } else {
        $lang = "en";
    }

    $arr = getLangArr($lang);

    if(isset($arr[$id])) {
        $txt = $arr[$id];
    } else {
        return "";
    }

    return $txt;
}

}