<?php

namespace CBM\Model\Drivers;

class Pgsql
{
    public function dsn(array $config):string
    {
        $host = $config['host'] ?? 'localhost';
        $dbname = $config['dbname'] ?? '';
        $port = $config['port'] ?? 5432;

        return "pgsql:host={$host};port={$port};dbname={$dbname}";
    }
}