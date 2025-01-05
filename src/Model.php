<?php
/**
 * Project: Laika MVC Framework
 * Author Name: Showket Ahmed
 * Author Email: riyadhtayf@gmail.com
 */

// Namespace
namespace CBM\Model;

use PDOException;
use CBM\ModelHelper\ModelExceptions;

class Model extends Database
{
    // Set Table
    /**
     * @param string $table - Required Argument
     */
    public static function table(string $table):object
    {
        self::conn()->table = $table;
        return self::$instance;
    }

    ############################
    ###### CRUD FUNCTIONS ######
    ############################

    // Set Slect Columns
    /**
     * @param string $columns - Default is '*'
     */
    public function select(string $columns = '*'):object
    {
        $this->action = 'select';
        $this->select = $columns;
        return $this;
    }

    // Set Group By
    /**
     * @param string $columns - Required Argument
     */
    public function group(string $columns)
    {
        $this->group = $columns;
        return $this;
    }

    // Set Having
    /**
     * @param string $column - Required Argument
     */
    public function having(string $column)
    {
        $this->having = $column;
        return $this;
    }

    // Set Join
    /**
     * @param string $table - Required Argument
     * @param string $condition - Required Argument
     * @param string $type - Default is 'LEFT'
     */
    public function join(string $table, string $condition, string $type = 'LEFT'):object
    {
        $type = strtoupper($type);
        $this->join[] = "{$type} JOIN {$table} ON {$condition}";
        return $this;
    }

    // Set Where
    /**
     * @param string $column - Required Argument
     * @param string $operator - Required Argument. Example '=', 'ON', '>', '<'
     * @param int|string $value - Required Argument.
     * @param ?string $compare - Default is null. Example 'AND', 'OR'
     */
    public function filter(string $column, string $operator, Int|String $value, ?String $compare = null):object
    {
        $this->filter[] = "{$column} {$operator} ?" . ($compare ? " {$compare}": "");
        $this->params[] = $value;
        return $this;
    }

    // Set Where
    /**
     * @param string $column - Required Argument
     * @param int|string $min - Required Argument
     * @param int|string $max - Required Argument
     * @param ?string $compare - Default is null
     */
    public function between(string $column, int|string $min, int|string $max, ?string $compare = null):object
    {
        $this->between[] = "{$column} BETWEEN ? AND ?" . ($compare ? " {$compare}": "");
        $this->params = array_merge($this->params, [$min, $max]);
        return $this;
    }

    // Set Where
    /**
     * @param string $column - Required Argument
     * @param int|string $min - Required Argument
     * @param int|string $max - Required Argument
     * @param ?string $compare - Default is null
     */
    public function notin(string $column, int|string $min, int|string $max, ?string $compare = null):object
    {
        $this->between[] = "{$column} NOT BETWEEN ? AND ?" . ($compare ? " {$compare}": "");
        $this->params = array_merge($this->params, [$min, $max]);
        return $this;
    }

    // Set Where
    /**
     * @param array $where - Required Argument
     * @param string $operator - Default is '='
     * @param string $compare - Default is 'AND'
     */
    public function where(array $where, string $operator = '=', string $compare = 'AND'):object
    {
        foreach($where as $key=>$value){
            $this->where[] = "{$key} {$operator} ?" . ($compare ? " {$compare}": "");
            $this->params[] = array_merge($this->params, [$value]);
        }
        return $this;
    }

    // Set Order
    /**
     * @param string $column - Required Argument
     * @param string $ascending - Default is true for Ascending. Use false for Descending
     */
    public function order(string $column, bool $ascending = true):object
    {
        $direction = $ascending ? 'ASC' : 'DESC';
        $this->order = "ORDER BY {$column} {$direction}";
        return $this;
    }

    // Set Limit
    /**
     * @param int|string $limit - Default is 20
     */
    public function limit(int|string $limit = 20):object
    {
        // $limit = (int) $limit;
        // Set Query
        $this->limit = "LIMIT {$limit}";
        return $this;
    }

    // Set Offset
    /**
     * @param int|string $offset - Default is 0
     */
    public function offset(int|string $offset = 0):object
    {
        $offset = (int) $offset;
        $this->offset =  ($offset > 0) ? $offset : 0;

        // Set Query
        $this->limit = "OFFSET {$this->offset}";
        return $this;
    }

