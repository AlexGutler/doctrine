<?php

namespace AG\Produto\Mapper;

use AG\Produto\Entity\Produto;
use Doctrine\ORM\EntityManager;

class ProdutoMapper
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function insert(Produto $produto)
    {
        $this->em->persist($produto);
        $this->em->flush();

        return $produto;
    }

    public function update(Produto $produto)
    {
        if ($this->em->find('AG\Produto\Entity\Produto', $produto->getId()))
        {
            $this->em->merge($produto);
            $this->em->flush();
            return $produto;
        }
        return false;
    }

    public function delete($id)
    {
        $produto = ($this->em->find('AG\Produto\Entity\Produto', $id)) ? ($this->em->find('AG\Produto\Entity\Produto', $id)) : false;

        if(!$produto)
        {
            return false;
        }

        $this->em->remove($produto);
        $this->em->flush();
        return true;
    }


    public function fetchAll()
    {
        $produtos = $this->em->getRepository('AG\Produto\Entity\Produto')->findAll();
        $data = array();
        foreach ($produtos as $key => $produto)
        {
            $data[] = array(
                'id' => $produto->getId(),
                'nome' => $produto->getNome(),
                'descricao' => $produto->getDescricao(),
                'valor' => $produto->getValor()
            );
        }
        return $data;
    }

    public function fetch($id)
    {
        $produto = $this->em->find('AG\Produto\Entity\Produto', $id);
        return [
            'id' => $produto->getId(),
            'nome' => $produto->getNome(),
            'descricao' => $produto->getDescricao(),
            'valor' => $produto->getValor()
        ];
    }
}