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

use PDO;
use PDOException;

class Database
{    
    // PDO Instance
    private static $instance = null;

    // Database Driver
    private static String $driver;

    // Database Host
    private static String $host;

    // Database Port
    private static Int $port;

    // Database Name
    private static String $name;

    // Database Username
    private static String $user;

    // Database Password
    private static String $password;

    // DSN
    private String $dsn;

    // PDO Connection
    protected $pdo;

    // Offset
    protected $offset = 0;

    // Table
    protected String $table = '';

    // Select
    protected String $select = '*';

    // Join
    protected Array $join = [];

    // Where
    protected Array $where = [];

    // Filter
    protected Array $filter = [];

    // Compare
    protected String $compare = '';

    // Operator
    protected String $operator = '';

    // Order
    protected String $order = '';

    // Limit
    protected Int $limit = 0;

    // Parameters
    protected Array $params = [];

    // Columns for Create Table
    protected Array $columns = [];

    // Primary Key for Create Table
    protected String $primaryKey = '';

    // Unique Key for Create Table
    protected String $uniqueKey = '';

    // Index Key for Create Table
    protected String $indexKey = '';

    // Fulltext Key for Create Table
    protected String $fulltextKey = '';

    // Engine for Create Table
    protected String $engine = 'InnoDB';

    // Charset for Create Table
    protected String $charset = 'utf8mb4';

    // Collate for Create Table
    protected String $collate = 'utf8mb4_general_ci';

    // SQL Command
    protected String $sql = '';

    // Database Drivers
    private static Array $ports = [
        'dblib'     =>   10060, // Microsoft SQL Server
        'mysql'     =>   3306, // Mysql Server
        'pgsql'     =>   5432, // Postgres Server
        'sqlite'    =>   0, // Sqlite
    ];

    // Initiate Database
    public function __construct(string $fetch = 'object')
    {
        // Get Resources
        // $this->resource();

        // Get Fetch Method
        $default_fetch = ($fetch != 'assoc') ? PDO::FETCH_OBJ : PDO::FETCH_ASSOC;
        try{
            $this->pdo = (self::$driver == 'sqlite') ? new PDO($this->dsn()) : new PDO($this->dsn(), self::$user, self::$password);

            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $default_fetch);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);

        }catch(PDOException $e){
            exit('<body style="margin:0;"><div style="height:100vh;position:relative;"><h1 style="text-align:center;color:#ef3a3a; position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);margin:0;">Connection Error!<br>['.$e->getCode().'] - '.$e->getMessage().'</h1></div></body>');
        }
    }

    // Begin Transection
    protected static function beginTransection()
    {
        self::conn();
        self::$instance->pdo->beginTransaction();
    }


    // Configure DB Model
    public static function config(array $config):void
    {
        try {
            // Get Host
            if(!isset($config['host'])){
                throw new PDOException("Database Host Error!", 85001);
            }

            // Get Driver
            if(!isset($config['driver'])){
                throw new PDOException("Database Driver Error!", 85002);
            }

            // Get Name
            if(!isset($config['name'])){
                throw new PDOException("Database Name Error!", 85003);
            }

            // Get Username
            if(!isset($config['user']) && self::requiredCredentials($config['driver'])){
                throw new PDOException("Database User Error!", 85004);
            }

            // Get Password
            if(!isset($config['password']) && self::requiredCredentials($config['driver'])){
                throw new PDOException("Database Password Error!", 85005);
            }
        }catch(PDOException $e){
            echo "[" . $e->getCode() . "] - " . $e->getMessage() . ". Line: " . $e->getFile() . ":" . $e->getLine();
        }

        self::$driver = strtolower($config['driver']);
        self::$host = $config['host'];
        self::$name = $config['name'];
        self::$port = $config['port'] ?? self::$ports[self::$driver];
        self::$user = $config['user'] ?? 'user_key_missing';
        self::$password = $config['password'] ?? 'password_key_missing';
    }

    // Prepare DSN
    private function dsn():string
    {
        if(self::$driver == 'dblib'){
            $this->dsn = self::$driver.":host=".self::$host.":".self::$port.";dbname=".self::$name;
        }elseif((self::$driver == 'pgsql') || (self::$driver == 'mysql')){
            $this->dsn = self::$driver.":host=".self::$host.":".self::$port.";dbname=".self::$name;
        }elseif(self::$driver == 'sqlite'){
            $this->dsn = self::$driver.":".self::$host;
        }
        return $this->dsn;
    }

    // Connection
    protected static function conn(string $fetch = 'object'):Null|Object
    {
        if(!self::$instance)
        {
            self::$instance = new static($fetch);
        }
        return self::$instance;
    }

    // Set Database Credentials
    private static function requiredCredentials(string $driver):bool
    {
        $driver = strtolower($driver);
        return (($driver == 'dblib') || ($driver == 'mysql') || (($driver == 'pgsql')));
    }

    // Reset SQL Statement
    protected function reset():void
    {
        $this->table        =   '';
        $this->where        =   [];
        $this->filter       =   [];
        $this->join         =   [];
        $this->params       =   [];
        $this->select       =   '*';
        $this->order        =   '';
        $this->limit        =   0;
        $this->compare      =   '';
        $this->operator     =   '';
        $this->offset       =   0;
        $this->sql          =   '';
        $this->columns      =   [];
        $this->primaryKey   =   '';
        $this->uniqueKey    =   '';
        $this->indexKey     =   '';
        $this->fulltextKey  =   '';
        $this->engine       =   'InnoDB';
        $this->charset      =   'utf8mb4';
        $this->collate      =   'utf8mb4_general_ci';
    }

    // Hide Properties and Methods 
    public function __debugInfo()
    {
        return [
            'db-info'       =>  'Sorry This is Protected. Please Follow The Documentation.'
        ];
    }
}