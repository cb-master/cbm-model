<?php

namespace CBM\Model\Drivers;

class Mysql
{
    public function dsn(array $config):string
    {
        $host = $config['host'] ?? 'localhost';
        $dbname = $config['dbname'] ?? '';
        $port = $config['port'] ?? 3306;
        $charset = $config['charset'] ?? 'utf8mb4';

        return "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
    }
}