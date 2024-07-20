<?php

use AMQF\InstantMQTTRelay\MQTTService;
use AMQF\InstantMQTTRelay\WebSocket\WebSocketService;
use Dotenv\Dotenv;

require './vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('HOST', $_ENV['MQTT_HOST']);
define('PORT', $_ENV['MQTT_PORT']);

// Define the expected options and their short options
$options = getopt('t:');

// Check if the required option -t is set
if (!isset($options['t'])) {
    die("Error: The -t option (topics filepath) is required.\n");
}


// $address = 'instant-mqtt-relay-broker';
// $port = 1884;

// $connection = @fsockopen($address, $port, $errno, $errstr, 5);

// if ($connection) {
//     echo "OK: Conexão estabelecida com o broker MQTT em $address:$port\n";
//     fclose($connection);
// } else {
//     echo "NÃO-OK: Não foi possível conectar ao broker MQTT em $address:$port\n";
//     echo "Erro: $errno - $errstr\n";
// }

// Função para carregar os tópicos do arquivo
function loadTopicsFromFile(string $filename) {
    if (!file_exists($filename)) {
        throw new Exception("Arquivo de tópicos não encontrado: " . $filename);
    }

    $topics = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return $topics;
}

// var_dump(HOST);die();
try{
    echo sprintf('Redirecting MQTT data from %s MQTT Server to % Web Socket Server');
    $webSocketService = new WebSocketService(HOST);
    $webSocketService = new WebSocketService(HOST);
    $mqttService = new MQTTService(HOST, PORT, 'mqtt_client', $webSocketService);

    $mqttService->connect();

    foreach(loadTopicsFromFile('./topics.txt') as $topic)
    {
        $mqttService->subscribe($topic);
    }

    $mqttService->loop();
}catch(DomainException $exception)
{
    echo sprintf("DomainException: %s", $exception->getMessage());
    exit($exception->getCode());
}