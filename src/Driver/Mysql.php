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
namespace CBM\Model\Driver;

use CBM\Model\Abstracts\AbstractDriver;
use CBM\ModelHelper\ModelExceptions;

class Mysql Extends AbstractDriver
{
    // Database DSN
    public static function dsn($host, $port, $name):string
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
    public static function user(?string $user):string|null
    {
        return $user ? $user : null;
    }

    // Database Password
    public static function password(?string $password):string|null
    {
        return $password ? $password : null;
    }
}