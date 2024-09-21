<?php
session_start();

header("Access-Control-Allow-Origin: *");

include_once $_SERVER["DOCUMENT_ROOT"] . "/include/hash.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/include/encryption.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/include/id.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/include/redis.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/include/transport_encryption.php";

//Check request
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("HTTP/1.1 400 Bad Request");
    die();
}

//Get data
try {
    $tEnc = new TransportEncryption();
    $inputEncrypted = file_get_contents("php://input");
    $inputObj = $tEnc->decryptJSON($inputEncrypted);
} catch (Exception $e) {
    header("HTTP/1.1 204 No Content");
    die();
}

//Validate data
try {
    //Expiry time
    if (!is_int($inputObj->expiry)) {
        validationError("Invalid property: expiry (int)");
    }
    if ($inputObj->expiry < 60) {
        validationError("Invalid property: expiry (int) out of bounds");
    }
    if ($inputObj->expiry > 604800) {
        validationError("Invalid property: expiry (int) out of bounds");
    }

    //Password protection
    if (!is_bool($inputObj->passProtect)) {
        validationError("Invalid property: passProtected (bool)");
    }

    //Validate password
    if (!is_string($inputObj->pass)) {
        validationError("Invalid property: pass (string)");
    }
    if (
        strlen($inputObj->pass < 1 && $inputObj->passProtect) ||
        strlen($inputObj->pass) > 100
    ) {
        validationError("Invalid property: pass (string) invalid length");
    }

    //Validate paste
    if (!is_string($inputObj->pasteText)) {
        validationError("Invalid property: pasteText (string)");
    }
} catch (Exception $e) {
    validationError("Unknown error");
}

//PREPARE DATA
$idGen = new IdGenerator();

$timeout = $inputObj->expiry;
$timeoutUnix = time() + $timeout;
$id = $idGen->GetNew();

$entry = new stdClass();
$entry->id = $id;
$entry->timeoutUnix = $timeoutUnix;
$entry->protected = $inputObj->passProtect;
$entry->password = create_secure_hash($inputObj->pass, $id);

//PASTE ENCRYPTION
$encryption = new Encryption($entry->password);
$entry->text = $encryption->encrypt($inputObj->pasteText);

$entryJSON = json_encode($entry);

//STORE TO DATABASE
try {
    $redisConn = new RedisConn();
    //Store data
    $redisConn->Set($id, $entryJSON, $timeout);
    //Statistics

    $statCount = intval($redisConn->Get("klisterCounter")) + 1;
    $statExpiery =
        doubleval($redisConn->Get("klisterExpieryTotalMinutes")) +
        doubleval($timeout) / 60;
    $redisConn->Set("klisterCounter", $statCount, 0, true);
    $redisConn->Set("klisterExpieryTotalMinutes", $statExpiery, 0, true);
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Error storing Klister";
    die();
}

//FINISH
if (session_status() == PHP_SESSION_ACTIVE) {
    $_SESSION["createdPaste"] = $id;
}
header("HTTP/1.1 201 Created");
header("Content-Type: text/plain; charset=utf-8");
echo $id;
die();

function validationError($reason)
{
    header("HTTP/1.1 400 Bad Request");
    echo $reason;
    die();
}