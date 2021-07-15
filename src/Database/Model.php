<?php
/*
 * @Created by          : Waris Agung Widodo (ido.alit@gmail.com)
 * @Date                : Sun Jun 27 2021 02:04:14
 * @File name           : Model.php
 *
 * The MIT License (MIT)
 * Copyright (c) 2021 Waris Agung Widodo
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial
 * portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Idoalit\Sinom\Database;

use Idoalit\Sinom\Supports\Text;

/**
 * Class Model
 * @package Idoalit\Sinom\Database
 * @method static Model find($id)
 * @method static Model select($columns)
 * @method static Model join($table, $first, $operator, $second, $type = 'inner')
 * @method static Model where($column, $operator = '=', $value = null)
 * @method static Model isNull($column)
 * @method static Model groupBy($columns)
 * @method static Model orderBy($column, $order = 'asc')
 *
 * @method count($column = '*')
 * @method get()
 * @method first()
 * @method all($columns = [])
 */
abstract class Model extends Query
{
    /**
     * @var \PDO|\mysqli
     */
    protected $connection;
    protected $connection_class;
    protected $table;
    protected $primary_key = 'id';
    protected $key_type = 'int';
    protected $properties = [];

    public function __construct($connection = null, $id = null)
    {
        // set default database connection
        $this->setConnection($connection);

        // set table
        $this->setTable();

        // load initial data if id not null
        if (!is_null($id)) $this->load($id);
    }

    public function __set($name, $value)
    {
        $this->properties[$name] = $value;
    }

    public function __get($name)
    {
        if (isset($this->properties[$name])) return $this->properties[$name];
        return null;
    }

    public function __call($method, $arguments)
    {
        $_method = '_' . $method;
        if (method_exists($this, $_method)) return $this->$_method(...$arguments);
        return null;
    }

    public static function __callStatic($method, $arguments)
    {
        $instance = new static;
        $_method = '_' . $method;
        if (method_exists($instance, $_method)) return $instance->$_method(...$arguments);
        return null;
    }

    private function load($id) {
        $query = $this->connection->query("SELECT * FROM $this->table WHERE $this->primary_key = $id");
        if ($query->rowCount() < 1) return null;
        foreach ($query->fetch(\PDO::FETCH_ASSOC) as $key => $value) {
            $this->properties[$key] = $value;
        }
        return $this;
    }

    public function save()
    {
        if (!is_null($this->{$this->primary_key})) {
            return $this->update();
        }
        return $this->insert();
    }

    public function insert() {
        $column = implode(', ', array_map(function($col){ return "`$col`"; }, array_keys($this->properties)));
        $values = implode(', ', array_map(function($col){ return ":$col"; }, array_keys($this->properties)));

        $exe_arr = [];
        foreach ($this->properties as $key => $value) {
            $exe_arr[':' . $key] = $value;
        }

        $stmt = $this->connection->prepare("insert into `$this->table` ($column) values ($values)");
        $exe = $stmt->execute($exe_arr);
        if($this->debug) $stmt->debugDumpParams();
        return $exe;
    }

    public function update() {
        $set_arr = [];
        foreach ($this->properties as $col => $val) {
            if ($col === $this->primary_key) continue;
            $set_arr[] = "`$col` = :$col";
        }

        $set_str = implode(', ', $set_arr);

        $exe_arr = [];
        foreach ($this->properties as $key => $value) {
            $exe_arr[':' . $key] = $value;
        }

        $stmt = $this->connection->prepare("update `$this->table` set $set_str where `$this->primary_key` = :$this->primary_key");
        $exe = $stmt->execute($exe_arr);
        if($this->debug) $stmt->debugDumpParams();
        return $exe;
    }

    public function delete()
    {
        $stmt = $this->connection->prepare("delete from `$this->table` where `$this->primary_key` = :id");
        $exe = $stmt->execute([':id' => $this->{$this->primary_key}]);
        if($this->debug) $stmt->debugDumpParams();
        return $exe;
    }

    public function toArray()
    {
        return $this->properties;
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param mixed $connection
     */
    public function setConnection($connection): void
    {
        $this->connection = $connection;
        if(is_null($this->connection) && !is_null($this->connection_class)) {
            
            if (!is_array($this->connection_class)) {
                $this->connection_class = [$this->connection_class, 'getInstance'];
            }
            
            $this->connection = call_user_func($this->connection_class);
        }
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed $table
     */
    public function setTable($table = null): void
    {
        if (is_null($this->table)) {
            if (is_null($table)) {
                $table = Text::unCamelCase((new \ReflectionClass($this))->getShortName()) . 's';
            }
            $this->table = $table;
        }
    }

    /**
     * @return string
     */
    public function getPrimaryKey(): string
    {
        return $this->primary_key;
    }

    /**
     * @param string $primary_key
     */
    public function setPrimaryKey(string $primary_key): void
    {
        $this->primary_key = $primary_key;
    }

    /**
     * @return string
     */
    public function getKeyType(): string
    {
        return $this->key_type;
    }

    /**
     * @param string $key_type
     */
    public function setKeyType(string $key_type): void
    {
        $this->key_type = $key_type;
    }
}
