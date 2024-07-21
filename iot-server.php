<?php

use AMQF\InstantMQTTRelay\ConfigParser;
use AMQF\InstantMQTTRelay\MQTTService;
use AMQF\InstantMQTTRelay\WebSocket\WebSocketServer;
use AMQF\InstantMQTTRelay\WebSocket\WebSocketService;

require './vendor/autoload.php';
require 'timeout-manager.php';
require 'websocket-server.php';

define('FILENAME', $argv[0]);

/** ==================================
 *   Parse IoT MQTT Router arguments
 * ===================================
 */

 $options = getopt('c:v::');

/** =================================
 *   Argument 
 * ==================================
 */

 function usage($filename)
 {
     echo "Usage: $filename\n";
     echo "  -c <config-file>   Path to the configuration file (required)\n";
     echo "  -v                 Verbose mode (optional)\n";
     exit(1);
 }

if (!isset($options['c']) || empty($options['c']))
{
    usage(FILENAME);
}

/** @var ConfigParser $config */
$config = new ConfigParser($options['c']);

if (isset($options['v']))
{
    echo "Using IOTWS:\n";
    echo $config . "\n\n";
}

$params = $config->getParameters();

/** ==================================
 *   Setup IoT MQTT Router arguments
 * ===================================
 */

define('NAME', $params['name']);
define('TOPICS', $params['topics']);
define('MQTT_BROKER_SERVER_HOST', $params['mqtt_broker_server_host']);
define('MQTT_SERVER_PORT', $params['mqtt_broker_server_port']);
define('WEBSOCKET_SERVER_CONNECTION_STRING', $params['websocket_server_connection_string']);
define('WEBSOCKET_SERVER_HOST', $params['websocket_server_host']);
define('WEBSOCKET_SERVER_PORT', $params['websocket_server_port']);

start_web_socket_server('WEBSOCKET SERVER', WEBSOCKET_SERVER_PORT);

wait_server(
    'MQTT SERVER',
    MQTT_BROKER_SERVER_HOST,
    MQTT_SERVER_PORT,
    timeout: 5,
    verbose: true
);

wait_server(
    'WEBSOCKET SERVER',
    WEBSOCKET_SERVER_HOST,
    WEBSOCKET_SERVER_PORT,
    timeout: 10,
    verbose: true
);

try{
    $webSocketService = new WebSocketService(WEBSOCKET_SERVER_CONNECTION_STRING);
    $mqttService = new MQTTService(
        MQTT_BROKER_SERVER_HOST,
        MQTT_SERVER_PORT,
        'mqtt_client',
        $webSocketService
    );
    $mqttService->connect();

    foreach($topics as $topic)
    {
        $mqttService->subscribe($topic);
    }

    $mqttService->loop();
}catch(DomainException $exception)
{
    echo sprintf("DomainException: %s", $exception->getMessage());
    exit($exception->getCode());
}