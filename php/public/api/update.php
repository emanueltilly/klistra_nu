<?php
session_start();

header("Access-Control-Allow-Origin: *");

include_once $_SERVER["DOCUMENT_ROOT"] . "/include/hash.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/include/encryption.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/include/redis.php";

//Check request
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("HTTP/1.1 400 Bad Request");
    die();
}

//Get data
try {
    $inputJSON = file_get_contents("php://input");
    $inputObj = json_decode($inputJSON);
} catch (Exception $e) {
    header("HTTP/1.1 400 Bad Request");
    echo "Invalid JSON payload";
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

    //Password
    if (!is_string($inputObj->pass)) {
        validationError("Invalid property: pass (string)");
    }

    //Validate paste
    if (!is_string($inputObj->pasteText)) {
        validationError("Invalid property: pasteText (string)");
    }

} catch (Exception $e) {
    validationError("Unknown error");
}

$redisConn = new RedisConn();

try {
    $dbObject = json_decode($redisConn->Get($inputObj->id));

    //CHECK PASSWORD
    $passwordHash = create_secure_hash($inputObj->pass, $inputObj->id);

    if ($dbObject->password != $passwordHash) {
        header("HTTP/1.1 401 Unauthorized");
        echo "Invalid password";
        die();
    }

    //ENCRYPT NEW DATA
    $encryption = new Encryption($passwordHash);
    $dbObject->text = $encryption->encrypt($inputObj->pasteText);

    //STORE TO DATABASE
    $redisConn->Set($inputObj->id, json_encode($dbObject), $dbObject->timeoutUnix - time());

} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Error updating Klister";
    die();
}

//FINISH
header("HTTP/1.1 200 OK");
header("Content-Type: application/json");
echo json_encode(array('id' => $inputObj->id));
die();

function validationError($reason)
{
    header("HTTP/1.1 400 Bad Request");
    echo $reason;
    die();
}
