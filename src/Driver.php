<?php
/**
 * Project: Laika MVC Framework
 * Author Name: Showket Ahmed
 * Author Email: riyadhtayf@gmail.com
 */

// Namespace
namespace CBM\Model;

use Exception;

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
    private static $config = false;

    // Supported Drivrs
    private static array $drivers = ['mariadb', 'mysql'];

    // Configure DB Model
    /**
     * @param array $config - Required Argument
     */
    public static function config(array $config):void
    {
        // Check Database Driver Exist
        if(!isset($config['driver'])){
            throw new Exception("Database Driver Does Not Exist! Please Run Model::config(['driver'=>'mysql'])", 85012);
        }
        // Check Database Name Exist
        if(!isset($config['name'])){
            throw new Exception("Database Name Not Found. Please Run Model::config(['name'=>'db_name'])", 85015);
        }
        // Check Database User Exist
        if(!isset($config['user'])){
            throw new Exception("Database User Not Found. Please Run Model::config(['user'=>'db_user_name'])", 85015);
        }
        // Check Database Password Exist
        if(!isset($config['password'])){
            throw new Exception("Database User Password Not Found. Please Run Model::config(['password'=>'db_password'])", 85015);
        }
        // Set Configuration
        self::$host     =   $config['host'] ?? 'localhost';
        self::$driver   =   $config['driver'];
        self::$name     =   $config['name'];
        self::$port     =   (Int) ($config['port'] ?? 3306);
        self::$user     =   $config['user'];
        self::$password =   $config['password'];
        self::$object   =   (isset($config['object']) && is_bool($config['object'])) ? $config['object'] : self::$object;

        // Set Configuaration True
        self::$config = true;
    }

    // Get Driver Class
    public static function driver():string
    {
        // Check Model Configured
        if(!self::$config){
            throw new Exception("Database Config Error. Run Model::config() First", 85014);
        }
        // Check Driver Exist
        if(!isset(self::$driver)){
            throw new Exception("Database Driver Does Not Exist!", 85012);
        }
        if(!in_array(self::$driver, self::$drivers)){
            throw new Exception(sprintf("Unsupported Database Driver '%s'!", self::$driver), 85012);
        }
        $class = "\\CBM\\Model\\Driver\\".ucfirst(self::$driver);
        if(!class_exists($class)){
            throw new Exception("Driver '".self::$driver."' Does Not Exist!", 85012);
        }
        return $class;
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