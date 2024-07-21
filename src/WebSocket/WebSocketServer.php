<?php

namespace AMQF\InstantMQTTRelay\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use SplObjectStorage;

class WebSocketServer implements MessageComponentInterface {
    protected SplObjectStorage $_clients;

    public function __construct()
    {
        $this->_clients = new SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Armazena a conexão do cliente
        $this->_clients->attach($conn);
        echo "Novo cliente conectado.\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Envia a mensagem recebida para todos os clientes conectados

        /** @var \Ratchet\WebSocket\WsConnection $client */
        foreach ($this->_clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }

        echo "Mensagem recebida: $msg\n";
    }

    public function onClose(ConnectionInterface $conn) {
        // Remove a conexão do cliente
        $this->_clients->detach($conn);
        echo "Cliente desconectado.\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Erro: {$e->getMessage()}\n";
        $conn->close();
    }

    function run(string $port) : void
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new WebSocketServer()
                )
            ),
            $port
        );
        
        $server->run();
    }
}
