<?php

use AMQF\IoTServer\App;
use AMQF\IoTServer\Config\IoTWSConfig;
use AMQF\IoTServer\Config\ConfigurationException;
use AMQF\IoTServer\MQTTClient;
use AMQF\IoTServer\WebSocketServer;
use React\EventLoop\Loop;
use Swoole\Coroutine;
// use Swoole\Coroutine\Server;
// use Swoole\WebSocket\Server;

require_once './vendor/autoload.php';

// new WebSocketServer();
define('CONFIG_PATH', './config.iotws');

use Swoole\Http\Server;

$server = new Server("127.0.0.1", 9501);

$server->on("request", function ($request, $response) {
    $response->header("Content-Type", "text/plain");
    $response->end("Hello, Swoole!");
});

$server->start();

try
{
    // Web Socket Server
    $webSocketServer = new WebSocketServer('127.0.0.1', 9501);
    $webSocketServer->onOpen(
        function ($fd)
        {
            echo $fd;
        }
    );
    $webSocketServer->onMessage(
        function ($client, $data)
        {
            echo $data;
        }
    );
    $webSocketServer->onClose(
        function ($fd)
        {
            echo $fd;
        }
    );
    $webSocketServer->start();

    // /** @var IoTWSConfig */
    // $config = new IoTWSConfig(CONFIG_PATH);

    // /** @var MQTTClient */
    // $mqttClient = new MQTTClient($config->getMQTTClientSettings());

    // $mqttClient->connect();
    
    // $mqttClient->subscribe(function(string $topic, string $message)
    // {
    //     echo "$topic => $message";
    // });

    // $mqttClient->run();

    // $main = new App($config);
    // $main->run();
}catch(ConfigurationException $exception)
{
    echo 'Check configuration ' . CONFIG_PATH . PHP_EOL;
    echo 'Syntax error: ' . $exception->getMessage() . PHP_EOL;
    exit();
}

