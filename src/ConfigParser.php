<?php

namespace AMQF\InstantMQTTRelay;

use InvalidArgumentException;

class ConfigParser
{
    private $config = [
        'name' => null,
        'topics' => [],
        'mqtt_broker_server_host' => null,
        'mqtt_broker_server_port' => null,
        'websocket_server_connection_string' => null,
        'websocket_server_host' => null,
        'websocket_server_port' => null,
    ];

    public function __construct($configFilePath)
    {
        if (!file_exists($configFilePath)) {
            throw new InvalidArgumentException('Configuration file path is required');
        }

        $this->parseConfig($configFilePath);
        $this->validateConfig();
    }

    private function parseConfig($configFilePath)
    {
        $lines = file($configFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);
    
            if (preg_match('/^IOT MQTT ROUTER (\w+)$/', $line, $matches)) {
                $this->config['name'] = trim($matches[1]);

            } elseif (preg_match('/^SHOULD SUBSCRIBE MQTT TOPICS (.+)$/', $line, $matches)) {
                $this->config['topics'] = array_map('trim', explode(',', $matches[1]));

            } elseif (preg_match('/^FROM MQTT BROKER SERVER ([\w\.-]+)(?::(\d+))?$/', $line, $matches)) {
                $this->config['mqtt_broker_server_host'] = trim($matches[1]);
                $this->config['mqtt_broker_server_port'] = isset($matches[2]) ? (int)$matches[2] : 1883; // Default port

            } elseif (preg_match('/^BROADCASTING DATA TO WEBSOCKET SERVER (.+)$/', $line, $matches)) {
                $connectionString = trim($matches[1]);
                $this->config['websocket_server_connection_string'] = $connectionString;
    
                // Separar o host e o port
                $parts = explode(':', $connectionString, 2);
                $this->config['websocket_server_host'] = trim($parts[0]);

                // Verificar se existe uma segunda parte e definir o port
                $this->config['websocket_server_port'] = isset($parts[1]) ? (int)$parts[1] : 80; // 80 (ws) ou 443 (wss)
            }
        }
    }

    private function validateConfig(): void
    {
        $errors = [];

        if (empty($this->config['name'])) {
            $errors[] = 'Missing required <NAME> in "IOT MQTT ROUTER".';
        }
        
        if (empty($this->config['topics'])) {
            $errors[] = 'Missing required <TOPICS> in "SHOULD SUBSCRIBE MQTT TOPICS".';
        }

        if (empty($this->config['mqtt_broker_server_host'])) {
            $errors[] = 'Missing required <MQTT_broker_SERVER_HOST> in "FROM MQTT SERVER".';
        }
        
        if ($this->config['mqtt_broker_server_port'] === null) {
            $errors[] = 'Missing required <MQTT_BROKER_SERVER_PORT> in "FROM MQTT SERVER".';
        }

        if (empty($this->config['websocket_server_connection_string'])) {
            $errors[] = 'Missing required <WEBSOCKET_SERVER_CONNECTION_STRING> in "BROADCASTING DATA TO WEBSOCKET SERVER".';
        }
        
        if (!empty($errors)) {
            $this->printUsage();
            echo "\n";
            foreach ($errors as $error) {
                echo "Error: $error\n";
            }
            exit(1);
        }
    }

    private function printUsage(): void
    {
        global $argv;
        $filename = basename($argv[0]);

        echo <<<USAGE
Usage: php $filename -c config.iotws

Configuration should be provided in an IOTWS (IoT WebServer) file.

STRUCTURE:

\tIOT MQTT ROUTER <NAME>
\tSHOULD SUBSCRIBE MQTT TOPICS <TOPICS>
\tFROM MQTT BROKER SERVER [HOST:PORT]
\tBROADCASTING DATA TO WEBSOCKET SERVER [HOST:PORT]

EXAMPLE:

\tIOT MQTT ROUTER MyRouter1
\tSHOULD SUBSCRIBE MQTT TOPICS topic1, topic2, topic3
\tFROM MQTT BROKER SERVER localhost:1883
\tBROADCASTING DATA TO WEBSOCKET SERVER ws.example.com:8081

PROVIDED:

\n{$this}
USAGE;
    }

    public function getParameters(): array
    {
        return [
            'name' => $this->config['name'],
            'topics' => implode(',', $this->config['topics']),
            'mqtt_broker_server_host' => $this->config['mqtt_broker_server_host'],
            'mqtt_broker_server_port' => $this->config['mqtt_broker_server_port'],
            'websocket_server_connection_string' => $this->config['websocket_server_connection_string'],
            'websocket_server_host' => $this->config['websocket_server_host'],
            'websocket_server_port' => $this->config['websocket_server_port'],
        ];
    }

    public function __toString(): string
    {
        $params = $this->getParameters();

        return <<<DSL
\tIOT MQTT ROUTER {$params['name']}
\tSHOULD SUBSCRIBE MQTT TOPICS {$params['topics']}
\tFROM MQTT BROKER SERVER {$params['mqtt_broker_server_host']}:{$params['mqtt_broker_server_port']}
\tBROADCASTING DATA TO WEBSOCKET SERVER {$params['websocket_server_connection_string']}
DSL;
    }
}
