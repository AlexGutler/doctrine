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
        $this->em->flush();

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

        return $repository->findAll();
        //return $repository->getProdutosOrdenados();
        //return $repository->getProdutosPagination(1, 4);
    }
    public  function buscarProduto($options = array())
    {
        /**
         * @var $option
         * @params 'coluna', 'valor'
         */
        $repository = $this->em->getRepository('AG\Produto\Entity\Produto');
        //return $repository->findByNome($options['valor']);
        return $repository->getBuscarProdutos($options);
        //return $repository->fetchPagination($offset, $limit, $options);
    }
    public function fetchPagination($offset, $limit)
    {
        $repository = $this->em->getRepository('AG\Produto\Entity\Produto');
        return $repository->fetchPagination($offset, $limit);
    }
    /*
        A consulta SQL abaixo diz "retornar apenas 10 registros, começar no registro 16 (offset 15)":
        $sql = "SELECT * FROM Orders LIMIT 10 OFFSET 15";
    */
}