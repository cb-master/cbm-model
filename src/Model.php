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

class Model extends Database
{
    // Fetch As Object Constant
    public const OBJECT = 'object';

    // Fetch As Array Constant
    public const ASSOC = 'assoc';

    // Get Connection
    public static function conn(string $fetch = 'object'):Null|Object
    {
        return parent::conn($fetch);
    }

    // Begin Transection
    public static function beginTransection()
    {
        Model::conn()->pdo->beginTransaction();
    }

    // Commit
    public static function commit()
    {
        Model::conn()->pdo->commit();
    }

    // Roll Back
    public static function rollBack()
    {
        Model::conn()->pdo->rollBack();
    }

    ############################
    ###### CRUD FUNCTIONS ######
    ############################

    // Set Table
    public function table(string $table):object
    {
        $this->table = $table;
        return $this;
    }

    // Set Slect Columns
    public function select(string $columns = '*'):object
    {
        $this->select = $columns;
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
    public function filter(string $column, string $operator, int|string $value):object
    {
        $this->filter[] = "{$column} {$operator} ?";
        $this->params[] = $value;
        return $this;
    }

    // Set Where
    public function where(array $where, string $compare = '=', string $operator = 'AND'):object // $operator = AND / OR / && / ||
    {
        $this->operator = $operator;
        $this->compare = $compare;
        foreach($where as $key=>$value){
            $this->where[] = "{$key} {$this->compare} ?";
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
        $this->sql = "SELECT {$this->select} FROM {$this->table}";
        $result = [];

        if (!empty($this->joins)) {
            $this->sql .= ' ' . implode(' ', $this->join);
        }

        if (!empty($this->where)) {
            $this->sql .= ' WHERE ' . implode(" {$this->operator} ", $this->where);
        }

        if (!empty($this->filter)) {
            $this->sql .= ' WHERE ' . implode(" AND ", $this->filter);
        }
        
        if (!empty($this->order)) {
            $this->sql .= " {$this->order}";
        }

        if (!empty($this->limit)) {
            $this->sql .= " {$this->limit}";
        }

        try{
            // Prepare Statement
            $stmt = $this->pdo->prepare($this->sql);
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
        return $result;
    }

    // Execute Database For Single Value
    public function single():object|array
    {
        $this->sql = "SELECT {$this->select} FROM {$this->table}";

        if (!empty($this->joins)) {
            $this->sql .= ' ' . implode(' ', $this->join);
        }

        if (!empty($this->where)) {
            $this->sql .= ' WHERE ' . implode(" {$this->operator} ", $this->where);
        }

        if (!empty($this->filter)) {
            $this->sql .= ' WHERE ' . implode(" AND ", $this->filter);
        }

        if (!empty($this->order)) {
            $this->sql .= " {$this->order}";
        }

        if (!empty($this->limit)) {
            $this->sql .= " {$this->limit}";
        }
                
        try{
            // Prepare Statement
            $stmt = $this->pdo->prepare($this->sql);
            // Execute Statement
            $stmt->execute($this->params);
        }catch(PDOException $e){
            echo "[" . $e->getCode() . "] - " . $e->getMessage() . ". Line: " . $e->getFile() . ":" . $e->getLine();
        }

        // Fetch Data
        $result = $stmt->fetch();
        
        // Reset Statemment Helpers
        $this->reset();
        // Return
        return $result ?: [];
    }

    // Insert Into Database
    public function insert(array $data):int
    {
        $columns = implode(', ', array_keys($data));

        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $this->sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        try{
            // Prepare Statement
            $stmt = $this->pdo->prepare($this->sql);
            // Execute Statement
            $stmt->execute(array_values($data));
        }catch(PDOException $e){
            echo "[" . $e->getCode() . "] - " . $e->getMessage() . ". Line: " . $e->getFile() . ":" . $e->getLine();
        }

        // Reset Statemment Helpers
        $this->reset();
        // Return
        return (int) $this->pdo->lastInsertId();
    }

    // Replace Data
    public function replace($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $this->sql = "REPLACE INTO {$this->table} ($columns) VALUES ($placeholders)";
        try{
            // Prepare Statement
            $stmt = $this->pdo->prepare($this->sql);
            // Execute Statement
            $stmt->execute(array_values($data));
        }catch(PDOException $e){
            echo "[" . $e->getCode() . "] - " . $e->getMessage() . ". Line: " . $e->getFile() . ":" . $e->getLine();
        }

        // Reset Statemment Helpers
        $this->reset();
        // Return
        return $this->pdo->lastInsertId();
    }

    // Update Data Into Table
    public function update(array $data):int
    {
        // Get Params
        $result = 0;
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "$column = ?";
            $params[] = $value;
        }

        // Get Params
        $toUpdate = array_merge($params, $this->params);
        
        try {
            // SQL Statement
            $this->sql = "UPDATE {$this->table} SET " . implode(', ', $set);
            // Check Where Statement
            if ($this->where || $this->filter){
                if($this->where){
                    $this->sql .= ' WHERE ' . implode(" {$this->operator} ", $this->where);
                }elseif($this->filter){
                    $this->sql .= ' WHERE ' . implode(" {$this->operator} ", $this->filter);
                }
    
                try{
                    // Prepare Statement
                    $stmt = $this->pdo->prepare($this->sql);    
                    // Execute Statement
                    $stmt->execute($toUpdate);
                    $result = (int) $stmt->rowCount();
                }catch(PDOException $e){
                    echo "[" . $e->getCode() . "] - " . $e->getMessage() . ". Line: " . $e->getFile() . ":" . $e->getLine();
                }
            }else{
                throw new PDOException("Where Clause Not Found: {$this->sql}", 85006);
            }
        }catch(PDOException $e) {
            echo "[" . $e->getCode() . "] - " . $e->getMessage() . ". Line: " . $e->getFile() . ":" . $e->getLine();
        }
        // Reset Statemment Helpers
        $this->reset();
        // Return
        return $result;
    }

    // Delete Column
    public function pop()
    {
        $result = 0;
        
        try {
            // SQL Statement
            $this->sql = "DELETE FROM {$this->table}";
            // Check Where Statement
            if($this->where || $this->filter){
                if($this->where){
                    $this->sql .= ' WHERE ' . implode(" {$this->operator} ", $this->where);
                }elseif($this->filter){
                    $this->sql .= ' WHERE ' . implode(" {$this->operator} ", $this->filter);
                }
                
                try{
                    // Prepare Statement
                    $stmt = $this->pdo->prepare($this->sql);        
                    // Execute Statement
                    $stmt->execute($this->params);
                    $result = (int) $stmt->rowCount();
                }catch(PDOException $e){
                    echo "[" . $e->getCode() . "] - " . $e->getMessage() . ". Line: " . $e->getFile() . ":" . $e->getLine();
                }
            }else{
                throw new PDOException("Where Clause Not Found: {$this->sql}", 85006);
            }
        }catch(PDOException $e){
            echo "[" . $e->getCode() . "] - " . $e->getMessage() . ". Line: " . $e->getFile() . ":" . $e->getLine();
        }

        // Reset Statemment Helpers
        $this->reset();

        // Return
        return $result;
    }

    // Generate UUID
    public static function uuid(string $table, string $column)
    {
        $time = substr(str_replace('.', '', microtime(true)), -6);
        $uid = 'uuid-'.bin2hex(random_bytes(3)).'-'.bin2hex(random_bytes(3)).'-'.bin2hex(random_bytes(3)).'-'.bin2hex(random_bytes(3)).'-'.$time;
        // Check Already Exist & Return
        if(self::conn()->table($table)->select()->filter($column, '=', $uid)->single()){
            return self::uuid($table, $column);
        }
        return strtoupper($uid);
    }

    #############################
    ###### TABLE FUNCTIONS ######
    #############################

    // Add a Column Definition
    public function addColumn(string $name, string $type, bool $notNull = true, bool $autoIncrement = false, ?string $default = null):object
    {
        $column = "`{$name}` {$type}";
        $column .= $notNull ? " NOT NULL" : '';
        $column .= $autoIncrement ? " AUTO_INCREMENT" : '';
        $column .= $default ? " DEFAULT '{$default}'" : '';
        $this->columns[] = $column;
        return $this;
    }

    // Set Primary Key
    public function primaryKey(string $key):object
    {
        $this->primaryKey = $key;
        return $this;
    }

    // Set Unique Key
    public function uniqueKey(string $key):object
    {
        $this->uniqueKey = $key;
        return $this;
    }

    // Set Index Key
    public function indexKey(string $key):object
    {
        $this->indexKey = $key;
        return $this;
    }

    // Set Fulltext Key
    public function fulltextKey(string $key):object
    {
        $this->fulltextKey = $key;
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
    
            // Create SQL Statement
            $this->sql = "CREATE TABLE `{$this->table}` (\n";
            $this->sql .= implode(",\n", $this->columns);
    
            // Primary Key if Exist
            if ($this->primaryKey){
                $this->sql .= ",\nPRIMARY KEY (`{$this->primaryKey}`)";
            }
            
            // Unique Key if Exist
            if ($this->uniqueKey){
                $this->sql .= ",\nUNIQUE KEY (`{$this->uniqueKey}`)";
            }

            // Index Key if Exist
            if ($this->indexKey){
                $this->sql .= ",\nKEY (`{$this->indexKey}`)";
            }

            // Fulltext Key if Exist
            if ($this->fulltextKey){
                $this->sql .= ",\nFULLTEXT KEY (`{$this->fulltextKey}`)";
            }

            $this->sql .= "\n)\nENGINE={$this->engine} DEFAULT CHARSET={$this->charset} COLLATE={$this->collate};";

            // Prepare Statement
            if(!($stmt = $this->pdo->prepare($this->sql))){
                throw new PDOException("SQL Prepare Error: {$this->sql}", 85006);
            }

            // Execute Statement
            if(!$stmt->execute()){
                throw new PDOException("SQL Statement Execution Error: {$this->sql}", 85007);
            }

            // Reset Values 
            $this->reset();
            return true;
        } catch (PDOException $e){
            echo '<pre>';
            print_r($e);
            echo '</pre>';
            echo "[" . $e->getCode() . "] - " . $e->getMessage() . ". Line: " . $e->getFile() . ":" . $e->getLine();
            return false;
        }
    }
}