<?php
namespace AG\Service\Tag;

use AG\Entity\Tag\Tag;
use AG\Utils\Validator\Tag\TagValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class TagService
{
    private $em;
    private $tagValidator;

    public function __construct(EntityManager $em, TagValidator $tagValidator)
    {
        $this->em = $em;
        $this->tagValidator = $tagValidator;
    }

    public function insert(Request $request)
    {
        $tag = new Tag();
        $tag->setNome($request->get('nome'));

        $isValid = $this->tagValidator->validate($tag);

        if(true !== $isValid)
        {
            return $isValid;
        }

        $this->em->persist($tag);
        $this->em->flush();

        return $tag;
    }

    public function update(Request $request, $id)
    {
        $tag = $this->em->getReference('AG\Entity\Tag\Tag', $id);
        $tag->setNome($request->get('nome'));

        $isValid = $this->tagValidator->validate($tag);

        if(true !== $isValid)
        {
            return $isValid;
        }

        $this->em->persist($tag);
        $this->em->flush();

        return $tag;
    }

    public function delete($id)
    {
        $tag = $this->em->getReference('AG\Entity\Tag\Tag', $id);

        $this->em->remove($tag);
        $this->em->flush();

        return true;
    }

    public function fetch($id)
    {
        $repository = $this->em->getRepository('AG\Entity\Tag\Tag');

        return $this->getData($repository->find($id));
    }

    public function fetchAll()
    {
        $repository = $this->em->getRepository('AG\Entity\Tag\Tag');

        return $this->toArray($repository->findAll());
    }

    public function toArray(array $tags)
    {
        $arrayTags = array();
        foreach($tags as $key => $tag){
            $arrayTags[$key]['id'] = $tag->getId();
            $arrayTags[$key]['nome'] = $tag->getNome();
        }

        return $arrayTags;
    }

    private function getData(Tag $tag)
    {
        $arrayTag['id'] = $tag->getId();
        $arrayTag['nome'] = $tag->getNome();

        return $arrayTag;
    }

    public  function buscarTags($options = array())
    {
        /**
         * @var $option
         * @params 'coluna', 'valor'
         */
        $repository = $this->em->getRepository('AG\Entity\Tag\Tag');

        return $repository->getBuscarTags($options);
    }

    public function fetchPagination($offset, $limit)
    {
        $repository = $this->em->getRepository('AG\Entity\Tag\Tag');
        return $repository->fetchPagination($offset, $limit);
    }
    /* A consulta SQL abaixo diz "retornar apenas 10 registros, começar no registro 16 (offset 15)":
      $sql = "SELECT * FROM Orders LIMIT 10 OFFSET 15"; */
}