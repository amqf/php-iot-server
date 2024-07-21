<?php

namespace AMQF\InstantMQTTRelay\WebSocket;

use DomainException;
use Exception;
use WebSocket\Client as WebSocketClient;

class WebSocketService
{
    private WebSocketClient $client;

    public function __construct(string $host)
    {
        try{
            $this->client = new WebSocketClient($host);
        }catch(Exception $e)
        {
            throw new DomainException("Unreachable WebSocket Server $host");
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