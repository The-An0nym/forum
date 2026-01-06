<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/errors.php' ;
require_once $path . '/functions/sessionUpdates.php' ;

echo response();

function response() : string {
    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    }

    $err = jsonEncodeErrors(clearCurrSession());
    if($err !== "") {
        return $err;
    }
    
    return pass();
}