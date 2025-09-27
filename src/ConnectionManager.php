<?php

namespace CBM\Model;

use PDO;
use InvalidArgumentException;

class ConnectionManager
{
    private static array $connections = [];

    public static function add(array $config, string $name = 'default'): void
    {
        // Make PDO Instance
        if (!self::has($name)) {
            $pdo = new Config($config);
            self::$connections[$name] = $pdo->getPDO();
        }
        return;
    }

    public static function get(string $name = 'default'): PDO
    {
        if (!self::has($name)) {
            throw new InvalidArgumentException("Connection '{$name}' does not exist.");
        }

        return self::$connections[$name];
    }

    public static function has(string $name): bool
    {
        return isset(self::$connections[$name]);
    }
}