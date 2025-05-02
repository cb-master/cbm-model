<?php

namespace CBM\Model;

use PDO;

abstract class Model
{
    protected static PDO $pdo;
    protected static string $table;
    protected static string $id;
    protected static string $name = 'default';

    public function __construct(?string $name = null)
    {
        self::setPDO($name);
    }

    /**
     * @param PDO $pdo Required parameter.
     * @return void
     */
    public static function setPDO(?string $name = null):void
    {
        self::$name = $name ?: self::$name;
        static::$pdo = ConnectionManager::get(self::$name);
    }

    /**
     * @param array $where Optional parameter. Default is []
     * @param string $operator Optional parameter. Default is '='
     * @param string $compare Optional parameter. Default is 'AND'
     * @return array
     */
    public static function all(array $where = [], string $operator = '=', string $compare = 'AND'):array
    {
        $db = DB::getInstance(static::$pdo)->table(static::$table);
        return $where ? $db->where($where, $operator, compare:$compare)->get() : $db->get();
    }

    /**
     * @param array $where Optional parameter. Default is []
     * @param string $operator Optional parameter. Default is '='
     * @param string $compare Optional parameter. Default is 'OR'
     * @return array
     */
    public static function find(array $where = [], string $operator = '=', string $compare = 'OR'):array
    {
        return DB::getInstance(static::$pdo)->table(static::$table)->where($where, $operator, compare:$compare)->get();
    }

    /**
     * @param string $column Required parameter.
     * @param int|string $value Required parameter.
     * @return array
     */
    public static function first(int|string $value):array
    {
        return DB::getInstance(static::$pdo)->table(static::$table)->where(self::$id, '=', value:$value)->first();
    }

    /**
     * @param array $where Required parameter.
     * @param string $operator Optional parameter. Default is '='
     * @param string $compare Optional parameter. Default is 'AND'
     * @return int
     */
    public static function delete(array $where, string $operator = '=', string $compare = 'AND'):int
    {
        return DB::getInstance(static::$pdo)->table(static::$table)->where($where, $operator, compare:$compare)->delete();
    }

    /**
     * @param array $data Required parameter.
     * @return int
     */
    public static function create(array $data):int
    {
        return DB::getInstance(static::$pdo)->table(static::$table)->insert($data);
    }

    /**
     * @param array $rows Required parameter.
     * @return int
     */
    public static function createMany(array $rows):bool
    {
        return DB::getInstance(static::$pdo)->table(static::$table)->insertMany($rows);
    }

    /**
     * @param array $where Required parameter.
     * @param array $data Required parameter.
     * @return int
     */
    public static function update(array $where, array $data):int
    {
        return DB::getInstance(static::$pdo)->table(static::$table)->where($where)->update($data);
    }
}