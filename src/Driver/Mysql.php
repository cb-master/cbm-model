<?php
/**
 * Project: Laika MVC Framework
 * Author Name: Showket Ahmed
 * Author Email: riyadhtayf@gmail.com
 */

// Namespace
namespace CBM\Model\Driver;

use CBM\Model\Abstracts\AbstractDriver;
use Exception;

class Mysql Extends AbstractDriver
{
    // Database DSN
    /**
     * @param string $host - Required Argument
     * @param ?string $name - Required Argument
     * @param int|string|bool $port - Required Argument
     */
    public static function dsn(string $host, string $name, int|string|bool|null $port = null):string
    {
        if(!$host){
            throw new Exception("Mysql Database Host Error", 85001);
        }
        if(!$port){
            throw new Exception("Mysql Database Port Error", 85013);
        }
        if(!$name){
            throw new Exception("Mysql Database Name Error", 85003);
        }
        return "mysql:host={$host}:{$port};dbname={$name}";
    }

    // Database User
    /**
     * @param ?string $user - Required Argument
     */
    public static function user(?string $user):string|null
    {
        return $user;
    }

    // Database Password
    /**
     * @param ?string $password - Required Argument
     */
    public static function password(?string $password):string|null
    {
        return $password;
    }
}