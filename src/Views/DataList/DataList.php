<?php
namespace Idoalit\Sinom\Views\DataList;

trait DataList {
    private $modifyColumns = [];
    private $modifyHeaders = [];
    private $options;

    function _datalist(Options $options = null)
    {
        $this->options = $options ?? new Options;
        return $this->buildTable();
    }

    function _modify($column, $callback)
    {
        $this->modifyColumns[$column] = $callback;
        return $this;
    }

    function _modifyHeader($header, $callback)
    {
        $this->modifyHeaders[$header] = $callback;
        return $this;
    }

    private function buildTableHeader() {
        $columns = $this->getColumns();
        if (is_null($columns) || $columns === '*') {
            $columns = [];
            $query_column = $this->connection->query("SHOW COLUMNS FROM $this->table");
            while ($row = $query_column->fetchObject()) {
                $columns[] = $row->Field;
            }
        }
        $header  = '<tr class="'.$this->options->header_class.'" '.$this->options->header_attr.'>';
        foreach ($columns as $column => $alias) {
            $header .= '<th>';
            if (isset($this->modifyHeaders[$alias])) {
                $callback = $this->modifyHeaders[$alias];
                if (is_callable($callback)) {
                    $header .= call_user_func_array($callback, [$column, $alias]);
                } else {
                    $header .= $callback;
                }
            } else {
                $header .= $alias;
            }
            $header .= '</th>';
        }
        $header .= '</tr>';
        return $header;
    }

    private function buildTableRow() {
        $row  = '';
        foreach ($this->_get() as $object) {
            $row .= '<tr class="'.$this->options->row_class.'" '.$this->options->row_attr.'>';
            foreach($object->toArray() as $key => $item) {
                $row .= '<td>';
                if (isset($this->modifyColumns[$key])) {
                    $callback = $this->modifyColumns[$key];
                    if (is_callable($callback)) {
                        $row .= call_user_func_array($callback, [$item, $object]);
                    } else {
                        $row .= $callback;
                    }
                } else {
                    $row .= $item;
                }
                $row .= '</td>';
            }
            $row .= '</tr>';
        }
        return $row;
    }

    private function buildTable() {
        $table  = '<table class="'.$this->options->table_class.'" '.$this->options->table_attr.'>';
        $table .= '<thead>';
        $table .= $this->buildTableHeader();
        $table .= '</thead>';
        $table .= '<tbody>';
        $table .= $this->buildTableRow();
        $table .= '</tbody>';
        $table .= '</table>';
        return $table;
    }
}