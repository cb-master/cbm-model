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

use PDOException;
use CBM\ModelHelper\ModelExceptions;

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
    public function limit(Int|Null $limit = NULL):object
    {
        $limit = (Int) ($limit ?: LIMIT);
        $pagenumber = (int) $_GET('page') + 1;
        // Get Limit
        $limit = (int) $limit;
        
        // Set Offset
        $this->offset = ($pagenumber > 0) ? (($pagenumber - 1) * $limit) : 0;

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
            if(!($stmt = $this->pdo->prepare($this->sql))){
                throw new PDOException("SQL Error: {$this->sql}", 85006);
            }

            // Execute Statement
            if(!$stmt->execute($this->params)){
                throw new PDOException("SQL Param Error: {$this->sql}", 85007);
            }

            // Fetch Data
            $result = $stmt->fetchAll();
        }catch(PDOException $e){
            echo "[".$e->getCode() . "] - " . $e->getMessage() . ". Line: " . $e->getFile() . ":" . $e->getLine();
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
            if(!($stmt = $this->pdo->prepare($this->sql))){
                throw new PDOException("SQL Error: {$this->sql}", 85006);
            }

            // Execute Statement
            if(!$stmt->execute($this->params)){
                throw new PDOException("SQL Param Error: {$this->sql}", 85007);
            }            
        }catch(PDOException $e){
            echo "[".$e->getCode() . "] - " . $e->getMessage() . ". Line: " . $e->getFile() . ":" . $e->getLine();
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
            if(!($stmt = $this->pdo->prepare($this->sql))){
                throw new PDOException("SQL Error: {$this->sql}", 85006);
            }

            // Execute Statement
            if(!$stmt->execute(array_values($data))){
                throw new PDOException("SQL Param Error: {$this->sql}", 85007);
            }
        }catch(PDOException $e){
            echo "[".$e->getCode() . "] - " . $e->getMessage() . ". Line: " . $e->getFile() . ":" . $e->getLine();
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
            if(!($stmt = $this->pdo->prepare($this->sql))){
                throw new PDOException("SQL Error: {$this->sql}", 85006);
            }

            // Execute Statement
            if(!$stmt->execute(array_values($data))){
                throw new PDOException("SQL Param Error: {$this->sql}", 85007);
            }
        }catch(PDOException $e){
            echo "[".$e->getCode() . "] - " . $e->getMessage() . ". Line: " . $e->getFile() . ":" . $e->getLine();
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
                    if(!($stmt = $this->pdo->prepare($this->sql))){
                        throw new PDOException("SQL Error: {$this->sql}", 85006);
                    }
    
                    // Execute Statement
                    if(!$stmt->execute($toUpdate)){
                        throw new PDOException("SQL Param Error: {$this->sql}", 85007);
                    }
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
                    if(!($stmt = $this->pdo->prepare($this->sql))){
                        throw new PDOException("SQL Error: {$this->sql}", 85006);
                    }
        
                    // Execute Statement
                    if(!$stmt->execute($this->params)){
                        throw new PDOException("SQL Param Error: {$this->sql}", 85007);
                    }
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

        // Reset Values
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
}