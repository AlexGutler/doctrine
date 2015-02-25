<?php
namespace AG\Entity\Tag;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class TagRepository extends EntityRepository
{
    public function getBuscarTags($options = array())
    {
        $dql = "SELECT t FROM AG\Entity\Tag\Tag t WHERE t.'{$options['coluna']}' LIKE '%{$options['valor']}%'";

        return $this
            ->getEntityManager()
            ->createQuery($dql)
            ->getResult();
    }

    public function fetchPagination($offset, $limit)
    {
        $dql = "SELECT t FROM AG\Entity\Tag\Tag t";

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