<?php
session_start();

include_once $_SERVER["DOCUMENT_ROOT"] . "/include/hash.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/include/encryption.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/include/redis.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("HTTP/1.1 400 Bad Request");
    die();
}

//Get data
try {
    $inputJSON = file_get_contents("php://input");
    $inputObj = json_decode($inputJSON);
} catch (Exception $e) {
    header("HTTP/1.1 204 No Content");
    die();
}

//Validate data
try {
    //Validate id
    if (!is_string($inputObj->id)) {
        validationError("Invalid property: id (string)");
    }
    if (strlen($inputObj->id) < 4) {
        validationError("Invalid property: id (string) too short");
    }
} catch (Exception $e) {
    validationError("Unknown error");
}

$redisConn = new RedisConn();
$response = new stdClass();

try {
    $dbObject = json_decode($redisConn->Get($inputObj->id));
    if ($dbObject == null) {
        header("HTTP/1.1 404 Not Found");
        die();
    }
    $response->passwordProtected = $dbObject->passwordProtected;
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Error storing Klister";
    die();
}

//PRESENT DATA
header("HTTP/1.1 200 OK");
header("Content-Type: application/json; charset=utf-8");
echo json_encode($response);
die();

function validationError($reason)
{
    header("HTTP/1.1 400 Bad Request");
    echo $reason;
    die();
}
