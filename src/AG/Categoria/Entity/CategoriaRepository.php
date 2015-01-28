<?php

namespace AG\Categoria\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class CategoriaRepository extends EntityRepository
{
    public function getBuscarCategorias($options = array())
    {
        $dql = "SELECT c FROM AG\Categoria\Entity\Categoria c WHERE c.'{$options['coluna']}' LIKE '%{$options['valor']}%'";

        return $this
            ->getEntityManager()
            ->createQuery($dql)
            ->getResult()
            ;
    }

    public function fetchPagination($offset, $limit)
    {
        $dql = "SELECT c FROM AG\Categoria\Entity\Categoria c";

        $query = $this
            ->getEntityManager()
            ->createQuery($dql)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return new Paginator($query);
    }
    /* A consulta SQL acima diz "retornar apenas $limit registros, começar no registro $offset":
     $sql = "SELECT * FROM Orders LIMIT 10 OFFSET 15"; */

}