<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/require/notifications.php' ;
require_once $path . '/functions/errors.php' ;

echo response();

function response() : string {
    if(!validateSession()) {
        return jsonErr("login");
    }

    $page = 0;
    if(isset($_POST["p"])) {
        $page = (int)$_POST["p"];
    }

    $html = generateNotifsHTML($page);

    return json_encode(
        array(
            "status" => "pass",
            "data" => $html;
        )
    )
}