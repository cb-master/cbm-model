<?php
/**
 * Project: Laika MVC Framework
 * Author Name: Showket Ahmed
 * Author Email: riyadhtayf@gmail.com
 */

// Namespace
namespace CBM\Model;

use CBM\ModelHelper\Resource;
use CBM\Handler\Error\Error;
use PDOException;
use PDO;

class Database Extends Driver
{    
    // PDO Instance
    protected static $instance = null;

    // PDO Connection
    protected $pdo;

    // Offset
    protected String $offset = '';

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

    // Compare
    protected String $compare = '';

    // Where
    protected string $where = '';

    // NOT
    protected string $not = '';

    // Order
    protected String $order = '';

    // Limit
    protected String $limit = '';

    // Parameters
    protected Array $params = [];

    // Columns
    protected Array $columns = [];

    // Placeholders
    protected String $placeholders = '';

    // Primary Key for Create Table
    protected String $primary = '';

    // Unique Key for Create Table
    protected Array $unique = [];

    // Index Key for Create Table
    protected Array $index = [];

    // Fulltext Key for Create Table
    protected Array $fulltext = [];

    // Engine for Create Table
    protected String $engine = 'InnoDB';

    // Charset for Create Table
    protected String $charset = 'utf8mb4';

    // Collate for Create Table
    protected String $collate = 'utf8mb4_general_ci';

    // SQL Command
    protected String $sql = '';

    // Initiate Database
    public function __construct()
    {
        // Get Fetch Method
        $fetch = (self::$object) ? PDO::FETCH_OBJ : PDO::FETCH_ASSOC;

        // Get Connection
        try{
            // Connect
            $this->pdo = new PDO($this->dsn(), $this->user(), $this->password());
            // Set Attributes
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $fetch);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
        }catch(PDOException $e){
            echo Resource::connection_error();
            exit();
        }
    }

    // Begin Transection
    public static function beginTransaction()
    {
        return self::conn()->pdo->beginTransaction();
    }

    // Commit Transection
    public static function commit()
    {
        return self::$instance->pdo->commit();
    }

    // Rollback Transection
    public static function rollBack()
    {
        try {
            self::$instance->pdo->rollBack();
        }catch(PDOException $e){}
    }

    // Connection
    public static function conn():Null|Object
    {
        if(!self::$instance)
        {
            self::$instance = new Static();
        }

        return self::$instance;
    }

    // Make Select Query
    protected function makeSelectQuery():string
    {
        // Check $this->table Exist
        try {
            if(!$this->table){
                throw new Error("Table Name Not Found!", 85009);
            }
        }catch(Error $er){
            Error::throw($er);
        }

        $this->sql = "{$this->table}";
        // Join SQL
        $this->sql .= $this->join ? ' ' . implode(' ', $this->join) : "";
        // Where SQL
        $this->sql .= $this->where ? " WHERE {$this->where} " : "";
        $this->sql = trim($this->sql);
        $this->sql = trim($this->sql, 'AND');
        $this->sql = trim($this->sql, 'OR');
        // Group SQL
        $this->sql .= $this->group ? " GROUP BY {$this->group}" : "";
        // Having SQL
        $this->sql .= $this->having ? " HAVING {$this->having}" : "";
        // Order SQL
        $this->sql .= $this->order ? " {$this->order}" : "";
        // Limit SQL
        $this->sql .= $this->limit ? " {$this->limit}" : "";
        // Offset SQL
        $this->sql .= $this->offset ? " {$this->offset}" : "";
        return $this->sql;
    }

    // Make Insert Query
    protected function makeInsertQuery():string
    {
        $columns = implode(', ', array_values($this->columns));
        return "{$this->table} ({$columns}) VALUES ({$this->placeholders})";
    }

    // Make Update Query
    protected function makeUpdateQuery():string
    {
        $this->sql = "{$this->table} SET " . implode(', ', $this->columns);
        // Check Where Statement
        try{
            if(!$this->where){
                throw new Error("Where Clause Not Found: {$this->sql}", 85006);
            }
        }catch(Error $er) {
            Error::throw($er);
        }
        // Where SQL
        $where = $this->not ? "WHERE NOT" : "WHERE";
        $this->sql .= $this->where ? " {$where} {$this->where} " : "";
        $this->sql = trim($this->sql);
        $this->sql = trim($this->sql, 'AND');
        $this->sql = trim($this->sql, 'OR');
        return $this->sql;
    }

    // Make Delete Query
    protected function makePopQuery():string
    {
        $this->sql = "{$this->table}";
        // Check Where Statement
        try{
            if(!$this->where){
                throw new Error("Where Clause Not Found: {$this->sql}", 85006);
            }
        }catch(Error $er) {
            Error::throw($er);
        }
        // Where SQL
        $where = $this->not ? "WHERE NOT" : "WHERE";
        $this->sql .= $this->where ? " {$where} {$this->where} " : "";
        $this->sql = trim($this->sql);
        $this->sql = trim($this->sql, 'AND');
        $this->sql = trim($this->sql, 'OR');
        return $this->sql;
    }

    // Reset SQL Statement
    protected function reset():void
    {
        $this->table        =   '';
        $this->group        =   '';
        $this->having       =   '';
        $this->compare      =   '';
        $this->where        =   '';
        $this->not          =   '';
        $this->join         =   [];
        $this->params       =   [];
        $this->select       =   '*';
        $this->order        =   '';
        $this->limit        =   '';
        $this->offset       =   '';
        $this->sql          =   '';
        $this->columns      =   [];
        $this->placeholders =   '';
        $this->primary      =   '';
        $this->unique       =   [];
        $this->index        =   [];
        $this->fulltext     =   [];
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