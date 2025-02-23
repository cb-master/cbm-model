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
    abstract public static function dsn(?string $host, ?string $name, int|string|bool|null $port = null):string;
}