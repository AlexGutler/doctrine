<?php

namespace AG\Produto\Mapper;

use AG\Produto\Entity\Produto;
use Doctrine\ORM\EntityManager;

class ProdutoMapper
{
    //private $conn;
    private $em;

    public function __construct(/*\PDO $conn,*/ EntityManager $em)
    {
        //$this->conn = $conn;
        $this->em = $em;
    }

    public function insert(Produto $produto)
    {
        /* $sql = "INSERT INTO `produtos`(`nome`, `descricao`, `valor`) VALUES (:nome, :descricao, :valor);";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':nome', $produto->getNome());
        $stmt->bindValue(':descricao', $produto->getDescricao());
        $stmt->bindValue(':valor', $produto->getValor());

        return $stmt->execute() ? true : false; */

        $this->em->persist($produto);
        $this->em->flush();

        return $produto;
    }

    public function update(Produto $produto)
    {
        $this->em->find('produtos', $produto->getId());
        /*$sql = "UPDATE `produtos` SET `nome`= :nome,
               `descricao`= :descricao,
               `valor`= :valor
                WHERE `id`= :id;";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':nome', $produto->getNome());
        $stmt->bindValue(':descricao', $produto->getDescricao());
        $stmt->bindValue(':valor',$produto->getValor());
        $stmt->bindValue(':id', $produto->getId());

        return $stmt->execute() ? true : false;*/
        $this->em->persist($produto);
        $this->em->flush();

        return $produto;
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