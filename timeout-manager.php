<?php

/**
 * @return resource|false
 */
function wait_server(
    string $server,
    string $host,
    int $port,
    float $timeout,
    bool $verbose=false
)
{
    // Attempt to open a socket connection
    $connection = @fsockopen(
        $host,
        $port,
        $errno,
        $errstr,
        5
    );

    if($verbose)
    {
        echo "\nConnecting to server $server...";

        if ($connection) {
            echo " conected";
            fclose($connection);
        } else {
            echo " unreachable\n";
            exit(1);
        }
    }

}