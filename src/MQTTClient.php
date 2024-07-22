<?php

namespace AMQF\IoTServer;

use AMQF\IoTServer\Config\ConfigurationException;
use AMQF\IoTServer\Config\Settings\MQTTClientSettings;
use PhpMqtt\Client\MqttClient as Client;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Exceptions\ConfigurationInvalidException;
use PhpMqtt\Client\Exceptions\ProtocolNotSupportedException;
use React\Promise\Deferred;

class MQTTClient
{
    private Client $_client;

    /**
     * Constructs the MQTTClient class.
     * 
     * Setup MQTT client given the settings
     * 
     * @param MQTTClientSettings $_settings The path to the configuration file.
     * 
     * @throws ConfigurationException If the MQTT Broker settings are invalid.
     */
    public function __construct(private MQTTClientSettings $_settings)
    {
        try{
            $this->_client = new Client(
                $_settings->getURL(),
                $_settings->getPort(),
                'php-mqtt-client'
            );
        }catch(ProtocolNotSupportedException $exception)
        {
            throw new ConfigurationException(
                message: 'Invalid MQTT Client Settings: ' . $exception->getMessage(),
                code: 1,
                previous: $exception
            );
        }
    }

    public function connect() : void
    {
        try{
            $this->_client->connect(
                (new ConnectionSettings)
                ->setUsername($this->_settings->getUsername())
                ->setPassword($this->_settings->getPassword())
            );
        }catch(ConfigurationInvalidException $exception)
        {
            throw new ConfigurationException(
                message: 'Invalid MQTT Client settings: ' . $exception->getMessage(),
                code: 1,
                previous: $exception
            );
        }
    }

    public function subscribe(callable $callback) : void
    {
        foreach ($this->_settings->getTopics() as $topic) {
            $this->_client->subscribe(
                $topic,
                function (
                    string $topic,
                    string $message,
                    bool $retained
                ) use ($callback) {
                    $callback($topic, $message);
                },
                qualityOfService: 0
            );
        }
    }

    public function disconnect()
    {
        $this->_client->disconnect();
    }

    public function run() : void
    {
        $this->_client->loop(true);
    }
}
