<?php


namespace App\Library;

class Helpers
{
    public static function setOrGetRedis($keyName, $keyValue = null, $expiredTime = null, $type)
    {
        $redisClient = new \Predis\Client();
        if ($type === 'get') {
            $res =  $redisClient->get($keyName);
            return $res;
        } else {
            $redisClient->setex($keyName, $expiredTime, $keyValue);
            return $redisClient->get($keyName);
        }
    }
}
