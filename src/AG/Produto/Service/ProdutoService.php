<?php

namespace AG\Produto\Service;

use AG\Produto\Entity\Produto,
    AG\Produto\Mapper\ProdutoMapper,
    AG\Produto\Validator\ProdutoValidator;
use Symfony\Component\HttpFoundation\Request;


class ProdutoService
{
    private $produto;
    private $mapper;
    private $produtoValidator;

    public function __construct(Produto $produto, ProdutoMapper $mapper, ProdutoValidator $produtoValidator)
    {
        $this->produto = $produto;
        $this->mapper = $mapper;
        $this->produtoValidator = $produtoValidator;
    }

    public function insert(Request $request)
    {
        $this->produto->setNome($request->get('nome'))
                      ->setDescricao($request->get('descricao'))
                      ->setValor($request->get('valor'));

        $isValid = $this->produtoValidator->validate($this->produto);

        return true !== $isValid ? $isValid : $this->mapper->insert($this->produto);
    }

    public function update(Request $request, $id)
    {
        $this->produto->setId($id)
            ->setNome($request->get('nome'))
            ->setDescricao($request->get('descricao'))
            ->setValor($request->get('valor'));

        $isValid = $this->produtoValidator->validate($this->produto);

        return true !== $isValid ? $isValid : $this->mapper->update($this->produto);
    }

    public function delete($id)
    {
        return $this->mapper->delete($id) ? true : false;
    }

    public function fetch($id)
    {
        return $this->mapper->fetch($id);
    }

    public function fetchAll()
    {
        return $this->mapper->fetchAll();
    }
}