<?php

namespace AMQF\InstantMQTTRelay;

require 'vendor/autoload.php';

use AMQF\InstantMQTTRelay\WebSocket\WebSocketService;
use PhpMqtt\Client\MqttClient;

class MQTTService
{
    private MqttClient $client;
    private WebSocketService $webSocketService;

    public function __construct(
        string $address,
        int $port,
        string $clientId,
        WebSocketService $webSocketService
    )
    {
        $this->client = new MqttClient($address, $port, $clientId);
        $this->webSocketService = $webSocketService;
    }

    public function connect(): void
    {
        $this->client->connect();
        echo "Conectado ao servidor MQTT em {$this->client->getHost()}:{$this->client->getPort()}\n";
    }

    public function subscribe(string $topic): void
    {
        $this->client->subscribe($topic, function (string $topic, string $message) {
            echo "Recebido no tópico [$topic]: $message\n";
            $this->webSocketService->send($topic, $message);
        });
    }

    public function loop(): void
    {
        echo "Aguardando mensagens MQTT...\n";
        $this->client->loop(true);
    }
}