<?php

namespace Store\Database;

use PDO;

class DB
{
    protected $config, $connection;

    private $wheres = [], $table;

    public function __construct()
    {
        $this->config = config("db");
        $this->newConnection();
    }

    public static function make()
    {
        return new static();
    }

    private function newConnection()
    {
        $data = $this->config['drivers'][$this->config['default']];

        $dns = sprintf("mysql:host=%s;dbname=%s", $data['host'], $data['db']);
        $this->connection = new PDO($dns,    $data['user'], $data['password']);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function where($column, $oprator, $value): static
    {
        $this->wheres[] = [
            'column' => $column,
            'operator' => $oprator,
            'value' => $value
        ];
        return $this;
    }

    public function table($table): static
    {
        $this->table = $table;
        return $this;
    }

    private function prepareWhere(): array
    {
        $where = '';
        $values = [];
        if (sizeof($this->wheres) > 0) {
            $where .= "WHERE ";
            foreach ($this->wheres as $w) {
                $where .= sprintf("%s %s ?", $w['column'], $w['operator']);
                $values[] = $w['value'];
            }
        }

        return [$where, $values];
    }

    public function count(): int
    {
        [$where, $values] = $this->prepareWhere();
        $func = "COUNT(id)";
        $sql = sprintf("SELECT $func FROM %s %s", $this->table, $where);
        $statment = $this->connection->prepare($sql);
        if ($statment->execute($values)) {
            return $statment->fetch(PDO::FETCH_ASSOC)[$func];
        } else {
            throw new \Exception("Error while reading " . $this->table . " table");
        }
    }

    public function get(): array
    {
        [$where, $values] = $this->prepareWhere();
        $sql = sprintf("SELECT * FROM %s %s ORDER BY id DESC", $this->table, $where);
        $statment = $this->connection->prepare($sql);

        if ($statment->execute($values)) {
            return $statment->fetchAll(PDO::FETCH_ASSOC);
        } else {
            throw new \Exception("Error while reading " . $this->table . " table");
        }
    }

    public function first(): array
    {
        [$where, $values] = $this->prepareWhere();
        $sql = sprintf("SELECT * FROM %s %s ORDER BY id DESC", $this->table, $where);
        $statment = $this->connection->prepare($sql);

        if ($statment->execute($values)) {
            return $statment->fetch(PDO::FETCH_ASSOC);
        } else {
            throw new \Exception("Error while reading " . $this->table . " table");
        }
    }

    public function create($data)
    {
        $columns = array_keys($data);
        $marks = [];
        foreach ($data as $k => $val) {
            $marks[] = "?";
        }
        $sql = sprintf("INSERT INTO %s (%s) VALUE (%s)", $this->table, implode(',', $columns), implode(',', $marks));
        $statment = $this->connection->prepare($sql);
        if ($res = $statment->execute(array_values($data))) {
            return $res;
        } else {
            throw new \Exception("Error while reading " . $this->table . " table");
        }
    }

    public function delete()
    {
        [$where, $values] = $this->prepareWhere();
        $sql = sprintf("DELETE FROM %s %s", $this->table, $where);
        $statment = $this->connection->prepare($sql);

        if ($res = $statment->execute($values)) {
            return $res;
        } else {
            throw new \Exception("Error while reading " . $this->table . " table");
        }
    }

    public function update($data)
    {
        [$where, $values] = $this->prepareWhere();
        $marks = [];
        foreach ($data as $k => $val) {
            $marks[] = "$k = ?";
        }
        $sql = sprintf("UPDATE %s SET %s %s", $this->table, implode(',', $marks), $where);
        $statment = $this->connection->prepare($sql);
        if ($res = $statment->execute(array_merge(array_values($data), $values))) {
            return $res;
        } else {
            throw new \Exception("Error while reading " . $this->table . " table");
        }
    }
}
