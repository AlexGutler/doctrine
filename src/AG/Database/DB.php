<?php
namespace AG\Database;

class DB
{
    /**
     * PDO
     * @var \PDO
     */
    private $pdo;

    /**
     * @param string $dsn
     * @param string $dbname
     * @param string $user
     * @param string $pass
     */
    public function __construct($dsn, $dbname, $username, $password)
    {
        if (!isset($dbname)){

        }
        $this->pdo = new \PDO($dsn, $username, $password, array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));
        $this->pdo->exec("CREATE DATABASE IF NOT EXISTS {$dbname} CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
        $this->pdo->exec("USE {$dbname};");
    }

    /**
     * Gets PDO Connection
     * @return \PDO
     */
    public function getConnection()
    {
        return $this->pdo;
    }
}