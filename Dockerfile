# Use uma imagem base com PHP 8.3
FROM php:8.3-cli

# Atualize a lista de pacotes e instale dependências do sistema
RUN apt-get update && apt-get install -y \
    libssl-dev \
    libwebsockets-dev \
    # netcat \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Instala o Composer manualmente
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Copia o código da aplicação e o script de espera para o container
COPY . /usr/src/app
COPY wait-for-broker.sh /usr/src/app/wait-for-broker.sh

# Define o diretório de trabalho
WORKDIR /usr/src/app

# Define as permissões do script
RUN chmod +x wait-for-broker.sh

# Instala as dependências do Composer
RUN composer install

# Expõe a porta do WebSocket
EXPOSE 8081

# Comando para executar o script de espera e, em seguida, iniciar os servidores
# CMD ["bash", "/usr/src/app/wait-for-broker.sh"]
CMD ["bash", "/usr/src/app/wait-for-broker.sh", "php", "/usr/src/app/socket_server.php", "&", "php", "/usr/src/app/mqtt_server.php"]
