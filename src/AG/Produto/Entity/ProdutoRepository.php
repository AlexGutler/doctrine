<?php

namespace AG\Produto\Entity;

use Doctrine\ORM\EntityRepository;

class ProdutoRepository extends EntityRepository
{
    public function getProdutosOrdenados()
    {
        // QUERY BUILDER
         return $this
            ->createQueryBuilder("p")
            ->orderBy("p.nome", 'asc')
            ->getQuery()
            ->getResult()
            ;

       // DQL
       /* $dql = "SELECT p FROM AG\Produto\Entity\Produto p ORDER BY p.nome";
        return $this
            ->getEntityManager()
            ->createQuery($dql)
            ->getResult()
        ; */
    }

    public function getProdutosDesc()
    {
        $dql = "SELECT p FROM AG\Produto\Entity\Produto p
            ORDER BY p.nome DESC";

        return $this
            ->getEntityManager()
            ->createQuery($dql)
            ->getResult()
        ;
    }

    public function getBuscarProdutos($options = array())
    {
        $dql = "SELECT p FROM AG\Produto\Entity\Produto p WHERE p.'{$options['coluna']}' LIKE '%{$options['valor']}%'";

        return $this
            ->getEntityManager()
            ->createQuery($dql)
            ->getResult()
        ;
    }
}