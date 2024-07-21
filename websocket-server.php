<?php

declare(ticks = 1); // Required for handling signals

use AMQF\InstantMQTTRelay\WebSocket\WebSocketServer;

function exec_web_socket_in_background(int $websocketPort) : void
{
    $webSocketServer = new WebSocketServer();
    try {
        echo "Starting WebSocket server on port $websocketPort...\n";
        $webSocketServer->run($websocketPort);
        echo "WebSocket server running on port $websocketPort\n";
    } catch (\Exception $e) {
        echo "Failed to start WebSocket server: " . $e->getMessage() . "\n";
        exit(1);
    }
}

function start_web_socket_server(string $serverName, int $websocketPort) : void
{
    $pid = pcntl_fork();

    if ($pid == -1) {
        // Fork failed
        die('Could not fork');
    } elseif ($pid) {
        // Parent process
        echo "Server $serverName started with PID $pid\n";
    } else {
        // Child process
        exec_web_socket_in_background($websocketPort);
        exit(0); // Ensure the child process exits after the server starts
    }
}