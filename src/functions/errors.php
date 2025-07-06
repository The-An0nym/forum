<?php

function getArr(string $lang = "en") : array {
    $errorLangArr = [
        "en" => [
            "generic" => "An error has occured. Please try again later", 
            "args" => "Missing or invalid argument(s)", 
            "login" => "Please login to continue",
            "acc" => "This account does not exist. Try logging in instead?",
            "logPswd" => "Incorrect combination of handle and password",
            "logFail" => "Login failed. Please try again later",
            "sigFail" => "Sign-up failed. Please try again later",
            "tUser" => "Username is already taken",
            "tHand" => "Handle is already taken",
            "pswdMin" => "Password needs to be at least 8 characters long",
            "pswdMax" => "Password needs to be less than or equal to 64 characters long",
            "userMin" => "Username needs to be at least 4 characters long",
            "userMax" => "Username needs to be less than or equal to 24 characters long",
            "handMin" => "Handle needs to be at least 4 characters long",
            "handReg" => "Only characters a-Z 0-9 - _ . are allowed for the handle"
            "handMax" => "Handle needs to be less than or equal to 16 characters long"
            ]
    ];

    if(isset($errorLangArr[$lang])) {
        return $errorLangArr[$lang];
    } else {
        return $errorLangArr["en"];
    }
}

function getError(string $id = "generic") : string {
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
        $err = $arr[$id];
    } else {
        $err = $arr["generic"];
    }

    return $err;
}