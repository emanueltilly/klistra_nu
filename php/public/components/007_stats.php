<?php
session_start();
include_once $_SERVER["DOCUMENT_ROOT"] . "/include/redis.php";
$redisConn = new RedisConn();
?>
<div class="docs_container_master">
<h2>Statistics</h2>
<p>
    <?php echo $redisConn->Get("klisterCounter"); ?> unique klisters.
</p>
<p>
    <?php echo doubleval($redisConn->Get("klisterExpieryTotalMinutes")) /
        $redisConn->Get("klisterCounter"); ?> min average expiery time.
</p>
<br><br>
</div>