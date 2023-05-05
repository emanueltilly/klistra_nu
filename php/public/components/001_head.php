<?php
session_start(); ?>
<?php function getRootUrl()
{
    $protocol =
        isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on"
            ? "https"
            : "http";
    $host = $_SERVER["HTTP_HOST"];
    return $protocol . "://" . $host . "/";
} ?>

<div class="head_container_master" id="head_container_master">
    <a href="<?php echo getRootUrl(); ?>">
        <h1>Klistra.nu</h1>
        <h3>Copy & Paste - without a trace</h3>
    </a>
</div>