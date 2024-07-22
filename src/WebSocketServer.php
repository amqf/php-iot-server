<?php

declare(strict_types=1);

namespace AMQF\IoTServer;

use Swoole\WebSocket\Server as SwooleWebSocketServer;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;

class WebSocketServer
{
    private SwooleWebSocketServer $_server;
    private string $host;
    private int $port;

    /**
     * Construtor da classe WebSocketServer.
     *
     * @param string $host Endereço IP ou hostname para o servidor.
     * @param int $port Porta para o servidor WebSocket.
     */
    public function __construct(string $host = '127.0.0.1', int $port = 9501)
    {
        $this->host = $host;
        $this->port = $port;
        $this->_server = new SwooleWebSocketServer($this->host, $this->port);
    }

    public function onOpen(callable $callback): void
    {
        // Evento: quando um cliente se conecta
        $this->_server->on(
            'open',
            function (SwooleWebSocketServer $server, Request $request) use ($callback)
            {
                echo "Connection opened: {$request->fd}\n";
                $callback($request->fd);
            }
        );
    }

    public function onMessage(callable $callback): void
    {
        // Evento: quando o servidor recebe uma mensagem do cliente
        $this->_server->on(
            'message',
            function (SwooleWebSocketServer $server, Frame $frame) use ($callback)
            {
                echo "Message from client {$frame->fd}: {$frame->data}\n";
                $callback($frame->id, $frame->data);

                // Enviar uma resposta para o cliente
                // $server->push($frame->fd, "Server received: {$frame->data}");
            }
        );
    }

    public function onClose(callable $callback): void
    {
        // Evento: quando um cliente se desconecta
        $this->_server->on(
            'close',
            function (SwooleWebSocketServer $server, int $fd) use ($callback)
            {
                echo "Connection closed: {$fd}\n";
                $callback($fd);
            }
        );
    }

    /**
     * Inicia o servidor WebSocket.
     */
    public function start(): void
    {
        $this->_server->start();
    }
}
