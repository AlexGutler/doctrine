<?php
namespace AG\Service\Produto;

use AG\Entity\Produto\Produto as ProdutoEntity,
    AG\Utils\Validator\Produto\ProdutoValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class ProdutoService
{
    private $produtoValidator;
    private $em;
    private $produto;

    public function __construct(ProdutoEntity $produto, EntityManager $em, ProdutoValidator $produtoValidator)
    {
        $this->produto = $produto;
        $this->em = $em;
        $this->produtoValidator = $produtoValidator;
    }

    public function insert(Request $request)
    {
        $this->produto->setNome($request->get('nome'))
                      ->setDescricao($request->get('descricao'))
                      ->setValor($request->get('valor'));

        $this->produto->setFile($request->files->get('path'));

        $isValid = $this->produtoValidator->validate($this->produto);

        if(true !== $isValid)
        {
            return $isValid;
        }

        if($request->get('categoria'))
        {
            $categoriaEntity = $this->em->getReference("AG\Entity\Categoria\Categoria", $request->get('categoria'));
            $this->produto->setCategoria($categoriaEntity);
        }

        if($request->get('tags'))
        {
            //$tags = explode(',', $request->get('tags')); // criar um array de tags
            $tags = $request->get('tags');
            foreach($tags as $rowTag)
            {
                // pega pela referencia a tag com o id da $rowTag e o adiciona no produto
                $tagEntity = $this->em->getReference("AG\Entity\Tag\Tag", $rowTag);
                $this->produto->addTag($tagEntity);
            }
        }

        $this->em->persist($this->produto);
        $this->em->flush();

        return $this->produto;
    }

    public function update(Request $request, $id)
    {
        $this->produto = $this->em->getReference('AG\Entity\Produto\Produto', $id);

        $this->produto
            ->setNome($request->get('nome'))
            ->setDescricao($request->get('descricao'))
            ->setValor($request->get('valor'));

        if($request->files->get('path')){
            self::removeImage($this->produto);
            $this->produto->setFile($request->files->get('path'));
        }

        $isValid = $this->produtoValidator->validate($this->produto);

        if(true !== $isValid)
        {
            return $isValid;
        }

        if($request->get('categoria'))
        {
            $categoriaEntity = $this->em->getReference("AG\Entity\Categoria\Categoria", $request->get('categoria'));
            $this->produto->setCategoria($categoriaEntity);
        }

        // antes de adicionar as tags é necessário remover as já cadastradas no banco
        $produtoRepository = $this->em->getRepository("AG\Entity\Produto\Produto", $this->produto->getId());
        $produtoRepository->removeAssociationTag($this->produto->getId());

        if($request->get('tags')){
            foreach($request->get('tags') as $tag){
                $entityTag = $this->em->getReference("AG\Entity\Tag\Tag", $tag);
                $this->produto->addTag($entityTag);
            }
        }

        // aplica no banco
        $this->em->persist($this->produto);
        $this->em->flush();

        return $this->produto;
    }

    public function delete($id)
    {
        $this->produto = $this->em->getReference('AG\Entity\Produto\Produto', $id);

        // remover a associação com as tags
        $produtoRepository = $this->em->getRepository("AG\Entity\Produto\Produto", $id);
        $produtoRepository->removeAssociationTag($id);

        $this->em->remove($this->produto);
        $this->em->flush();

        return true;
    }

    public function fetch($id)
    {
        $repository = $this->em->getRepository('AG\Entity\Produto\Produto');

        $this->produto = $repository->find($id);

        return $this->getData($this->produto);
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
        if(count($produto->getTags()) > 0){
            foreach($produto->getTags() as $key => $tag){
                $arrayProduto['tags'][$key]['id'] = $tag->getId();
                $arrayProduto['tags'][$key]['nome'] = $tag->getNome();
            }
        } else {
            $arrayProduto['tags'] = null;
        }
        $arrayProduto['path'] = $produto->getPath();

        return $arrayProduto;
    }

    public function fetchAll()
    {
        $repository = $this->em->getRepository('AG\Entity\Produto\Produto');

        return $this->toArray($repository->findAll());
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
            $arrayProdutos['path'] = $produto->getPath();
        }

        return $arrayProdutos;
    }

    public function buscarProduto($nome)
    {
        $repository = $this->em->getRepository('AG\Entity\Produto\Produto');
        return $repository->getBuscarProdutos($nome);
    }

    public function fetchPagination($offset, $limit)
    {
        $repository = $this->em->getRepository('AG\Entity\Produto\Produto');
        return $repository->fetchPagination($offset, $limit);
    }

    static public function uploadImage(ProdutoEntity $produto)
    {
        if (null === $produto->getFile()) {
            return $produto->getPath();
        }

        $filename = sha1($produto->getFile()->getClientOriginalName() . date('Y-m-d H:i:s')) . '.' . $produto->getFile()->getClientOriginalExtension();

        $produto->getFile()->move(
            $produto->getUploadRootDir(),
            $filename
        );

        return $filename;
    }


    static public function removeImage(ProdutoEntity $produto)
    {
        if (null === $produto->getPath()) {
            return;
        }

        if(file_exists($produto->getAbsolutePath()))
            unlink($produto->getAbsolutePath());

        return true;
    }
}