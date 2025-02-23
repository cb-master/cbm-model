<?php
/**
 * Project: Cloud Bill Master Database Model
 * Author Name: Showket Ahmed
 * Author Email: riyadhtayf@gmail.com
 */

// Namespace
namespace CBM\Model\Driver;

use CBM\Model\Abstracts\AbstractDriver;

class Mariadb Extends AbstractDriver
{
    // Database DSN
    /**
     * @param string $host - Required Argument
     * @param ?string $name - Required Argument
     * @param int|string|bool $port - Required Argument
     */
    public static function dsn(?string $host, ?string $name, int|string|bool|null $port = null):string
    {
        return "mysql:host={$host}:{$port};dbname={$name}";
    }
}