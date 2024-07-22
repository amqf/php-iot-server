<?php

use AMQF\IoTServer\App;
use AMQF\IoTServer\Config\IoTWSConfig;
use AMQF\IoTServer\Config\ConfigurationException;
use AMQF\IoTServer\MQTTClient;
use AMQF\IoTServer\WebSocketServer;
use Swoole\Coroutine;
use Swoole\Coroutine\Server;

require_once './vendor/autoload.php';

// new WebSocketServer();
define('CONFIG_PATH', './config.iotws');

try{
    /** @var IoTWSConfig */
    $config = new IoTWSConfig(CONFIG_PATH);
    
    /** @var MQTTClient */
    $mqttClient = new MQTTClient($config->getMQTTClientSettings());

    $mqttClient->connect();
    
    $mqttClient->subscribe(function(string $topic, string $message)
    {
        echo "$topic => $message";
    });

    $mqttClient->run();
}catch(ConfigurationException $exception)
{
    echo 'Check configuration ' . CONFIG_PATH . PHP_EOL;
    echo 'Syntax error: ' . $exception->getMessage() . PHP_EOL;
    exit();
}

// $main = new App();
// $main->run();