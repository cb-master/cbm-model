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

    // Group
    protected String $group = '';

    // Having
    protected String $having = '';

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

    // Fetch As Object Constant
    public const OBJECT = 'object';

    // Fetch As Array Constant
    public const ASSOC = 'assoc';

    // Initiate Database
    public function __construct(string $fetch = 'object')
    {
        // Get Fetch Method
        $default_fetch = ($fetch != 'assoc') ? PDO::FETCH_OBJ : PDO::FETCH_ASSOC;

        // Get Connection
        try{
            $this->pdo = new PDO($this->dsn(), self::$user, self::$password);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $default_fetch);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
        }catch(PDOException $e){
            exit('<body style="margin:0;"><div style="height:100vh;position:relative;"><h1 style="text-align:center;color:#ef3a3a; position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);margin:0;">Connection Error!<br>['.$e->getCode().'] - '.$e->getMessage().'</h1></div></body>');
        }
    }

    // Begin Transection
    public static function beginTransaction()
    {
        self::conn();
        return self::$instance->pdo->beginTransaction();
    }

    // Commit Transection
    public static function commit()
    {
        return self::$instance->pdo->commit();
    }

    // Rollback Transection
    public static function rollBack()
    {
       return self::$instance->pdo->rollBack();
    }

    // Configure DB Model
    public static function config(array $config):void
    {
        try {
            // Get Host
            if(!isset($config['host'])){
                throw new PDOException("Database Host Error!", 85001);
            }

            // Get Name
            if(!isset($config['name'])){
                throw new PDOException("Database Name Error!", 85003);
            }

            // Get Username
            if(!isset($config['user'])){
                throw new PDOException("Database User Error!", 85004);
            }

            // Get Password
            if(!isset($config['password'])){
                throw new PDOException("Database Password Error!", 85005);
            }
        }catch(PDOException $e){
            echo "[" . $e->getCode() . "] - " . $e->getMessage() . ". Line: " . $e->getFile() . ":" . $e->getLine();
        }

        self::$host = $config['host'];
        self::$name = $config['name'];
        self::$port = (Int) ($config['port'] ?? 3306);
        self::$user = $config['user'];
        self::$password = $config['password'];
    }

    // Prepare DSN
    private function dsn():string
    {
        $this->dsn = "mysql:host=".self::$host.":".self::$port.";dbname=".self::$name;
        return $this->dsn;
    }

    // Connection
    public static function conn(string $fetch = 'object'):Null|Object
    {
        if(!self::$instance)
        {
            self::$instance = new Static($fetch);
        }
        return self::$instance;
    }

    // Reset SQL Statement
    protected function reset():void
    {
        $this->table        =   '';
        $this->group        =   '';
        $this->having       =   '';
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