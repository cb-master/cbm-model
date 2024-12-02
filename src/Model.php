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

use PDOException;
use CBM\ModelHelper\ModelExceptions;

class Model extends Database
{
    // Set Table
    public static function table(string $table):object
    {
        self::conn()->table = $table;
        return self::$instance;
    }

    ############################
    ###### CRUD FUNCTIONS ######
    ############################

    // Set Slect Columns
    public function select(string $columns = '*'):object
    {
        $this->action = 'select';
        $this->select = $columns;
        return $this;
    }

    // Set Group By
    public function group(string $columns)
    {
        $this->group = $columns;
        return $this;
    }

    // Set Having
    public function having(string $column)
    {
        $this->having = $column;
        return $this;
    }

    // Set Join
    public function join(string $table, string $condition, string $type = 'LEFT'):object
    {
        $type = strtoupper($type);
        $this->join[] = "{$type} JOIN {$table} ON {$condition}";
        return $this;
    }

    // Set Where
    public function filter(string $column, string $compare, Int|String $value, ?String $operator = null):object
    {
        $this->filter[] = "{$column} {$compare} ?" . ($operator ? " {$operator}": "");
        $this->params[] = $value;
        return $this;
    }

    // Set Where
    public function between(string $column, string $min, string $max, ?String $operator = null):object
    {
        $this->between[] = "{$column} BETWEEN {$min} AND {$max}" . ($operator ? " {$operator}": "");
        return $this;
    }

    // Set Where
    public function where(array $where, string $compare = '=', string $operator = 'AND'):object // $operator = AND / OR / && / ||
    {
        $this->operator = $operator;
        foreach($where as $key=>$value){
            $this->where[] = "{$key} {$compare} ?";
            $this->params[] = $value;
        }
        return $this;
    }

    // Set Order
    public function order(string $column, string $direction = 'ASC'):object
    {
        $direction = ucwords($direction);
        $this->order = "ORDER BY {$column} {$direction}";
        return $this;
    }

    // Set Limit
    public function limit(Int|String $limit = 20):object
    {
        $limit = (Int) $limit;

        $pagenumber = (int) ($_GET['page'] ?? 0) + 1;
        // Get Limit
        $limit = (int) $limit;
        
        // Set Offset
        $this->offset = ($pagenumber > 0 ) ? (($pagenumber - 1) * $limit) : 0;

        // Set Query
        $this->limit = "LIMIT {$limit} OFFSET {$this->offset}";
        return $this;
    }

    // Execute Database
    public function get():array
    {
        // Make Query
        $sql = $this->makeQuery();

        try{
            // Prepare Statement
            $stmt = $this->pdo->prepare($sql);
            // Execute Statement
            $stmt->execute($this->params);
            // Fetch Data
            $result = $stmt->fetchAll();
        }catch(PDOException $e){
            echo "[" . $e->getCode() . "] - " . $e->getMessage() . ". Line: " . $e->getFile() . ":" . $e->getLine();
        }

        // Reset Statemment Helpers
        $this->reset();
        // Return
        return $result ?? [];
    }

    // Execute Database For Single Value
    public function single():object|array
    {
        $result = [];
        $sql = $this->makeQuery();
      
        try{
            // Prepare Statement
            $stmt = $this->pdo->prepare($sql);
            // Execute Statement
            $stmt->execute($this->params);
            // Fetch Data
            $result = $stmt->fetch();
        }catch(PDOException $e){
            echo "[" . $e->getCode() . "] - " . $e->getMessage() . ". Line: " . $e->getFile() . ":" . $e->getLine();
        }
        
        // Reset Statemment Helpers
        $this->reset();

        // Return
        return $result ?: [];
    }

    // Insert Into Database
    public function insert(array $data):int
    {
        // Make Query
        $this->action = 'insert';
        $this->columns = array_keys($data);
        $this->placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = $this->makeQuery();

        // Prepare Statement
        $stmt = $this->pdo->prepare($sql);
        // Execute Statement
        $stmt->execute(array_values($data));

        // Reset Statemment Helpers
        $this->reset();
        // Return
        return (int) $this->pdo->lastInsertId();
    }

    // Replace Data
    public function replace($data):int
    {
        // Make Query
        $this->action = 'replace';
        $this->columns = array_keys($data);
        $this->placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = $this->makeQuery();

        // Prepare Statement
        $stmt = $this->pdo->prepare($sql);
        // Execute Statement
        $stmt->execute(array_values($data));

        $result = $stmt->rowCount();

        // Reset Statemment Helpers
        $this->reset();
        // Return
        return (int) $result;
    }

    // Update Data Into Table
    public function update(array $data):int
    {
        $this->action = 'delete';
        // Get Params
        $set = [];
        foreach ($data as $column => $value) {
            $this->columns[] = "{$column} = ?";
            // $set[] = "$column = ?";
            $params[] = $value;
        }

        // Get Params
        $toUpdate = array_merge($params, $this->params);

        // Make SQL
        $sql = $this->makeQuery();
        // Prepare Statement
        $stmt = $this->pdo->prepare($sql);
        // Execute Statement
        $stmt->execute($toUpdate);
        // Get Result
        $result = (int) $stmt->rowCount();
        // Reset Statemment Helpers
        $this->reset();
        // Return
        return $result;
    }

