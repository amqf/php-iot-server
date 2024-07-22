<?php

namespace AMQF\IoTServer\Config\Settings;

use AMQF\IoTServer\Config\ConfigurationException;

/**
 * Represents a MQTT Client Settings for IoT Server
 * 
 * @package AMQF\IoTServer\Config\Settings
 * @author Antônio M. Quadros Filho <antoniomquadrosfilho@gmail.com>
 */
final class MQTTClientSettings
{
    private string $_host;
    private int $_port;
    private ?string $_username = null;
    private ?string $_password = null;

    public function __construct(
        string $url,
        private TopicsSettings $_topicsSettings)
    {
        /** @var array */
        $parsedURL = parse_url(trim($url));

        if(!isset($parsedURL['host']))
        {
            throw new ConfigurationException("Invalid URL [$url] for MQTT Client.");
        }


        /** @var int */
        $port = isset($parsedURL['port']) ? $parsedURL['port'] : 1883;

        if($port != null && ($port < 1024 || $port > 49151))
        {
            // Portas Bem Conhecidas (0-1023): Reservadas para serviços e protocolos conhecidos. Ex.: HTTP (80), HTTPS (443), FTP (21), SSH (22).
            // Portas Registradas (1024-49151): Podem ser usadas por aplicações de usuário ou serviços menos comuns.
            // Portas Dinâmicas/Privadas (49152-65535): Normalmente usadas para clientes se conectando a um serviço, ou para aplicações internas.
            throw new ConfigurationException("Invalid port [$port] for MQTTClient. Should be >= 1024 or <= 49151.");
        }

        $this->_host = trim($url);
        $this->_port = $port;
        
        if(isset($parsedURL['user'])){
            $this->_username = $parsedURL['user'];
        }

        if(isset($parsedURL['pass'])){
            $this->_password = $parsedURL['pass'];
        }
    }

    /**
     * Get MQTT Client Topics
     * 
     * @return array<string> MQTT Client Topics
     */
    function getTopics() : array
    {
        return $this->_topicsSettings->getTopics();
    }

    /**
     * Get MQTT Client URL
     * 
     * @return string MQTT Client URL
     */
    function getURL() : string
    {
        return sprintf(
            '%s:%s',
            $this->_host,
            $this->_port
        );
    }

    /**
     * Get MQTT Client host
     * 
     * @return string MQTT Client host
     */
    function getHost() : string
    {
        return $this->_host;
    }

    /**
     * Get MQTT Client port
     * 
     * @return int MQTT Client port
     */
    function getPort() : int
    {
        return $this->_port;
    }

    /**
     * Get MQTT Client username
     * 
     * @return string MQTT Client username
     */
    function getUsername() : ?string
    {
        return $this->_username;
    }

    /**
     * Get MQTT Client password
     * 
     * @return string MQTT Client password
     */
    function getPassword() : ?string
    {
        return $this->_password;
    }
}