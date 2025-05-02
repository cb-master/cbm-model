<?php

namespace CBM\Model;

use PDO;
use Exception;
use InvalidArgumentException;

class Config
{
    private static ?self $instance = null;
    protected array $config;
    protected PDO $pdo;

    private function __construct(array $config)
    {
        $this->config = $config;
        $this->pdo = $this->createPDO();
    }

    public static function makeInstance(array $config = []):self
    {
        if(self::$instance === null){
            if(empty($config)){
                throw new InvalidArgumentException('Configuration required for first initialization.');
            }
            self::$instance = new self($config);
        }

        return self::$instance;
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
            throw new InvalidArgumentException("Driver class '{$driverClass}' does not exist.");
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

        $options = $options + $defaultOptions;

        return new PDO($dsn, $username, $password, $options);
    }

    private function __clone(){
        throw new Exception('Cloning is not allowed.');
    }
    
    public function __wakeup(){
        throw new Exception('Unserializing is not allowed.');
    }
}