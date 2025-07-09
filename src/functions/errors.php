<?php

if(!function_exists('getErrorArr')) {

function getErrorArr(string $lang = "en") : array {
    $errorLangArr = [
        "en" => [
            "generic" => "An error has occured. Please try again later", 
            "args" => "Missing or invalid argument(s)", 
            "login" => "Please login to continue",
            "auth" => "You are not authorized to do this",
            "acc" => "This account does not exist. Try logging in instead?",
            "logPswd" => "Incorrect combination of handle and password",
            "logFail" => "Login failed. Please try again later",
            "sigFail" => "Sign-up failed. Please try again later",
            "pswdFail" => "Incorrect password",
            "tUser" => "Username is already taken",
            "tHand" => "Handle is already taken",
            "tImg" => "This image already exists",
            "imgType" => "Image must be a valid jpg or png image",
            "imgMaxMB" => "Image must be less than 1MB",
            "imgMin" => "Image size must be bigger or equal to than 128 x 128px",
            "imgMax" => "Image size must be below or equal to 1024 x 1024px",
            "pswdMin" => "Password needs to be at least 8 characters long",
            "pswdMax" => "Password needs to be less than or equal to 64 characters long",
            "userMin" => "Username needs to be at least 4 characters long",
            "userMax" => "Username needs to be less than or equal to 24 characters long",
            "handMin" => "Handle needs to be at least 4 characters long",
            "handReg" => "Only characters a-Z 0-9 - _ . are allowed for the handle",
            "handMax" => "Handle needs to be less than or equal to 16 characters long",
            "msgMinMax" => "Message needs to be between 20 and 200 chars (inclusive)",
            "contMin" => "No content",
            "contMax" => "Content needs to be less than or equal to 2000 characters long",
            "thrdMin" => "Thread title needs to be at least 8 characters long",
            "thrdMax" => "Thread title needs to be less than or equal to 64 characters long",
            "emptyCat" => "This category is empty", 
            "emptyThrd" => "This thread is empty", 
            "404user" => "This account does not exist",
            "404thrd" => "This thread does not exist",
            "404cat" => "This category does not exist",
            "404mod" => "This moderation row does not exist",
            "undoRepo" => "Cannot undo report",
            "undoOwn" => "Cannot undo as culprit",
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

    $arr = getErrorArr($lang);

    if(isset($arr[$id])) {
        $err = $arr[$id];
    } else {
        $err = $arr["generic"];
    }

    return $err;
}

function jsonErr(string $id = "generic", string $addMsg = "") : string {
    if($id === "") {
        $id = "generic";
    }
    
    $errMsg = getError($id);
    if($addMsg !== "") {
        $errMsg .= " " . $addMsg;
    }
    return json_encode(array(
        "status" => "fail", 
        "msg" => $errMsg
    ));
}

function pass() : string {
    return json_encode(array("status" => "pass"));
}

}