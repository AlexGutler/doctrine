<?php
namespace AG\Service\Categoria;

use AG\Entity\Categoria\Categoria;
use AG\Utils\Validator\Categoria\CategoriaValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class CategoriaService
{
    private $em;
    private $categoriaValidator;

    public function __construct(EntityManager $em, CategoriaValidator $categoriaValidator)
    {
        $this->em = $em;
        $this->categoriaValidator = $categoriaValidator;
    }

    public function insert(Request $request)
    {
        $categoria = new Categoria();
        $categoria->setNome($request->get('nome'));

        $isValid = $this->categoriaValidator->validate($categoria);

        if(true !== $isValid)
        {
            return $isValid;
        }

        $this->em->persist($categoria);
        $this->em->flush();

        return $categoria;
    }

    public function update(Request $request, $id)
    {
        $categoria = $this->em->getReference('AG\Entity\Categoria\Categoria', $id);
        $categoria->setNome($request->get('nome'));

        $isValid = $this->categoriaValidator->validate($categoria);

        if(true !== $isValid)
        {
            return $isValid;
        }

        $this->em->persist($categoria);
        $this->em->flush();

        return $categoria;
    }

    public function delete($id)
    {
        $categoria = $this->em->getReference('AG\Entity\Categoria\Categoria', $id);

        $this->em->remove($categoria);
        $this->em->flush();

        return true;
    }

    public function fetch($id)
    {
        $repository = $this->em->getRepository('AG\Entity\Categoria\Categoria');

        return $this->getData($repository->find($id));
    }

    public function fetchAll()
    {
        $repository = $this->em->getRepository('AG\Entity\Categoria\Categoria');

        return $this->toArray($repository->findAll());
    }

    private function toArray(array $arrayObject)
    {
        $newArray = array();
        foreach($arrayObject as $key => $object){
            $newArray[$key]['id'] = $object->getId();
            $newArray[$key]['nome'] = $object->getNome();
        }

        return $newArray;
    }

    private function getData(Categoria $categoria)
    {
        $arrayCategoria['id'] = $categoria->getId();
        $arrayCategoria['nome'] = $categoria->getNome();

        return $arrayCategoria;
    }

    public  function buscarCategorias($options = array())
    {
        /**
         * @var $option
         * @params 'coluna', 'valor'
         */
        $repository = $this->em->getRepository('AG\Entity\Categoria\Categoria');

        return $repository->getBuscarCategorias($options);
    }

    public function fetchPagination($offset, $limit)
    {
        $repository = $this->em->getRepository('AG\Entity\Categoria\Categoria');
        return $repository->fetchPagination($offset, $limit);
    }
    /* A consulta SQL abaixo diz "retornar apenas 10 registros, começar no registro 16 (offset 15)":
      $sql = "SELECT * FROM Orders LIMIT 10 OFFSET 15"; */
}