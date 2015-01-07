<?php

namespace AG\Database;


class DB
{
    private $pdo;

    public function __construct($dsn, $username, $password)
    {
        try {
            $this->pdo = new \PDO($dsn, $username, $password);
        } catch(\PDOException $e) {
            echo $e->getMessage()."\n";
            echo $e->getTraceAsString()."\n";

            die;
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}