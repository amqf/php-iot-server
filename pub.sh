#!/bin/bash

# Verifica se o tópico e a mensagem foram fornecidos
if [ "$#" -ne 2 ]; then
    echo "Uso: $0 <tópico> <mensagem>"
    exit 1
fi

TOPIC=$1
MESSAGE=$2

# Publica a mensagem no tópico
mosquitto_pub -h 127.0.0.1 -p 1884 -t "$TOPIC" -m "$MESSAGE" && echo "Mensagem publicada no tópico '$TOPIC': $MESSAGE"
