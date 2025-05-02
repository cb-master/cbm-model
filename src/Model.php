<?php

namespace CBM\Model;

abstract class Model
{
    // Table Name
    protected string $table;

    // Table ID
    protected string $id;

    // Database Connection Name
    protected string $name;

    public function __construct(string $name = 'default')
    {
        $this->name = $name;
    }

    /**
     * @param array $where Optional parameter. Default is []
     * @param string $operator Optional parameter. Default is '='
     * @param string $compare Optional parameter. Default is 'AND'
     * @return array
     */
    public function all(array $where = [], string $operator = '=', string $compare = 'AND'):array
    {
        $db = DB::getInstance($this->name)->table($this->table);
        return $where ? $db->where($where, $operator, compare:$compare)->get() : $db->get();
    }

    /**
     * @param array $where Optional parameter. Default is []
     * @param string $operator Optional parameter. Default is '='
     * @param string $compare Optional parameter. Default is 'OR'
     * @return array
     */
    public function find(array $where = [], string $operator = '=', string $compare = 'OR'):array
    {
        return DB::getInstance($this->name)->table($this->table)->where($where, $operator, compare:$compare)->get();
    }

    /**
     * @param string $column Required parameter.
     * @param int|string $value Required parameter.
     * @return array
     */
    public function first(int|string $value):array
    {
        return DB::getInstance($this->name)->table($this->table)->where($this->id, '=', value:$value)->first();
    }

    /**
     * @param array $where Required parameter.
     * @param string $operator Optional parameter. Default is '='
     * @param string $compare Optional parameter. Default is 'AND'
     * @return int
     */
    public function delete(array $where, string $operator = '=', string $compare = 'AND'):int
    {
        return DB::getInstance($this->name)->table($this->table)->where($where, $operator, compare:$compare)->delete();
    }

    /**
     * @param array $data Required parameter.
     * @return int
     */
    public function create(array $data):int
    {
        return DB::getInstance($this->name)->table($this->table)->insert($data);
    }

    /**
     * @param array $rows Required parameter.
     * @return int
     */
    public function createMany(array $rows):bool
    {
        return DB::getInstance($this->name)->table($this->table)->insertMany($rows);
    }

    /**
     * @param array $where Required parameter.
     * @param array $data Required parameter.
     * @return int
     */
    public function update(array $where, array $data):int
    {
        return DB::getInstance($this->name)->table($this->table)->where($where)->update($data);
    }
}