    // Execute Database
    public function get():array
    {
        // Make Query
        $sql = $this->makeQuery();

        // Prepare Statement
        $stmt = $this->pdo->prepare($sql);
        // Execute Statement
        $stmt->execute($this->params);
        // Fetch Data
        $result = $stmt->fetchAll();

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
      
        // Prepare Statement
        $stmt = $this->pdo->prepare($sql);
        // Execute Statement
        $stmt->execute($this->params);
        // Fetch Data
        $result = $stmt->fetch();
        
        // Reset Statemment Helpers
        $this->reset();

        // Return
        return $result ?: [];
    }

    // Insert Into Database
    /**
     * @param array $data - Required Argument as Associative Array
     */
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
    /**
     * @param array $data - Required Argument as Associative Array
     */
    public function replace(array $data):int
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
    /**
     * @param array $data - Required Argument as Associative Array
     */
    public function update(array $data):int
    {
        $this->action = 'update';
        // Get Params
        foreach ($data as $column => $value) {
            $this->columns[] = "{$column} = ?";
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
    public function uuid()
    {
        $time = substr(str_replace('.', '', microtime(true)), -6);
        $uid = 'uuid-'.bin2hex(random_bytes(3)).'-'.bin2hex(random_bytes(3)).'-'.bin2hex(random_bytes(3)).'-'.bin2hex(random_bytes(3)).'-'.$time;
        // Check Already Exist & Return
        if(self::table(self::$instance->table)->select()->filter('uuid', '=', $uid)->single()){
            return self::uuid();
        }
        return strtoupper($uid);
    }

    // Execute Custom Query 
    /**
     * @param string $query - Required Argument as Custom Query
     * @param array $params - Optional Arguments if Query has named arguments
     */
    public static function execute(string $query, array $params = []):int|array
    {
        // Prepare Statement
        $stmt = self::conn()->pdo->prepare($query);
        // Execute Statement
        $stmt->execute($params);
        return str_contains($query, "SELECT ") || str_contains($query, "select ") ? $stmt->fetchAll() : $stmt->rowCount();
    }

    #############################
    ###### TABLE FUNCTIONS ######
    #############################

    // Add a Column Definition
    /**
     * @param string $name - Required Argument like 'table_column_name'
     * @param string $type - Required Argument like 'int(10)' or 'varchar(255)' or 'longtext'
     * @param bool $null - Default Value is false. Use true For Defined as Null Column
     * @param bool $autoIncrement - Default Value is false. Use true For Auto Increment ID
     * @param ?string $default - Default Value is null. Use 'Any Value' to Set Default Value
     */
    public function column(
        string $name,
        string $type,
        bool $null = false,
        bool $autoIncrement = false,
        ?string $default = null
        ):object
    {
        $column = "`{$name}` {$type}";
        $column .= $null ? "" : " NOT NULL";
        $column .= $autoIncrement ? " AUTO_INCREMENT" : '';
        
        if($default){
            if(str_contains($default, 'timestamp')){
                $column .= $default ? " DEFAULT {$default}" : '';
            }else{
                $column .= $default ? " DEFAULT '{$default}'" : '';
            }
        }
        // $column .= $default ? " DEFAULT {$default}" : '';
        $this->columns[] = $column;
        return $this;
    }

    // Set Primary Key
    /**
     * @param string $column - Required Argument like 'table_column_name'
     */
    public function primary(string $column):object
    {
        try {
            if(!$this->primary){
                $this->primary = $column;
            }else{
                throw new ModelExceptions("Multiple Primary Key is Not Allowed", 85010);
            }
        } catch (ModelExceptions $e) {
            echo $e->message();
        }
        return $this;
    }

    // Set Unique Key
    /**
     * @param string $column - Required Argument like 'table_column_name'
     */
    public function unique(string $column):object
    {
        $this->unique[] = "UNIQUE({$column})";
        return $this;
    }

    // Set Index Key
    /**
     * @param string $column - Required Argument like 'table_column_name'
     */
    public function index(string $column):object
    {
        $this->index[] = "KEY({$column})";
        return $this;
    }

    // Set Fulltext Key
    /**
     * @param string $column - Required Argument like 'table_column_name'
     */
    public function fulltext(string $column):object
    {
        $this->fulltext[] = "FULLTEXT({$column})";
        return $this;
    }

    // Set Engine
    /**
     * @param ?string $engine - Default Value is null for 'InnoDB'
     */
    public function engine(?string $engine = null):object
    {
        $this->engine = $engine ?: $this->engine;
        return $this;
    }

    // Set Charset
    /**
     * @param ?string $charset - Default Value is null for 'utf8mb4'
     */
    public function charset(?string $charset = null):object
    {
        $this->charset = $charset ?: $this->charset;
        return $this;
    }

    // Set Collate
    /**
     * @param ?string $collate - Default Value is null for 'utf8mb4_general_ci'
     */
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