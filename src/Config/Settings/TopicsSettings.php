<?php

namespace AMQF\IoTServer\Config\Settings;

/**
 * Represents a MQTT Client Topics Settings for IoT Server
 * 
 * @package AMQF\IoTServer\Config\Settings
 * @author Antônio M. Quadros Filho <antoniomquadrosfilho@gmail.com>
 */
class TopicsSettings
{
    /**
     * @var array<string> MQTT Client Topics
     */
    private array $_topics;

    public function __construct(string $topics)
    {
        $this->_topics = array_map('trim', explode(',', $topics));
    }

    /**
     * Get MQTT Client Topics
     * 
     * @return array<string>
     */
    function getTopics() : array
    {
        return $this->_topics; 
    }
}