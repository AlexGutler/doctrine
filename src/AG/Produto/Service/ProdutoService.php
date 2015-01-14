<?php

namespace AG\Produto\Service;

use AG\Produto\Entity\Produto as ProdutoEntity,
    AG\Produto\Validator\ProdutoValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;


class ProdutoService
{
    private $produtoValidator;
    private $em;

    public function __construct(EntityManager $em, ProdutoValidator $produtoValidator)
    {
        $this->em = $em;
        $this->produtoValidator = $produtoValidator;
    }

    public function insert(Request $request)
    {
        $produtoEntity = new ProdutoEntity();
        $produtoEntity->setNome($request->get('nome'))
                      ->setDescricao($request->get('descricao'))
                      ->setValor($request->get('valor'));

        $isValid = $this->produtoValidator->validate($produtoEntity);

        if(true !== $isValid)
        {
            return $isValid;
        }

        $this->em->persist($produtoEntity);
        $this->em->flush();
        return $produtoEntity;
    }

    public function update(Request $request, $id)
    {
        $produto = $this->em->getReference('AG\Produto\Entity\Produto', $id);

        $produto
            ->setNome($request->get('nome'))
            ->setDescricao($request->get('descricao'))
            ->setValor($request->get('valor'));

        $isValid = $this->produtoValidator->validate($produto);

        if(true !== $isValid)
        {
            return $isValid;
        }

        $this->em->persist($produto);
        $this->em->flush();

        return $produto;
    }

    public function delete($id)
    {
        $produto = $this->em->getReference('AG\Produto\Entity\Produto', $id);

        $this->em->remove($produto);

        return true;
    }

    public function fetch($id)
    {
        $repository = $this->em->getRepository('AG\Produto\Entity\Produto');

        return $repository->find($id);
    }

    public function fetchAll()
    {
        $repository = $this->em->getRepository('AG\Produto\Entity\Produto');

        //return $repository->findAll();
        return $repository->getClientesDesc();
    }
}