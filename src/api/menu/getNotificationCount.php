<?php
$path = $_SERVER['DOCUMENT_ROOT'];

include $path . '/functions/validateSession.php';
include $path . '/functions/require/notifications.php' ;
include $path . '/functions/errors.php' ;

echo response();

function response() : string {
    if(!validateSession()) {
        return jsonErr("login");
    }

    if(!session_id()) {
        session_start();
    }

    $user_id = $_SESSION["user_id"];

    $res = NewNotifCount($user_id);

    if(!$res[0]) {
        return jsonErr($res[1]);
    }

    return json_encode(
        array(
            "status" => "pass",
            "data" => array(
                "count" => $res[1]
            )
        )
    );
}
