<?php

namespace CBM\Model;

use PDO;
use Exception;
use InvalidArgumentException;

class Config
{
    protected array $config;
    protected PDO $pdo;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->pdo = $this->createPDO();
    }

    public function getPDO():PDO
    {
        return $this->pdo;
    }

    protected function createPDO():PDO
    {
        $driver = strtolower($this->config['driver'] ?? '');

        if (empty($driver)) {
            throw new InvalidArgumentException('Database driver not specified.');
        }

        $driverClass = __NAMESPACE__ . '\\Drivers\\' . ucfirst($driver);

        if (!class_exists($driverClass)) {
            throw new InvalidArgumentException("Invalid Driver: '{$driver}'");
        }

        $driverInstance = new $driverClass();
        $dsn = $driverInstance->dsn($this->config);

        $username = $this->config['username'] ?? null;
        $password = $this->config['password'] ?? null;
        $options = $this->config['options'] ?? [];

        $defaultOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $options += $defaultOptions;

        return new PDO($dsn, $username, $password, $defaultOptions);
    }

    private function __clone(){
        throw new Exception('Cloning is not allowed.');
    }
    
    public function __wakeup(){
        throw new Exception('Unserializing is not allowed.');
    }
}