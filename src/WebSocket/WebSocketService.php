<?php

namespace AMQF\InstantMQTTRelay\WebSocket;

use DomainException;
use Exception;
use WebSocket\Client as WebSocketClient;

class WebSocketService
{
    private WebSocketClient $client;

    public function __construct(string $serverUrl)
    {
        try{
            $this->client = new WebSocketClient($serverUrl);
        }catch(Exception $e)
        {
            throw new DomainException("Unreachable Web Socket Server $serverUrl");
        }
    }

    public function send(string $topic, string $message): void
    {
        $this->client->send(json_encode([
            'key' => $topic,
            'value' => $message
        ]));
    }
}