<?php

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Messaging\MessageInterface;

require 'vendor/autoload.php';


// Configurações do MQTT
$server = 'broker.hivemq.com'; // Endereço do broker MQTT
$port = 1883; // Porta do broker MQTT
$clientId = 'phpMQTT-publisher'; // ID do cliente MQTT

// Função para publicar mensagem via MQTT
function publishMQTT($topic, $message) {
    global $server, $port, $clientId;

    $mqtt = new MqttClient($server, $port, $clientId);
    $mqtt->connect();
    $mqtt->publish($topic, $message, 0);
    $mqtt->disconnect();

    echo "Mensagem publicada no tópico $topic: $message\n";
}

// Cria um socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

if ($socket === false) {
    echo "Não foi possível criar o socket: " . socket_strerror(socket_last_error()) . "\n";
    exit;
}

// Liga o socket ao endereço e porta
$address = '127.0.0.1';
$port = 12345;
if (socket_bind($socket, $address, $port) === false) {
    echo "Não foi possível ligar o socket: " . socket_strerror(socket_last_error($socket)) . "\n";
    exit;
}

// Coloca o socket em modo de escuta
if (socket_listen($socket, 5) === false) {
    echo "Não foi possível colocar o socket em modo de escuta: " . socket_strerror(socket_last_error($socket)) . "\n";
    exit;
}

echo "Servidor de socket iniciado em $address:$port...\n";

while (true) {
    // Aceita uma nova conexão
    $client = socket_accept($socket);

    if ($client === false) {
        echo "Não foi possível aceitar a conexão: " . socket_strerror(socket_last_error($socket)) . "\n";
        continue;
    }

    // Lê a mensagem do cliente
    $input = socket_read($client, 1024);
    echo "Mensagem recebida: $input\n";

    // Publica a mensagem via MQTT
    $topic = 'test/topic';
    publishMQTT($topic, $input);

    // Envia uma resposta ao cliente
    $response = "Mensagem recebida e publicada no MQTT: $input";
    socket_write($client, $response, strlen($response));

    // Fecha a conexão com o cliente
    socket_close($client);
}

// Fecha o socket principal
socket_close($socket);
