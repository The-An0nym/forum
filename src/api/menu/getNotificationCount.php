<?php
$path = $_SERVER['DOCUMENT_ROOT'];

require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/require/notifications.php' ;
require_once $path . '/functions/errors.php' ;

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
