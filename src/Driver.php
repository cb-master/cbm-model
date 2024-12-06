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
namespace CBM\Model;

use CBM\ModelHelper\ModelExceptions;

class Driver
{
    // Database Object
    protected static String $driver;

    // Database Host
    protected static String $host;

    // Database Name
    protected static String $name;

    // Database Port
    protected static Int|String $port;

    // Database User
    protected static String $user;

    // Database Password
    protected static String $password;

    // Database Fetch as Object or Array
    protected static Bool $object = true;

    // Config Test
    private static $triggered = false;

    // Configure DB Model
    /**
     * @param array $config - Required Argument
     */
    public static function config(array $config):void
    {
        // Trigger Enable
        self::$triggered = true;
        // Set Configuration
        self::$driver   =   $config['driver'] ?? 'mysql';
        self::$host     =   $config['host'] ?? 'localhost';
        self::$name     =   $config['name'] ?? '';
        self::$port     =   (Int) ($config['port'] ?? 3306);
        self::$user     =   $config['user'] ?? 'no_user';
        self::$password =   $config['password'] ?? 'no_password';
        self::$object   =   (isset($config['object']) && is_bool($config['object'])) ? $config['object'] : self::$object;
    }

    // Get Driver Class
    public static function driver():string
    {
        // Check Model Configured
        try {
            if(!self::$triggered){
                throw new ModelExceptions("Database Config Error. Run Model::config() First", 85014);
            }
        } catch (ModelExceptions $e) {
            echo $e->message();
        }
        // Check Driver Exist
        try {
            if(isset(self::$driver)){
                $class = "\\CBM\\Model\\Driver\\".ucfirst(self::$driver);
                if(!class_exists("\\CBM\\Model\\Driver\\".ucfirst(self::$driver))){
                    throw new ModelExceptions("Driver '".self::$driver."' Does Not Exist!", 85012);
                }
            }else{
                throw new ModelExceptions("Database Driver Does Not Exist!", 85012);
            }
        } catch (ModelExceptions $e) {
            echo $e->message();
        }
        return $class ?? '';
    }

    // Database DSN
    protected function dsn():String
    {
        return self::driver()::dsn(self::$host, self::$name, self::$port);
    }

    // Database User
    protected static function user()
    {
        return self::driver()::user(self::$user);
    }

    // Database Password
    protected static function password()
    {
        return self::driver()::password(self::$password);
    }
}