<?php

namespace app\model;

use \app\Application as App;
use app\exception\DatabaseException;
use app\exception\Exception;

class Model
{

    protected $pdo;

    public function __construct()
    {
        $this->pdo = App::$app->db();
    }

    protected function query(string $query, array $params)
    {
        $sth = $this->pdo->prepare($query);
        if ($sth->execute($params)) {
            $result = $sth->fetchAll();
        }

        if ((int)$this->pdo->errorCode() > 0) {
            $ei = $this->pdo->errorInfo();
            throw new DatabaseException($ei[2]);
        }

        if ((int)$sth->errorCode() > 0) {
            $ei = $sth->errorInfo();
            throw new DatabaseException($ei[2]);
        }

        return $result;
    }

    public function getAt(string $table, string $column, string $value)
    {
        $query = "SELECT * FROM $table WHERE $column = :value";
        return $this->query($query, ['value' => $value]);

    }

    public function load(array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function loadById(int $id)
    {
        $this->load($this->getAt($this->table, 'id', $id)[0]);
    }

    public function filter($value)
    {
        $value = strip_tags($value);
        $value = htmlentities($value, ENT_QUOTES, 'UTF-8');
        $value = htmlspecialchars($value, ENT_QUOTES);
        return $value;
    }

    public function __set($property, $value)
    {
        $fnName = 'set' . ucfirst($property);
        if (method_exists($this, $fnName)) {
            return $this->$fnName($value);
        } else {
            $this->$property = $value;
            return $this->$property;
        }
    }

    public function __get($property)
    {
        $fnName = 'get' . ucfirst($property);
        if (method_exists($this, $fnName)) {
            return $this->$fnName();
        } else {
            return $this->$property;
        }
    }
}