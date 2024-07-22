<?php

namespace AMQF\IoTServer\Config;

use AMQF\IoTServer\Config\ConfigurationException;
use AMQF\IoTServer\Config\Settings\MQTTClientSettings;
use AMQF\IoTServer\Config\Settings\TopicsSettings;
use AMQF\IoTServer\Config\Settings\WebSocketServerSettings;

/**
 * Loads the IoTWS configuration for IoT Server, ensuring that required settings are in the correct format.
 * 
 * This class reads a configuration file and initializes the MQTT client and WebSocket server settings
 * based on the information extracted from the file.
 * 
 * @package AMQF\IoTServer\Config
 * @author Antônio M. Quadros Filho <antoniomquadrosfilho@gmail.com
 */
class IoTWSConfig
{
    /**
     * MQTT client settings.
     * 
     * @var MQTTClientSettings
     */
    private MQTTClientSettings $_mqttClientSettings;

    /**
     * WebSocket server settings.
     * 
     * @var WebSocketServerSettings
     */
    private WebSocketServerSettings $_webSocketServerSettings;


    /**
     * Constructs the IoTWSConfig class.
     * 
     * Reads the configuration file and initializes the properties based on the file information.
     * 
     * @param string $configPath The path to the configuration file.
     * 
     * @throws ConfigurationException If the MQTT Broker or WebSocket Server settings are invalid.
     */
    public function __construct(string $configPath)
    {
        /** @var string configuration file content */
        $fileContent = file_get_contents($configPath);

        preg_match('/SUBSCRIBING MQTT TOPICS (.+)/', $fileContent, $topicsMatch);
        preg_match('/FROM MQTT BROKER SERVER (.+)/', $fileContent, $mqttBrokerUrlMatch);
        preg_match('/BROADCASTING TO (.+) WITH WEBSOCKET SERVER/', $fileContent, $wsUrlMatch);

        
        if (!$mqttBrokerUrlMatch || !$topicsMatch)
        {
            throw new ConfigurationException('Invalid MQTT Broker settings');
        }

        if (!$wsUrlMatch)
        {
            throw new ConfigurationException('Invalid WebSocket Server settings');
        }

        /** @var MQTTClientSettings MQTT Client Settings */
        $this->_mqttClientSettings = new MQTTClientSettings(
            $mqttBrokerUrlMatch[1],
            new TopicsSettings($topicsMatch[1])
        );

        /** @var WebSocketServerSettings WebSocket Server Settings */
        $this->_webSocketServerSettings = new WebSocketServerSettings($wsUrlMatch[1]);
    }

    /**
     * Gets the MQTT client settings.
     * 
     * @return MQTTClientSettings The MQTT client settings.
     */
    function getMQTTClientSettings() : MQTTClientSettings
    {
        return $this->_mqttClientSettings;
    }

    /**
     * Gets the WebSocket server settings.
     * 
     * @return WebSocketServerSettings The WebSocket server settings.
     */
    function getWebSocketSettings() : WebSocketServerSettings
    {
        return $this->_webSocketServerSettings;
    }
}
