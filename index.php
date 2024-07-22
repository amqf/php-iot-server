<?php

use AMQF\IoTServer\App;
use AMQF\IoTServer\Config\IoTWSConfig;
use AMQF\IoTServer\Config\ConfigurationException;
use AMQF\IoTServer\MQTTClient;
use AMQF\IoTServer\WebSocketServer;
use React\EventLoop\Loop;
use Swoole\Coroutine;
// use Swoole\Coroutine\Server;
use Swoole\WebSocket\Server;

require_once './vendor/autoload.php';

// new WebSocketServer();
define('CONFIG_PATH', './config.iotws');


// // Configurações do servidor WebSocket
// $server = new Server("127.0.0.1", 9501);

// // var_dump($server);die();

// // Evento: quando um cliente se conecta
// $server->on('open', function ($server, $request) {
//     echo "Connection opened: {$request->fd}\n";
// });

// // Evento: quando o servidor recebe uma mensagem do cliente
// $server->on('message', function ($server, $frame) {
//     echo "Message from client {$frame->fd}: {$frame->data}\n";
//     // Enviar uma resposta para o cliente
//     $server->push($frame->fd, "Server received: {$frame->data}");
// });

// // Evento: quando um cliente se desconecta
// $server->on('close', function ($server, $fd) {
//     echo "Connection closed: {$fd}\n";
// });

// // Iniciar o servidor
// $server->start();

try
{
    // Exemplo de uso
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

