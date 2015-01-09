<?php

function connectionPDO()
{
    try
    {
        $config = include 'config.php';

        if(!isset($config['db']))
        {
            throw new \InvalidArgumentException('A configuração de conexão com o banco não foi encontrada.');
        }

        $host = (isset($config['db']['host']) ? $config['db']['host'] : null);
        //$dbname = (isset($config['db']['dbname']) ? $config['db']['dbname'] : null);
        $username = (isset($config['db']['username']) ? $config['db']['username'] : null);
        $password = (isset($config['db']['password']) ? $config['db']['password'] : null);

        return new \PDO("mysql:host={$host}", $username, $password);

    } catch(\PDOException $e) {
        echo $e->getMessage()."\n";
        echo $e->getTraceAsString()."\n";

        return false;
    }
}