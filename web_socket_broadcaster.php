<?php

use AMQF\InstantMQTTRelay\WebSocket\WebSocketServer;
use Dotenv\Dotenv;

require 'vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('WEB_SOCKET_PORT', $_ENV['WEB_SOCKET_PORT']);

echo "Servidor WebSocket iniciado em ws://127.0.0.1:8081\n";

$webSocketServer = new WebSocketServer();
$webSocketServer->run(WEB_SOCKET_PORT);