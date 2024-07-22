<?php

namespace AMQF\IoTServer\Monitoring;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;

final class Logger
{
    private static $instance;

    private function __construct() {}

    public static function setup(callable $callback)
    {
        self::$instance = new MonologLogger('app');
        self::$instance = $callback(self::$instance);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new MonologLogger('app');
            self::$instance->pushHandler(new StreamHandler('php://stdout', MonologLogger::DEBUG));
        }

        return self::$instance;
    }

    public static function debug($message, array $context = [])
    {
        self::getInstance()->debug($message, $context);
    }

    public static function info($message, array $context = [])
    {
        self::getInstance()->info($message, $context);
    }

    public static function notice($message, array $context = [])
    {
        self::getInstance()->notice($message, $context);
    }

    public static function warning($message, array $context = [])
    {
        self::getInstance()->warning($message, $context);
    }

    public static function error($message, array $context = [])
    {
        self::getInstance()->error($message, $context);
    }

    public static function critical($message, array $context = [])
    {
        self::getInstance()->critical($message, $context);
    }

    public static function alert($message, array $context = [])
    {
        self::getInstance()->alert($message, $context);
    }

    public static function emergency($message, array $context = [])
    {
        self::getInstance()->emergency($message, $context);
    }
}
