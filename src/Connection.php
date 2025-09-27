<?php

namespace CBM\Model;

use PDO;
use PDOException;

class Connection
{
    protected PDO $pdo;
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->connect();
    }

    protected function connect(): void
    {
        $configObj = new Config($this->config);
        $this->pdo = $configObj->getPDO();
    }

    public function getPDO(): PDO
    {
        try {
            $this->pdo->query('SELECT 1'); // Ping the database
        } catch (PDOException $e) {
            // Reconnect if ping fails
            $this->connect();
        }
        return $this->pdo;
    }
}