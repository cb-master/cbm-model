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
namespace CBM\Resource;

use stdClass;

class Model extends Database
{
    // Get Connection
    public static function conn():Null|Object
    {
        return parent::conn();
    }

    // Set Table
    public function table(string $table):object
    {
        $table = parent::setTable($table);
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
        $table = parent::setTable($table);
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
        $sql = "SELECT {$this->select} FROM {$this->table}";
        $result = [];

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->join);
        }

        if (!empty($this->where)) {
            $sql .= ' WHERE ' . implode(" {$this->operator} ", $this->where);
        }

        if (!empty($this->filter)) {
            $sql .= ' WHERE ' . implode(" AND ", $this->filter);
        }
        
        if (!empty($this->order)) {
            $sql .= ' ' . $this->order;
        }

        if (!empty($this->limit)) {
            $sql .= ' ' . $this->limit;
        }
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($this->params);
            $result = $stmt->fetchAll();
        }catch(\PDOException $e){}

        // Reset Values
        $this->reset();

        return $result;
    }

    // Execute Database For Single Value
    public function single():array
    {
        $sql = "SELECT {$this->select} FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->join);
        }

        if (!empty($this->where)) {
            $sql .= ' WHERE ' . implode(" {$this->operator} ", $this->where);
        }

        if (!empty($this->filter)) {
            $sql .= ' WHERE ' . implode(" AND ", $this->filter);
        }

        if (!empty($this->order)) {
            $sql .= ' ' . $this->order;
        }

        if (!empty($this->limit)) {
            $sql .= ' ' . $this->limit;
        }

        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($this->params);
            $result = $stmt->fetch();
        }catch(\PDOException $e){}
        
        // Reset Values
        $this->reset();
        return $result ?: [];
    }

    // Insert Into Database
    public function insert(array $data):int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_values($data));
        }catch(\PDOException $e){}
        // Reset Values
        $this->reset();
        return (int) $this->pdo->lastInsertId(); // Returns the ID of the last inserted row
    }

    // Replace Data
    public function replace($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "REPLACE INTO {$this->table} ($columns) VALUES ($placeholders)";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_values($data));
        }catch(\PDOException $e){}
        // Reset Values
        $this->reset();
        return true; // Returns the ID of the last inserted row
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

        $sql = "UPDATE {$this->table} SET " . implode(', ', $set);

        if (!empty($this->where)) {
            $sql .= ' WHERE ' . implode(" {$this->operator} ", $this->where);

            try{
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($toUpdate);
                $result = $stmt->rowCount();
            }catch(\PDOException $e){}
        }
        // Reset Values
        $this->reset();
        return (int) ($result ?? 0);
    }

    // Delete Column
    public function pop():int
    {
        $res = '';
        $sql = "DELETE FROM {$this->table}";
        if (!empty($this->where)) {
            $sql .= ' WHERE ' . implode(" {$this->operator} ", $this->where);
            try{
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($this->params);
            }catch(\PDOException $e){}
            $res = (int) $stmt->rowCount();
        }
        // Reset Values
        $this->reset();
        return $res ?: 0;
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