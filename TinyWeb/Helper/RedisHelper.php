<?php

namespace TinyWeb\Helper;

use Exception;
use Redis;
use TinyWeb\Application;

class RedisHelper
{

    private static $instance = null;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            try {
                self::$instance = new Redis();
                $host = Application::instance()->getEnv('ENV_REDIS_HOST', 'localhost');
                $port = Application::instance()->getEnv('ENV_REDIS_PORT', 6379);
                $pass = Application::instance()->getEnv('ENV_REDIS_PASS', '');
                if (!self::$instance->connect($host, $port)) {
                    throw new Exception('connect Redis server failed!');
                }
                if (!empty($pass)) {
                    self::$instance->auth($pass);
                }
            } catch (Exception $e) {
                return false;
            }
        }
        return self::$instance;
    }

    private function __construct()
    {
    }
}