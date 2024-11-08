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

// Forbidden Access
defined('ROOTPATH') || http_response_code(403).die('403 Forbidden Access!');

use PDO;
use PDOException;
use CBM\ModelHelper\Resource;

$path = ROOTPATH . "/Config/Config.php";
if(!file_exists($path)){
    if(!file_exists(ROOTPATH . "/Config")){
        mkdir(ROOTPATH . "/Config", 0750);
    }
    file_put_contents($path, Resource::config_resources());
}

require_once($path);

class Database
{

    // Database Driver
    private String $driver;

    // Database Host
    private String $host;

    // Database Port
    private Int $port;

    // Database Name
    private String $name;

    // Database Username
    private String $user;

    // Database Password
    private String $password;

    // DSN
    private String $dsn;

    // PDO Instance
    private static $instance = null;

    // PDO Connection
    protected $pdo;

    // Offset
    protected $offset = 0;

    // Table
    protected $table;

    // Select
    protected $select = '*';

    // Join
    protected $join = [];

    // Where
    protected $where = [];

    // Filter
    protected $filter = [];

    // Operator
    protected $operator = '';

    // Order
    protected $order = '';

    // Limit
    protected $limit = '';

    // Parameters
    protected $params = [];

    // SQL Command
    protected $sql = '';

    // Database Drivers
    private Array $drivers = [
        'dblib'     =>   10060, // Microsoft SQL Server
        'mysql'     =>   3306, // Mysql Server
        'pgsql'     =>   5432, // Postgres Server
        'sqlite'    =>   0, // Sqlite
    ];

    // Initiate Database
    public function __construct($fetch = 'object'){
        // Get Resources
        $this->resource();
        $default_fetch = ($fetch != 'assoc') ? PDO::FETCH_OBJ : PDO::FETCH_ASSOC;
        try{
            $this->pdo = ($this->driver == 'sqlite') ? new PDO($this->dsn()) : new PDO($this->dsn(), $this->user, $this->password);

            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $default_fetch);
            $this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);

        }catch(PDOException $e){
            exit('<body style="margin:0;"><div style="height:100vh;position:relative;"><h1 style="text-align:center;color:#ef3a3a; position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);margin:0;">Connection Error!<br>['.$e->getCode().'] - '.$e->getMessage().'</h1></div></body>');
        }
    }

    // Get Resources
    private function resource()
    {
        // Get Host
        if(!defined('DB_HOST')){
            throw new PDOException("Database Host Error!", 85001);
        }
        // Get Driver
        if(!defined('DB_DRIVER')){
            throw new PDOException("Database Driver Error!", 85002);
        }
        // Get Name
        if(!defined('DB_NAME')){
            throw new PDOException("Database Name Error!", 85003);
        }
        // Get Username
        if(!defined('DB_USER')){
            throw new PDOException("Database User Error!", 85004);
        }
        // Get Password
        if(!defined('DB_PASSWORD')){
            throw new PDOException("Database Password Error!", 85005);
        }

        $this->host = DB_HOST;
        $this->driver = strtolower(DB_DRIVER);
        $this->port = defined('DB_PORT') ? DB_PORT : $this->drivers[$this->driver];
        $this->name = defined('DB_NAME') ? DB_NAME : '';
        $this->user = defined('DB_USER') ? DB_USER : '';
        $this->password = defined('DB_PASSWORD') ? DB_PASSWORD : '';
    }

    // Prepare DSN
    private function dsn():string
    {
        if($this->driver == 'dblib'){
            $this->dsn = "{$this->driver}:host={$this->host}:{$this->port};dbname=$this->name";
        }elseif(($this->driver == 'pgsql') || ($this->driver == 'mysql')){
            $this->dsn = "{$this->driver}:host={$this->host}:{$this->port};dbname=$this->name";
        }elseif($this->driver == 'sqlite'){
            $this->dsn = "{$this->driver}:{$this->host}";
        }
        return $this->dsn;
    }

    // Connection
    protected static function conn():Null|Object
    {
        if(!self::$instance)
        {
            self::$instance = new static;
        }
        return self::$instance;
    }

    // Reset SQL Statement
    protected function reset():void
    {
        $this->where = [];
        $this->filter = [];
        $this->join = [];
        $this->params = [];
        $this->select = '*';
        $this->order = '';
        $this->limit = '';
        $this->operator = '';
        $this->table = '';
        $this->offset = 0;
        $this->sql = '';
    }

    public function __debugInfo()
    {
        return [
            'db-info'       =>  'Sorry This is Protected. Please Follow The Documentation.'
        ];
    }
}