    // Delete Column
    public function pop():int
    {
        // Set Action
        $this->action = 'pop';
        
        // Make Query
        $this->makeQuery();
        // Prepare Statement
        $stmt = $this->pdo->prepare($this->sql);        
        // Execute Statement
        $stmt->execute($this->params);
        $result = (int) $stmt->rowCount();

        // Reset Statemment Helpers
        $this->reset();

        // Return
        return $result ?: 0;
    }

    // Generate UUID
    public static function uuid(string $table, string $column)
    {
        $time = substr(str_replace('.', '', microtime(true)), -6);
        $uid = 'uuid-'.bin2hex(random_bytes(3)).'-'.bin2hex(random_bytes(3)).'-'.bin2hex(random_bytes(3)).'-'.bin2hex(random_bytes(3)).'-'.$time;
        // Check Already Exist & Return
        if(self::table($table)->select()->filter($column, '=', $uid)->single()){
            return self::uuid($table, $column);
        }
        return strtoupper($uid);
    }

    #############################
    ###### TABLE FUNCTIONS ######
    #############################

    // Add a Column Definition
    public function column(string $name, string $type, bool $null = false, bool $autoIncrement = false, ?string $default = null):object
    {
        $column = "`{$name}` {$type}";
        $column .= !$null ? " NOT NULL" : " NULL";
        $column .= $autoIncrement ? " AUTO_INCREMENT" : '';
        $column .= $default ? " DEFAULT '{$default}'" : '';
        $this->columns[] = $column;
        return $this;
    }

    // Set Primary Key
    public function primary(string $key):object
    {
        try {
            if(!$this->primary){
                $this->primary = $key;
            }else{
                throw new ModelExceptions("Multiple Primary Key is Not Allowed", 85010);
            }
        } catch (ModelExceptions $e) {
            echo $e->message();
        }
        return $this;
    }

    // Set Unique Key
    public function unique(string $key):object
    {
        $this->unique[] = "UNIQUE({$key})";
        return $this;
    }

    // Set Index Key
    public function index(string $key):object
    {
        $this->index[] = "KEY({$key})";
        return $this;
    }

    // Set Fulltext Key
    public function fulltext(string $key):object
    {
        $this->fulltext[] = "FULLTEXT({$key})";
        return $this;
    }

    // Set Engine
    public function engine(?string $engine = null):object
    {
        $this->engine = $engine ?: $this->engine;
        return $this;
    }

    // Set Charset
    public function charset(?string $charset = null):object
    {
        $this->charset = $charset ?: $this->charset;
        return $this;
    }

    // Set Collate
    public function collate(?string $collate = null):object
    {
        $this->collate = $collate ?: $this->collate;
        return $this;
    }

    // Generate and Execute the Create Table SQL
    public function create():bool
    {
        try {
            // Check Table & Columns are Exist
            if (!$this->table || !$this->columns) {
                throw new PDOException("Table Name & Columns Must Be Defined.", 85006);
            }
            // Reset Values 
        }catch(PDOException $e){
            echo "[" . $e->getCode() . "] - " . $e->getMessage() . ". Line: " . $e->getFile() . ":" . $e->getLine();
        }
        // Create SQL Statement
        $this->sql = "CREATE TABLE `{$this->table}` (";
        $this->sql .= implode(",", $this->columns);

        // Primary Key if Exist
        if ($this->primary){
            $this->sql .= ", PRIMARY KEY ({$this->primary})";
        }
        
        // Unique Key if Exist
        if ($this->unique){
            $this->sql .= ", " . implode(", ", $this->unique);
        }

        // Index Key if Exist
        if ($this->index){
            $this->sql .= ", " . implode(", ", $this->index);
        }

        // Fulltext Key if Exist
        if ($this->fulltext){
            $this->sql .= ", " . implode(", ", $this->fulltext);
        }

        $this->sql .= ") ENGINE={$this->engine} DEFAULT CHARSET={$this->charset} COLLATE={$this->collate};";

        // Prepare Statement
        $stmt = $this->pdo->prepare($this->sql);

        // Execute Statement
        $result = $stmt->execute();

        // Reset Values
        $this->reset();
        return $result;
    }

    // Get Tables
    public function exist()
    {
        // Check Table Method Exist
        try {
            if (!$this->table) {
                throw new PDOException("Table Name Must Be Defined.", 85006);
            }
            // Reset Values 
        }catch(PDOException $e){
            echo "[" . $e->getCode() . "] - " . $e->getMessage() . ". Line: " . $e->getFile() . ":" . $e->getLine();
        }

        $this->sql = "SHOW TABLES";

        // Prepare Statement
        $stmt = self::conn()->pdo->prepare($this->sql);
        $stmt->execute();

        // Execute Statement
        $result = $stmt->fetchAll();

        $result = json_decode(json_encode($result), true);

        $found = false;

        foreach($result as $res){
            $found = in_array($this->table, $res) ? true : false;
            if($found){
                break;
            }
        }
        
        // Reset Values
        $this->reset();
        return $found;
    }
}