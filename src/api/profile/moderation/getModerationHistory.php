<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/require/moderationHistory.php' ;
require_once $path . '/functions/errors.php' ;

echo response();

function response() {

    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    } 

    if(!validateSession()) {
        return jsonErr("login");
    }

    $page = 0;
    if (isset($_GET["p"])) {
        $page = (int)$_GET["p"];
    }

    $report = false;
    if(isset($_GET["r"])) {
        if($_GET["r"] === "1") {
            $report = true;
        }
    }

    $params = [];
    if(isset($_GET["c"])) {
        $params["culp_handle"] = $_GET["c"];
    }
    if(isset($_GET["s"])) {
        $params["sender_handle"] = $_GET["s"];
    }
    if(isset($_GET["t"])) {
        $params["type"] = (int)$_GET["t"];
    }
    if(isset($_GET["i"])) {
        $params["id"] = $_GET["i"];
    }
    if(isset($_GET["rev"])) {
        $params["reverse"] = $_GET["rev"];
    }

    $conn = getConn();
        
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT u.clearance FROM users u 
            WHERE u.user_id = '$user_id'
            LIMIT 1";

    $result = $conn->query($sql);

    $clearance = $result->fetch_assoc()["clearance"];

    if($clearance < 1) {
        return jsonErr("auth");
    }

    $data = getHistoryHTML($report, $page, $clearance, $params);
    if($report) {
        $amount = countReportHistory(false, $clearance, $params);
        $unread = countReportHistory(true, $clearance, $params);

        return json_encode(
            array(
                "status" => "pass",
                "data" => array(
                    "html" => trim($data),
                    "amount" => $amount,
                    "unread" => $unread
                )
            )
        );
    } else {
        $amount = countModHistory($params);

        return json_encode(
            array(
                "status" => "pass",
                "data" => array(
                    "html" => trim($data),
                    "amount" => $amount,
                )
            )
        );
    }
}