<?php
namespace AG\Tag\Service;

use AG\Tag\Entity\Tag;
use AG\Tag\Validator\TagValidator;
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
        $tag = $this->em->getReference('AG\Tag\Entity\Tag', $id);
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
        $tag = $this->em->getReference('AG\Tag\Entity\Tag', $id);

        $this->em->remove($tag);
        $this->em->flush();

        return true;
    }

    public function fetch($id)
    {
        $repository = $this->em->getRepository('AG\Tag\Entity\Tag');

        return $this->getData($repository->find($id));
    }

    public function fetchAll()
    {
        $repository = $this->em->getRepository('AG\Tag\Entity\Tag');

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
}