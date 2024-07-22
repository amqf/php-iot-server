<?php

use AMQF\IoTServer\App;
use AMQF\IoTServer\Config\IoTWSConfig;
use AMQF\IoTServer\Config\ConfigurationException;
use AMQF\IoTServer\Monitoring\Logger;
use AMQF\IoTServer\MQTTClient;
use AMQF\IoTServer\WebSocketServer;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use React\EventLoop\Loop;
use Swoole\Coroutine;
// use Swoole\Coroutine\Server;
// use Swoole\WebSocket\Server;

require_once './vendor/autoload.php';

// new WebSocketServer();
define('CONFIG_PATH', './config.iotws');

use Swoole\Http\Server;


// Logger::setup(function(MonologLogger $instance){
//     $instance->pushHandler(new StreamHandler(__DIR__ . '/logs.log', MonologLogger::DEBUG));
//     return $instance;
// });

// $server = new Server("127.0.0.1", 9501);

// $server->on("request", function ($request, $response) {
//     $response->header("Content-Type", "text/plain");
//     Logger::info('sensacional');
//     $response->end("Hello, Swoole!");
// });

// $server->start();

try
{
    // Web Socket Server
    $webSocketServer = new WebSocketServer('127.0.0.1', 9501);
    $webSocketServer->onOpen(
        function ($fd)
        {
            Logger::debug($fd);
        }
    );
    $webSocketServer->onMessage(
        function ($client, $data)
        {
            Logger::debug($data);
        }
    );
    $webSocketServer->onClose(
        function ($fd)
        {
            Logger::debug($fd);
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
    Logger::debug(
        sprintf(
            '%s. %s',
            'Check configuration ' . CONFIG_PATH,
            'Syntax error: ' . $exception->getMessage() . PHP_EOL
        )
    );
    exit();
}