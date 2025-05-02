<?php

namespace CBM\Model;

use PDO;
use InvalidArgumentException;

class ConnectionManager
{
    private static array $connections = [];

    public static function add(array $config, string $name = 'default'):void
    {
        if (isset(self::$connections[$name])) {
            throw new InvalidArgumentException("Connection '{$name}' already exists.");
        }
        
        $pdo = Config::makeInstance($config);
        self::$connections[$name] = $pdo->getPDO();
    }

    public static function get(string $name = 'default'):PDO
    {
        if (!isset(self::$connections[$name])) {
            throw new InvalidArgumentException("Connection '{$name}' does not exist.");
        }

        return self::$connections[$name];
    }

    public static function has(string $name): bool
    {
        return isset(self::$connections[$name]);
    }
}