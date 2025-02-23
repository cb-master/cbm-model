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
    private static String $driver;

    // Database Host
    private static String $host;

    // Database Name
    private static String $name;

    // Database Port
    private static Int|String $port;

    // Database User
    private static String $user;

    // Database Password
    private static String $password;

    // Database Fetch as Object or Array
    protected static Bool $object = true;

    // Config Test
    private static $config = false;

    // Configure DB Model
    /**
     * @param array $config - Required Argument
     */
    public static function config(array $config):void
    {
        // Check Database Name Exist
        if(!isset($config['name'])){
            throw new Exception("'name' Key Missing in Database Configuration. Please Run Model::config(['name'=>'db_name'])", 85015);
        }
        // Check Database User Exist
        if(!isset($config['user'])){
            throw new Exception("'user' Key Missing in Database Configuration. Please Run Model::config(['user'=>'db_user_name'])", 85015);
        }
        // Check Database Password Exist
        if(!isset($config['password'])){
            throw new Exception("'password' Key Missing in Database Configuration. Please Run Model::config(['password'=>'db_password'])", 85015);
        }
        // Set Configuration
        self::$host     =   $config['host'] ?? 'localhost';
        self::$driver   =   $config['driver'] ?? 'mysql';
        self::$name     =   $config['name'];
        self::$port     =   (Int) ($config['port'] ?? 3306);
        self::$user     =   $config['user'];
        self::$password =   $config['password'];
        self::$object   =   (isset($config['object']) && is_bool($config['object'])) ? $config['object'] : self::$object;

        // Set Configuaration True
        self::$config = true;
    }

    // Get Driver Class
    protected static function driver():string
    {
        // Check Model Configured
        if(!self::$config){
            throw new Exception("Database Config Error. Run Model::config() First", 85014);
        }
        if(!in_array(ucfirst(self::$driver), self::supported_drivers())){
            throw new Exception(sprintf("Unsupported Database Driver '%s'!", self::$driver), 85012);
        }
        $class = "\\CBM\\Model\\Driver\\".ucfirst(self::$driver);
        if(!class_exists($class)){
            throw new Exception(sprintf("Driver '%s' Does Not Exist!", self::$driver), 85012);
        }
        return $class;
    }

    // Get Supported Databases
    public static function supported_drivers()
    {
        $files = glob(__DIR__.'/Driver/*.php');
        $drivers = [];
        foreach($files as $file){
            $drivers[] = ucfirst(basename($file, '.php'));
        }
        return $drivers;
    }

    // Database DSN
    protected function dsn():String
    {
        return call_user_func([self::driver(), 'dsn'], self::$host, self::$name, self::$port);
    }

    // Database User
    protected static function user()
    {
        return self::$user;
    }

    // Database Password
    protected static function password()
    {
        return self::$password;
    }
}