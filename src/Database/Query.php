<?php
/*
 * @Created by          : Waris Agung Widodo (ido.alit@gmail.com)
 * @Date                : Sun Jun 27 2021 02:09:01
 * @File name           : Query.php
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

class Query
{
    private $columns;
    private $join;
    private $where;
    private $where_value = [];
    private $order;
    private $limit = 10;
    private $offset = 0;
    private $sql;
    private $rows;

    function _select($columns)
    {
        if (is_array($columns)) {
            $this->columns = $columns;
            return $this;
        }
        $this->columns = func_get_args();
        return $this;
    }

    function _join($table, $first, $operator, $second, $type = 'inner')
    {
        $this->join[] = [
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second,
            'type' => $type
        ];
        return $this;
    }

    function _leftJoin($table, $first, $operator, $second)
    {
        return $this->_join($table, $first, $operator, $second, 'left');
    }

    function _rightJoin($table, $first, $operator, $second)
    {
        return $this->_join($table, $first, $operator, $second, 'right');
    }

    function _where($column, $operator = '=', $value = null)
    {
        $this->where['and'][] = [$column, $operator, $value];
        return $this;
    }

    function _whereOr($column, $operator = '=', $value = null)
    {
        $this->where['or'][] = [$column, $operator, $value];
        return $this;
    }

    function _orderBy($column, $order = 'asc')
    {
        $this->order[] = [$column, $order];
        return $this;
    }

    function _limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    function _offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    function _get()
    {
        // build sql first
        $this->build();
        // execute query
        $sth = $this->execute();
        // get rows
        while ($row = $sth->fetchObject(get_class($this))) {
            if (is_null($this->rows)) $this->rows = [];
            $this->rows[] = $row;
        }

        return $this->rows;
    }

    function _first()
    {
        // set limit just one
        $this->limit = 1;
        // build sql first
        $this->build();
        // execute query
        $sth = $this->execute();
        return $sth->fetchObject(get_class($this));
    }

    function _find($id)
    {
        // select by primary key
        $this->_where($this->primary_key, '=', $id);
        // get first
        return $this->_first();
    }

    function _all($columns = [])
    {
        return $this->_get($columns);
    }

    function _sql() {
        // build sql
        $this->build();
        // return it
        return $this->sql;
    }

    private function execute()
    {
        // prepare statement
        $sth = $this->connection->prepare($this->sql);
        // bind parameter
        foreach ($this->where_value as $key => $value) {
            $sth->bindValue($key + 1, $value);
        }
        // execute sql
        $sth->execute();
        return $sth;
    }

    private function build()
    {
        $this->sql = 'select ' . $this->buildColumn();
        $this->sql .= ' from `' . $this->table . '` ';
        $this->sql .= $this->buildJoin();
        if (($where = $this->buildWhere()) !== '') $this->sql .= ' where ' . $where;
        if (($order = $this->buildOrder()) !== '') $this->sql .= ' order by ' . $order;
        $this->sql .= ' limit ' . $this->limit;
        $this->sql .= ' offset ' . $this->offset;

    }

    private function buildColumn()
    {
        if (is_null($this->columns) || empty($this->columns)) return '*';
        if ($this->columns[0] === '*') return '*';
        return implode(', ', array_map(function ($item, $key) {
            $item = str_replace('`', '', $item);
            if (is_int($key)) return '`' . $item . '`';
            $key = str_replace('`', '', $key);
            return '`' . $key . '` AS `' . $item . '`';
        }, $this->columns, array_keys($this->columns)));
    }

    private function buildJoin()
    {
        $join_str = '';
        if (!is_null($this->join)) {
            foreach ($this->join as $join) {
                $first = implode('.', array_map(function($item){ return "`{$item}`"; }, explode('.', $join['first'])));
                $second = implode('.', array_map(function($item){ return "`{$item}`"; }, explode('.', $join['second'])));
                $join_str .= "{$join['type']} join `{$join['table']}` on {$first} {$join['operator']} {$second} ";
            }
        }
        return $join_str;
    }

    private function buildWhere()
    {
        $where = '';
        foreach ($this->where as $key => $item) {
            $sparator = ' ' . $key . ' ';
            if ($where !== '') $where .= $sparator;
            $where .= implode($sparator, array_map(function ($where) {
                $this->where_value[] = $where[2];
                return '`' . $where[0] . '` ' . $where[1] . ' ?';
            }, $item));
        }

        return $where;
    }

    private function buildOrder()
    {
        if (is_null($this->order) || empty($this->order)) return '';
        return implode(', ', array_map(function ($item) {
            return $item[0] . ' ' . $item[1];
        }, $this->order));
    }
}
