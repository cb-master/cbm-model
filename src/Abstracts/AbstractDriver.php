<?php
/**
 * APP Name:        Laika DB Model
 * APP Provider:    Showket Ahmed
 * APP Link:        https://cloudbillmaster.com
 * APP Contact:     riyadtayf@gmail.com
 * APP Version:     1.0.0
 * APP Company:     Cloud Bill Master Ltd.
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