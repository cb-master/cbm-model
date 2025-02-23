<?php
/**
 * Project: Cloud Bill Master Database Model
 * Author Name: Showket Ahmed
 * Author Email: riyadhtayf@gmail.com
 */

// Namespace
namespace CBM\Model;

use Exception;
use Throwable;

class Model extends Database
{
    // Set Table
    /**
     * @param string $table - Required Argument
     */
    public static function table(string $table):object
    {
        self::instance()->table = $table;
        return self::$instance;
    }

    ############################
    ###### CRUD FUNCTIONS ######
    ############################

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
     * @param string $operator - Required Argument. Example '=', 'IN', '>', '<'
     * @param int|string $value - Required Argument.
     * @param ?string $compare - Default is null. Example 'AND', 'IN'
     */
    public function filter(string $column, string $operator, int|string $value, ?string $compare = null):object
    {
        $compare = $compare ? strtoupper($compare) : $compare;
        $this->where .= $compare ? "{$column} {$operator} ? {$compare} " : "{$column} {$operator} ? ";
        $this->params = array_merge($this->params, [$value]);
        return $this;
    }

    // Set Where
    /**
     * @param string $column - Required Argument
     * @param int|string $min - Required Argument
     * @param int|string $max - Required Argument
     * @param ?string $compare - Default is 'AND'
     */
    public function between(string $column, int|string $min, int|string $max, string $compare = 'AND'):object
    {
        $compare = strtoupper($compare);
        $this->where .= "{$column} BETWEEN ? AND ? {$compare} ";
        $this->params = array_merge($this->params, [$min, $max]);
        return $this;
    }

