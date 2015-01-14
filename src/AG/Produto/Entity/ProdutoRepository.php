<?php

namespace AG\Produto\Entity;

use Doctrine\ORM\EntityRepository;

class ProdutoRepository extends EntityRepository
{
    public function getProdutosOrdenados()
    {
        return $this
            ->createQueryBuilder("p")
            ->orderBy("p.id", 'asc')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getClientesDesc()
    {
        $dql = "SELECT p FROM AG\Produto\Entity\Produto p
            ORDER BY p.nome DESC";

        return $this
            ->getEntityManager()
            ->createQuery($dql)
            ->getResult()
            ;
    }
}