<?php
/**
 * Project: Laika MVC Framework
 * Author Name: Showket Ahmed
 * Author Email: riyadhtayf@gmail.com
 */

// Namespace
namespace CBM\Model;

use PDO;
use PDOException;
use CBM\ModelHelper\Resource;
use CBM\ModelHelper\ModelExceptions;

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
    protected Array $where = [];

    // Filter
    protected Array $filter = [];

    // Filter
    protected Array $between = [];

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

    // Query Action
    protected String $action = '';

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
            echo '<pre>';
            print_r($e);
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

    // Make Query
    public function makeQuery():string
    {
        try {
            if(!$this->action){
                throw new ModelExceptions("SQL Building Error!", 85008);
            }
        }catch(ModelExceptions $e){
            echo $e->message();
        }

        // Check $this->table Exist
        try {
            if(!$this->table){
                throw new ModelExceptions("Table Name Not Found!", 85009);
            }
        }catch(ModelExceptions $e){
            echo $e->message();
        }

        // SQL For Select
        if($this->action === 'select'){
            $this->sql = "SELECT {$this->select} FROM {$this->table}";
            // Join SQL
            $this->sql .= $this->join ? ' ' . implode(' ', $this->join) : "";
            // Where SQL
            if($this->where){
                $this->sql .= $this->where ? ' WHERE ' . implode(" {$this->compare} ", $this->where) : '';
            }
            // Filter SQL
            if($this->filter){
                $this->sql .= $this->filter ? ' WHERE ' . implode(" ", $this->filter) : '';
            }
            // Between SQL
            if($this->between){
                $this->sql .= $this->between ? ' WHERE ' . implode(" ", $this->between) : '';
            }
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
        }
        // SQL For Insert
        elseif($this->action === 'insert'){
            $columns = implode(', ', array_values($this->columns));
            $this->sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$this->placeholders})";
        }
        // SQL For Replace
        elseif($this->action === 'replace'){
            $columns = implode(', ', array_values($this->columns));
            $this->sql = "REPLACE INTO {$this->table} ({$columns}) VALUES ({$this->placeholders})";
        }
        // SQL For Update
        elseif($this->action === 'update'){
            $columns = implode(', ', array_values($this->columns));
            $this->sql = "UPDATE {$this->table} SET " . implode(', ', $this->columns);
            // Check Where Statement
            if ($this->where || $this->filter || $this->between){
                // Where SQL
                if($this->where){
                    $this->sql .= $this->where ? ' WHERE ' . implode(" {$this->compare} ", $this->where) : '';
                }
                // Filter SQL
                if($this->filter){
                    $this->sql .= $this->filter ? ' WHERE ' . implode(" ", $this->filter) : '';
                }
                // Between SQL
                if($this->between){
                    $this->sql .= $this->between ? ' WHERE ' . implode(" ", $this->between) : '';
                }
            }else{
                throw new ModelExceptions("Where Clause Not Found: {$this->sql}", 85006);
            }
        }
        // SQL For Delete
        elseif($this->action === 'pop'){
            $columns = implode(', ', array_values($this->columns));
            $this->sql = "DELETE FROM {$this->table}";
            // Check Where Statement
            if ($this->where || $this->filter || $this->between){
                // Where SQL
                if($this->where){
                    $this->sql .= $this->where ? ' WHERE ' . implode(" {$this->compare} ", $this->where) : '';
                }
                // Filter SQL
                if($this->filter){
                    $this->sql .= $this->filter ? ' WHERE ' . implode(" ", $this->filter) : '';
                }
                // Between SQL
                if($this->between){
                    $this->sql .= $this->between ? ' WHERE ' . implode(" ", $this->between) : '';
                }
            }else{
                throw new ModelExceptions("Where Clause Not Found: {$this->sql}", 85006);
            }
        }

        // Return Query
        return $this->sql;
    }

    // Reset SQL Statement
    protected function reset():void
    {
        $this->table        =   '';
        $this->group        =   '';
        $this->having       =   '';
        $this->compare      =   '';
        $this->where        =   [];
        $this->filter       =   [];
        $this->between      =   [];
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