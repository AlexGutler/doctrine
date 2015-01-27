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

        if($request->get('categoria'))
        {
            $categoriaEntity = $this->em->getReference("AG\Categoria\Entity\Categoria", $request->get('categoria'));
            $produtoEntity->setCategoria($categoriaEntity);
        }

        if($request->get('tags'))
        {
            //$tags = explode(',', $request->get('tags')); // criar um array de tags
            $tags = $request->get('tags');
            foreach($tags as $rowTag)
            {
                // pega pela referencia a tag com o id da $rowTag e o adiciona no produto
                $tagEntity = $this->em->getReference("AG\Tag\Entity\Tag", $rowTag);
                $produtoEntity->addTag($tagEntity);
            }
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

        if($request->get('categoria'))
        {
            $categoriaEntity = $this->em->getReference("AG\Categoria\Entity\Categoria", $request->get('categoria'));
            $produto->setCategoria($categoriaEntity);
        }

        // antes de adicionar as tags é necessário remover as já cadastradas no banco
        $produtoRepository = $this->em->getRepository("AG\Produto\Entity\Produto", $id);
        $produtoRepository->removeAssociationTag($id);
        foreach($request->get('tags') as $tag){
            $entityTag = $this->em->getReference("AG\Tag\Entity\Tag", $tag);
            $produto->addTag($entityTag);
        }

        // aplica no banco
        $this->em->persist($produto);
        $this->em->flush();

        return $produto;
    }

    public function delete($id)
    {
        $produto = $this->em->getReference('AG\Produto\Entity\Produto', $id);

        // remover a associação com as tags
        $produtoRepository = $this->em->getRepository("AG\Produto\Entity\Produto", $id);
        $produtoRepository->removeAssociationTag($id);

        $this->em->remove($produto);
        $this->em->flush();

        return true;
    }

    public function fetch($id)
    {
        $repository = $this->em->getRepository('AG\Produto\Entity\Produto');

        $produto = $repository->find($id);

        return $this->getData($produto);
    }

    private function getData(ProdutoEntity $produto)
    {
        $arrayProduto['id'] = $produto->getId();
        $arrayProduto['nome'] = $produto->getNome();
        $arrayProduto['descricao'] = $produto->getDescricao();
        $arrayProduto['valor'] = $produto->getValor();
        if($produto->getCategoria()){
            $arrayProduto['categoria']['id'] = $produto->getCategoria()->getId();
            $arrayProduto['categoria']['nome'] = $produto->getCategoria()->getNome();
        } else {
            $arrayProduto['categoria']['id'] = null;
            $arrayProduto['categoria']['nome'] = null;
        }
        if(count($produto->getTags())>0){
            foreach($produto->getTags() as $key => $tag){
                $arrayProduto['tags'][$key]['id'] = $tag->getId();
                $arrayProduto['tags'][$key]['nome'] = $tag->getNome();
            }
        } else {
            $arrayProduto['tags'] = null;
        }

        return $arrayProduto;
    }

    public function fetchAll()
    {
        $repository = $this->em->getRepository('AG\Produto\Entity\Produto');

        return $this->toArray($repository->findAll());
        //return $repository->getProdutosOrdenados();
        //return $repository->getProdutosPagination(1, 4);
    }

    public function toArray(array $produtos)
    {
        $arrayProdutos = array();
        foreach($produtos as $key => $produto)
        {
            $arrayProdutos[$key]['id'] = $produto->getId();
            $arrayProdutos[$key]['nome'] = $produto->getNome();
            $arrayProdutos[$key]['descricao'] = $produto->getDescricao();
            $arrayProdutos[$key]['valor'] = $produto->getValor();
            if($produto->getCategoria()){
                $arrayProdutos[$key]['categoria']['id'] = $produto->getCategoria()->getId();
                $arrayProdutos[$key]['categoria']['nome'] = $produto->getCategoria()->getNome();
            } else {
                $arrayProdutos[$key]['categoria'] = null;
            }

            if(count($produto->getTags()) > 0)
            {
                foreach($produto->getTags() as $k => $tag){
                    $arrayProdutos[$key]['tags'][$k]['id'] = $tag->getId();
                    $arrayProdutos[$key]['tags'][$k]['nome'] = $tag->getNome();
                }
            } else {
                $arrayProdutos[$key]['tags'] = null;
            }
        }

        return $arrayProdutos;
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