    // Set Where
    /**
     * @param string $column - Required Argument
     * @param int|string $min - Required Argument
     * @param int|string $max - Required Argument
     * @param ?string $compare - Default is 'AND
     */
    public function not():object
    {
        $this->not = "NOT";
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
        $compare = strtoupper($compare);
        foreach($where as $key=>$value){
            $this->where .= "{$key} {$operator} ? {$compare} ";
            $this->params = array_merge($this->params, [$value]);
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
        $this->offset = "OFFSET {$this->offset}";
        return $this;
    }

    // Get All From Database
    /**
     * @param string $columns - Default is '*'
     */
    public function get(string $columns = '*'):array
    {
        try{
            // Prepare Statement
            $stmt = self::pdo()->prepare("SELECT {$columns} FROM {$this->makeSelectQuery()}");
            
            // Execute Statement
            $stmt->execute($this->params);

            // Fetch Data
            $result = $stmt->fetchAll();
        }catch(Throwable $th) {
            throw $th;
        }
        
        // Reset Statemment Helpers
        $this->reset();
        
        // Return
        return $result ?? [];
    }

    // Get Single Value From Database
    /**
     * @param string $columns - Default is '*'
     */
    public function single(string $columns = '*'):object|array
    {     
        try{
            // Prepare Statement
            $stmt = self::pdo()->prepare("SELECT {$columns} FROM {$this->makeSelectQuery()}");
            // Execute Statement
            $stmt->execute($this->params);
            // Fetch Data
            $result = $stmt->fetch();
        }catch(Throwable $th) {
            throw $th;
        }
        
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
        $this->columns = array_keys($data);
        $this->placeholders = implode(', ', array_fill(0, count($data), '?'));

        try{
            // Prepare Statement
            $stmt = $this->pdo->prepare("INSERT INTO {$this->makeInsertQuery()}");
            // Execute Statement
            $stmt->execute(array_values($data));

            // Get Last Insert ID
            $result = (int) $this->pdo->lastInsertId();
        }catch(Throwable $e){
            throw $e;
        }
        // Reset Statemment Helpers
        $this->reset();
        // Return
        return $result ?? 0;
    }

    // Replace Data
    /**
     * @param array $data - Required Argument as Associative Array
     */
    public function replace(array $data):int
    {
        // Make Query
        $this->columns = array_keys($data);
        $this->placeholders = implode(', ', array_fill(0, count($data), '?'));

        try{
            // Prepare Statement
            $stmt = $this->pdo->prepare("REPLACE INTO {$this->makeInsertQuery()}");

            // Execute Statement
            $stmt->execute(array_values($data));

            // Count Effected Rows
            $result = (int) $stmt->rowCount();
        } catch (Throwable $th) {
            throw $th;
        }

        // Reset Statemment Helpers
        $this->reset();
        
        // Return
        return $result ?? 0;
    }

    // Update Data Into Table
    /**
     * @param array $data - Required Argument as Associative Array
     */
    public function update(array $data):int
    {
        // Get Params
        foreach ($data as $column => $value) {
            $this->columns[] = "{$column} = ?";
            $params[] = $value;
        }

        // Get Params
        $params = array_merge($params, $this->params);

        try{
            // Prepare Statement
            $stmt = $this->pdo->prepare("UPDATE {$this->makeUpdateQuery()}");
            // Execute Statement
            $stmt->execute($params);
            // Get Result
            $result = (int) $stmt->rowCount();
        }catch(Throwable $th) {
            throw $th;
        }
        // Reset Statemment Helpers
        $this->reset();
        // Return
        return $result ?? 0;
    }

    // Delete Column
    public function pop():int
    {
        try{
            // Prepare Statement
            $stmt = $this->pdo->prepare("DELETE FROM {$this->makePopQuery()}");

            // Execute Statement
            $stmt->execute($this->params);

            // Get Result
            $result = (int) $stmt->rowCount();
        }catch(Throwable $th) {
            throw $th;
        }

        // Reset Statemment Helpers
        $this->reset();

        // Return
        return $result ?? 0;
    }

    // Generate UUID
    public function uuid()
    {
        $time = substr(str_replace('.', '', microtime(true)), -6);
        $uid = 'uuid-'.bin2hex(random_bytes(3)).'-'.bin2hex(random_bytes(3)).'-'.bin2hex(random_bytes(3)).'-'.bin2hex(random_bytes(3)).'-'.$time;
        // Check Already Exist & Return
        if(self::table(self::$instance->table)->filter('uuid', '=', $uid)->single()){
            return self::uuid();
        }
        return strtoupper($uid);
    }

    // Execute Custom Query 
    /**
     * @param string $query - Required Argument as Custom Query
     * @param array $params - Optional Arguments if Query has named arguments
     */
    public static function execute(string $query, array $params = []):int|array|object
    {
        try{
            // Prepare Statement
            $stmt = self::pdo()->prepare($query);

            // Execute Statement
            $stmt->execute($params);
            if(preg_match("/select /i", $query)){
                return $stmt->fetchAll();
            }elseif(preg_match("/insert /i", $query)){
                return (int) self::pdo()->lastInsertId();
            }
            return (int) $stmt->rowCount();
        }catch(Throwable $th) {
            throw $th;
        }
        return [];
    }

    #############################
    ###### TABLE FUNCTIONS ######
    #############################

    // Add a Column Definition
    /**
     * @param string $name - Required Argument like 'table_column_name'
     * @param string $type - Required Argument like 'int(10) unsigned not null auto_increment ' or 'varchar(255)' or 'longtext'
     */
    public function column(string $name, string $type):object
    {
        $column = "`{$name}` {$type}";
        $this->columns[] = $column;
        return $this;
    }

    // Set Primary Key
    /**
     * @param string $column - Required Argument like 'table_column_name'
     */
    public function primary(string $column):object
    {
        if($this->primary){
            throw new Exception("Multiple Primary Key is Not Allowed", 85010);
        }
        $this->primary = $column;
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
        // Check Table & Columns are Exist
        if (!$this->table || !$this->columns) {
            throw new Exception("Table Name & Columns Must Be Defined.", 85006);
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
        if (!$this->table) {
            throw new Exception("Table Name Must Be Defined.", 85006);
        }

        $this->sql = "SHOW TABLES";

        // Prepare Statement
        try{
            $stmt = self::pdo()->prepare($this->sql);
            $stmt->execute();

            // Execute Statement
            $result = $stmt->fetchAll();

            $result = json_decode(json_encode($result), true);

            foreach($result as $res){
                if(in_array($this->table, $res)){
                    return true;
                }
            }
        }catch(Throwable $th) {
            throw $th;
        }
        
        // Reset Values
        $this->reset();
        return false;
    }
}