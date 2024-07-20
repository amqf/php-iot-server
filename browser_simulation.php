<?php
require 'vendor/autoload.php';

use WebSocket\Client as WebSocketClient;

$webSocketServer = 'ws://127.0.0.1:8081';

try {
    $client = new WebSocketClient($webSocketServer);
    echo "Conectado ao servidor WebSocket em $webSocketServer\n";

    while (true) {
        // Lê mensagens do WebSocket
        $message = $client->receive();
        echo "Mensagem recebida: $message\n";

        // Aqui você pode processar a mensagem, por exemplo, salvar em um banco de dados
        // ou realizar outras ações conforme necessário.
    }
} catch (Exception $e) {
    echo "Erro: {$e->getMessage()}\n";
}
