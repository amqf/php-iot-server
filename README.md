# PHP INSTANT MQTT RELAY

Redirect MQTT data to a WebSocket server that broadcasts messages to all connected WebSocket clients.

- CREATE AWESOME REALTIME APPLICATIONS


Example:

- Render Web Dashboard for a IoT System

!['ilustration'](./image.png "How it works?")

# Requirements

- [mosquitto](https://mosquitto.org/download/) - MQTT Server

# Use Case

- Feature: Realtime Dashboard for IoT Systems

  Scenario: Display real-time data on the dashboard
    Given the IoT system is connected and sending data
    And the dashboard application is running
    When the dashboard receives new data from the MQTT broker
    Then the dashboard should update the displayed data in real-time


# Environment

Setup your environment

```
MQTT_HOST=localhost
MQTT_PORT=1883

WEB_SOCKET_HOST=localhost
WEB_SOCKET_PORT=8081
```

## Up Web Socket Broadcaster

- Receive data from MQTT Relay
- Broadcast received data to connected WebSocket Clients.

```php
./php web_socket_broadcaster.php 
```

## Up MQTT Relay

- Receive MQTT data
- Redirect MQTT data received to WebSocket Broadcaster
./php mqtt_relay.php -t ./topics.txt

## Up MQTT Server

- Receive MQTT data from a IoT Service

```
sudo systemctl start mosquitto
```


## Tips

Enshure that port 8081 is free

```bash
sudo kill $(lsof -i :8081 | awk 'NR>1 {print $2}')
```

## Exceptions Throwable

- Unreachable Web Socket Server -> Up MQTT Server