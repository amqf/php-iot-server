#!/usr/bin/env bash

# Show usage help
usage() {
    echo -e "Usage: $0 -c config.iotws [-v]"
    echo -e "  -c <config-file>   Path to the configuration file (required)"
    echo -e "  -v                 Verbose mode (optional)"
    exit 1
}

# Ensure at least one argument is provided
if [ $# -lt 2 ]; then
    usage
fi

# Default values
VERBOSE=""

# Parse arguments
while [[ "$1" != "" ]]; do
    case $1 in
        -c )
            shift
            DSL_CONFIG_FILE=$1
            ;;
        -v )
            VERBOSE="-v"  # Set verbose flag
            ;;
        * )
            usage
            ;;
    esac
    shift
done

# Ensure required config file
if [ -z "$DSL_CONFIG_FILE" ]; then
    usage
fi

# Up IOT Server with optional verbose flag
php iot-server.php -c "$DSL_CONFIG_FILE" $VERBOSE
