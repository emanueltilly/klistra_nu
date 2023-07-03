<?php
//PHP SESSION
session_start();
if (!isset($_SESSION["createdPaste"])) {
    $_SESSION["createdPaste"] = "";


}

//INCLUDES
include_once "./include/guid.php";

//HTML Main Page
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <meta name="description" content="Klistra.nu is a secure and encrypted online platform that allows you to share password protected text with peace of mind. Keep your sensitive information safe and secure with Klistra.">
    <meta name="keywords" content="Klistra.nu, secure, encrypted, online platform, share text pastes, passwords, automatic expiry, sensitive information, safe, secure">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Klistra.nu</title>
    <link rel="icon" type="image/x-icon" href="/static/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@40,300,0,200" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="static/script.js"></script>
    <?php
    
    if (isset($_COOKIE["Lightmode"])) {
        $cookieValue = $_COOKIE["Lightmode"];
    
        if ($cookieValue == "1") {
            // Cookie value matches the expected value
            echo '<link rel="stylesheet" type="text/css" href="singe.css">';
        } else {
            // Cookie value is different
            echo '<link rel="stylesheet" type="text/css" href="syle.css">';
        }
    } else {
        // Cookie is not set
        echo '<link rel="stylesheet" type="text/css" href="syle.css">';
    }
    ?>
    
    <!--this is for lightmode by Johannes Tilly and chatgpt honestly-->
    <script>
        function setCookieAndReload() {
            // Set the cookie using JavaScript
            dvar expirationDate = new Date();
            expirationDate.setDate(expirationDate.getDate() + 399);

            var cookieValue = document.cookie;

            if (cookieValue.includes("Lightmode=1")) {
            // Cookie with the expected value exists
                var cookieValue = "0"; 
            } else {
            // Cookie does not exist or has a different value
                var cookieValue = "1";
            }

            var cookieName = "Lightmode";
            var cookieExpiration = "expires=" + expirationDate.toUTCString();

            document.cookie = cookieName + "=" + cookieValue + "; " + cookieExpiration + "; path=/";

            // Reload the page
            window.location.reload();
        }
    </script>
   
    </script>
  </head>
  <body>
  
    <div class="pageWrapper">
    <div class="master_container">
	    <div id="001_head_container"></div>
        <?php if (isset($_GET["klister"])) {
            $_SESSION["createdPaste"] = $_GET["klister"]; ?>
                <div id="003_read_container"></div>
            <?php
        } elseif (isset($_GET["page"])) {
            //PAGES
            switch ($_GET["page"]) {
                case "privacy":
                    echo '<div id="005_privacy_container"></div>';
                    break;
                case "api":
                    echo '<div id="006_api_container"></div>';
                    break;
                case "stats":
                    echo '<div id="007_stats_container"></div>';
                    break;
            }
        } else {
             ?>
                <div id="002_create_container"></div>
            <?php
        } ?>
        <div id="004_footer_container"></div>
    </div>
    </div>
  </body>
  
  
</html>