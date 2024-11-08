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

use CBM\ModelHelper\ModelExceptions;

class Model extends Database
{
    // Fetch As Object Constant
    public const OBJECT = 'object';

    // Fetch As Array Constant
    public const ASSOC = 'assoc';

    // Get Connection
    public static function conn():Null|Object
    {
        return parent::conn();
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
        $this->$operator = $operator;
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
            $this->sql .= ' ' . $this->order;
        }

        if (!empty($this->limit)) {
            $this->sql .= ' ' . $this->limit;
        }
        try{
            if(!($stmt = $this->pdo->prepare($this->sql))){
                throw new ModelExceptions("SQL Error: {$this->sql}", 85006);
            }
            if(!$stmt->execute($this->params)){
                throw new ModelExceptions("SQL Param Error: {$this->sql}", 85007);
            }
            $stmt->execute($this->params);
            $result = $stmt->fetchAll();
        }catch(ModelExceptions $e){
            echo $e->message();
        }

        // Reset Values
        $this->reset();

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
            $this->sql .= ' ' . $this->order;
        }

        if (!empty($this->limit)) {
            $this->sql .= ' ' . $this->limit;
        }

        try{
            if(!($stmt = $this->pdo->prepare($this->sql))){
                throw new ModelExceptions("SQL Error: {$this->sql}", 85006);
            }
            if(!$stmt->execute($this->params)){
                throw new ModelExceptions("SQL Param Error: {$this->sql}", 85007);
            }
            $stmt->execute($this->params);
            $result = $stmt->fetch();
        }catch(ModelExceptions $e){
            echo $e->message();
        }
        
        // Reset Values
        $this->reset();
        return $result;
    }

    // Insert Into Database
    public function insert(array $data):int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $this->sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        try{
            if(!($stmt = $this->pdo->prepare($this->sql))){
                throw new ModelExceptions("SQL Error: {$this->sql}", 85006);
            }
            if(!$stmt->execute(array_values($data))){
                throw new ModelExceptions("SQL Param Error: {$this->sql}", 85007);
            }
            $stmt->execute($this->params);
        }catch(ModelExceptions $e){
            echo $e->message();
        }

        // Reset Values
        $this->reset();
        return (int) $this->pdo->lastInsertId(); // Returns the ID of the last inserted row
    }

    // Replace Data
    public function replace($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $result = 0;

        $this->sql = "REPLACE INTO {$this->table} ($columns) VALUES ($placeholders)";
        try{
            if(!($stmt = $this->pdo->prepare($this->sql))){
                throw new ModelExceptions("SQL Error: {$this->sql}", 85006);
            }
            if(!$stmt->execute(array_values($data))){
                throw new ModelExceptions("SQL Param Error: {$this->sql}", 85007);
            }
            $result = (int) $stmt->rowCount();
        }catch(ModelExceptions $e){}
        // Reset Values
        $this->reset();
        return $result; // Returns the ID of the last inserted row
    }

    // Update Data Into Table
    public function update(array $data):int
    {
        $result = 0;
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "$column = ?";
            $params[] = $value;
        }
        $toUpdate = array_merge($params, $this->params);

        $this->sql = "UPDATE {$this->table} SET " . implode(', ', $set);

        if (!empty($this->where)) {
            $this->sql .= ' WHERE ' . implode(" {$this->operator} ", $this->where);

            try{
                if(!($stmt = $this->pdo->prepare($this->sql))){
                    throw new ModelExceptions("SQL Error: {$this->sql}", 85006);
                }
                if(!$stmt->execute($toUpdate)){
                    throw new ModelExceptions("SQL Param Error: {$this->sql}", 85007);
                }
                $result = $stmt->rowCount();
            }catch(ModelExceptions $e){
                echo $e->message();
            }
        }
        // Reset Values
        $this->reset();
        return (int) ($result ?? 0);
    }

    // Delete Column
    public function pop():int
    {
        $result = 0;
        $this->sql = "DELETE FROM {$this->table}";
        if (!empty($this->where)) {
            $this->sql .= ' WHERE ' . implode(" {$this->operator} ", $this->where);
            try{
                if(!($stmt = $this->pdo->prepare($this->sql))){
                    throw new ModelExceptions("SQL Error: {$this->sql}", 85006);
                }
                if(!$stmt->execute($this->params)){
                    throw new ModelExceptions("SQL Param Error: {$this->sql}", 85007);
                }
                $result = (int) $stmt->rowCount();
            }catch(ModelExceptions $e){
                echo $e->message();
            }
        }
        // Reset Values
        $this->reset();
        return $result ?: 0;
    }

    // Generate UUID
    public static function uuid(string $table, string $column)
    {
        $time = substr(str_replace('.', '', microtime(true)), -6);
        $uid = 'uuid-'.bin2hex(random_bytes(3)).'-'.bin2hex(random_bytes(3)).'-'.bin2hex(random_bytes(3)).'-'.bin2hex(random_bytes(3)).'-'.$time;
        // Check Already Exist or Return
        if(self::conn()->table($table)->select()->where([$column => $uid])->single()){
            return self::uuid($table, $column);
        }
        return $uid;
    }
}