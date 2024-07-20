#!/bin/bash

# Função para verificar a conexão com o broker
wait_for_broker() {
    local host=$1
    local port=$2
    local timeout=${3:-30}

    echo "Aguardando o broker MQTT em $host:$port..."
    start_time=$(date +%s)

    while true; do
        # Tenta conectar ao broker
        (echo > /dev/tcp/$host/$port) >/dev/null 2>&1
        if [ $? -eq 0 ]; then
            echo "Broker MQTT está disponível."
            return 0
        fi

        current_time=$(date +%s)
        elapsed=$((current_time - start_time))
        if [ $elapsed -ge $timeout ]; then
            echo "Tempo limite excedido aguardando o broker MQTT em $host:$port"
            return 1
        fi

        sleep 1
    done
}

# Configurações do broker
BROKER_HOST="instant-mqtt-relay-broker"
BROKER_PORT="1883"

# Espera pelo broker MQTT
wait_for_broker $BROKER_HOST $BROKER_PORT

# Executa os servidores
exec "$@"
