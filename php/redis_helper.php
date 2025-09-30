<?php
// php/redis_helper.php
// requires phpredis extension
$REDIS_HOST = '127.0.0.1';
$REDIS_PORT = 6379;
$REDIS_DB = 0;
$REDIS_TTL = 60 * 60 * 24 * 7; // 7 days token TTL

$redis = new Redis();
try {
    $redis->connect($REDIS_HOST, $REDIS_PORT);
    if (isset($REDIS_DB)) $redis->select($REDIS_DB);
} catch (Exception $e) {
    // If Redis is not available, we won't abort; but session features will fail
    $redis = null;
}
