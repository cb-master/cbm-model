<?php
/**
 * Laika Database Model
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * This file is part of the Laika PHP MVC Framework.
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace CBM\Model;

use PDO;
use InvalidArgumentException;

class ConnectionManager
{
    private static array $connections = [];

    public static function add(array $config, string $name = 'default'): void
    {
        if (isset(self::$connections[$name])) {
            throw new InvalidArgumentException("Connection '{$name}' already exists.");
        }
        
        $pdo = Config::makeInstance($config);
        self::$connections[$name] = $pdo->getPDO();
    }

    public static function get(string $name = 'default'): PDO
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