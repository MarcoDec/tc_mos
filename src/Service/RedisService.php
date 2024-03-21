<?php
// src/Service/RedisService.php

namespace App\Service;

use Predis\Client;

class RedisService
{
    private string $scheme;
    private string $host;
    private int $port;

    public function __construct(string $scheme, string $host, int $port)
    {
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;
    }

    public function getClient(): Client
    {
        return new Client([
            'scheme' => $this->scheme,
            'host' => $this->host,
            'port' => $this->port,
        ]);
    }
}
