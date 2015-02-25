<?php
namespace AG\Entity\Usuario;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class UsuarioRepository extends EntityRepository
{
    public function findByUsername($username)
    {
        $dql = "SELECT u FROM AG\Entity\Usuario\Usuario u WHERE u.username = '{$username}'";

        return $this
            ->getEntityManager()
            ->createQuery($dql)
            ->getResult();
    }
}