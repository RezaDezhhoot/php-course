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

    public static function make() {
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

    public function get()
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

        $sql = sprintf("SELECT * FROM %s %s", $this->table, $where);
        $statment = $this->connection->prepare($sql);

        if ($statment->execute($values)) {
            return $statment->fetchAll(PDO::FETCH_ASSOC);
        } else {
            throw new \Exception("Error while reading " . $this->table . " table");
        }
    }
}
