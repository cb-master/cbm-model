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
    // Databse Instance
    // abstract public function instance():object;

    // Driver DSN
    abstract public static function dsn(String $host, Int|String|Bool $port, ?String $name):string;

    // Database User
    abstract public static function user(?string $user):string|null;

    // Database Password
    abstract public static function password(?string $password):string|null;
}