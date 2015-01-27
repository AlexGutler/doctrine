<?php

namespace AG\Produto\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

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

    public function fetchPagination($offset, $limit)
    {
        $dql = "SELECT p FROM AG\Produto\Entity\Produto p";

        $query = $this
            ->getEntityManager()
            ->createQuery($dql)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return new Paginator($query);
    }

    /*
        A consulta SQL acima diz "retornar apenas $limit registros, começar no registro $offset":
        $sql = "SELECT * FROM Orders LIMIT 10 OFFSET 15";
    */


    public function removeAssociationTag($idProduto)
    {
        $sql = "DELETE FROM produtos_tags WHERE produto_id = :id";
        $params = array('id' => $idProduto);

        return $this->getEntityManager()->getConnection()->prepare($sql)->execute($params);
    }
}