<?php

function getArr(string $lang = "en") : array {
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
            "password" => "password,"
            "pfp" => "profile picture",
            "posts" => "posts",
            "threads" => "threads",
            "delAccount" => "delete account",
            /* And so on... */
            ]
    ];

    if(isset($errorLangArr[$lang])) {
        return $errorLangArr[$lang];
    } else {
        return $errorLangArr["en"];
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

    $arr = getArr($lang);

    if(isset($arr[$id])) {
        $txt = $arr[$id];
    } else {
        return "";
    }

    return $txt;
}