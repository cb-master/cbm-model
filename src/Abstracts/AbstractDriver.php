<?php
/**
 * Project: Laika MVC Framework
 * Author Name: Showket Ahmed
 * Author Email: riyadhtayf@gmail.com
 */

// Namespace
namespace CBM\Model\Abstracts;

abstract class AbstractDriver
{
    // Driver DSN
    abstract public static function dsn(string $host, string $name, int|string|bool|null $port = null):string;

    // Database User
    abstract public static function user(?string $user):string|null;

    // Database Password
    abstract public static function password(?string $password):string|null;
}