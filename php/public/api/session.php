<?php
session_start();

header("Access-Control-Allow-Origin: *");

if (isset($_SESSION["createdPaste"])) {
    if (strlen($_SESSION["createdPaste"]) > 4) {
        header("HTTP/1.1 200 OK");
        header("Content-Type: text/html; charset=utf-8");
        echo $_SESSION["createdPaste"];
        die();
    } else {
        header("HTTP/1.1 204 No Content");
        die();
    }
} else {
    header("HTTP/1.1 428 Precondition Required");
    die();
}

?>
