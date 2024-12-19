<?php
/**
 * Project: Laika MVC Framework
 * Author Name: Showket Ahmed
 * Author Email: riyadhtayf@gmail.com
 */

// Namespace
namespace CBM\Model\Driver;

use CBM\Model\Abstracts\AbstractDriver;
use CBM\ModelHelper\ModelExceptions;

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
        try {
            if(!$host){
                throw new ModelExceptions("Database Host Error", 85001);
            }
            if(!$port){
                throw new ModelExceptions("Database Port Error", 85013);
            }
            if(!$name){
                throw new ModelExceptions("Database Name Error", 85003);
            }
        } catch (ModelExceptions $e) {
            echo $e->message();
        }
        return "mysql:host={$host}:{$port};dbname={$name}";
    }

    // Database User
    /**
     * @param ?string $user - Required Argument
     */
    public static function user(?string $user):string|null
    {
        return $user ? $user : null;
    }

    // Database Password
    /**
     * @param ?string $password - Required Argument
     */
    public static function password(?string $password):string|null
    {
        return $password ? $password : null;
    }
}