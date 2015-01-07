<?php

namespace AG\Produto\Mapper;

use AG\Produto\Entity\Produto;

class ProdutoMapper
{
    private $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function insert(Produto $produto)
    {
        $sql = "INSERT INTO `produtos`(`nome`, `descricao`, `valor`) VALUES (:nome, :descricao, :valor);";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':nome', $produto->getNome());
        $stmt->bindValue(':descricao', $produto->getDescricao());
        $stmt->bindValue(':valor', $produto->getValor());

        return $stmt->execute() ? true : false;
    }

    public function update(Produto $produto)
    {
        $sql = "UPDATE `produtos` SET `nome`= :nome,
               `descricao`= :descricao,
               `valor`= :valor
                WHERE `id`= :id;";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':nome', $produto->getNome());
        $stmt->bindValue(':descricao', $produto->getDescricao());
        $stmt->bindValue(':valor',$produto->getValor());
        $stmt->bindValue(':id', $produto->getId());

        return $stmt->execute() ? true : false;
    }

    public function delete($id)
    {
        $sql = "DELETE FROM `produtos` WHERE `id` = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id);

        return $stmt->execute() ? true : false;
    }


    public function fetchAll()
    {
        $sql = "SELECT * FROM `produtos`;";
        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function fetch($id)
    {
        $sql = "SELECT * FROM `produtos` WHERE `id`=:id;";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}