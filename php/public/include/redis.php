<?php
class RedisConn
{
    private $redis;
    private $redisAuth = "";
    private $redisIp = "";
    private $redisPort = 6379;
    private $connected = false;

    private function Connect()
    {
        if (!$this->connected) {
            try {
                $this->redisAuth = getenv("REDIS_REQUIREPASS");
                $this->redisIp = getenv("REDIS_IP");
                $this->redis = new Redis();
                $this->redis->connect($this->redisIp, $this->redisPort);
                $this->redis->auth($this->redisAuth);
            } catch (Exception $e) {
                $this->connected = false;
                return false;
            }
        }
        $this->connected = true;
        return true;
    }

    private function Disconnect()
    {
        if ($this->connected) {
            try {
                $this->redis->close();
            } catch (Exception $e) {
                return false;
            }
        }
        $this->connected = false;
        return true;
    }

    public function Set($key, $value, $expiration, $noExpiry = false)
    {
        if (!$this->Connect()) {
            return false;
        }
        try {
            $this->redis->set($key, $value);
            if ($noExpiry == false) {
                $this->redis->expire($key, $expiration);
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function Get($key)
    {
        if (!$this->Connect()) {
            return false;
        }
        try {
            return $this->redis->get($key);
        } catch (Exception $e) {
            return false;
        }
    }
}
