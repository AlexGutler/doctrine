<?php
namespace AG\Service\Usuario;

use AG\Entity\Usuario\Usuario,
    AG\Utils\Validator\usuario\usuarioValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class UsuarioService
{
    private $usuarioValidator;
    private $em;
    private $usuario;

    public function __construct(Usuario $usuario, EntityManager $em, usuarioValidator $usuarioValidator)
    {
        $this->usuario = $usuario;
        $this->em = $em;
        $this->usuarioValidator = $usuarioValidator;
    }
    // password_verify($senha, $user['senha'])
    public function insert(Request $request)
    {
        $this->usuario->setUsername($request->get('username'))
                      ->setEmail($request->get('email'))
                      ->setPassword($request->get('password'));

        //$this->usuario->setFile($request->files->get('path'));

        $isValid = $this->usuarioValidator->validate($this->usuario);

        if(true !== $isValid)
        {
            return $isValid;
        }

        $this->em->persist($this->usuario);
        $this->em->flush();

        return $this->usuario;
    }

    public function update(Request $request, $id)
    {
        $this->usuario = $this->em->getReference('AG\Entity\Usuario\Usuario', $id);

        $this->usuario->setUsername($request->get('username'))
            ->setEmail($request->get('email'))
            ->setPassword($request->get('password'));

//        if($request->files->get('path')){
//            self::removeImage($this->usuario);
//            $this->usuario->setFile($request->files->get('path'));
//        }

        $isValid = $this->usuarioValidator->validate($this->usuario);

        if(true !== $isValid)
        {
            return $isValid;
        }

        // aplica no banco
        $this->em->persist($this->usuario);
        $this->em->flush();

        return $this->usuario;
    }

    public function delete($id)
    {
        $this->usuario = $this->em->getReference('AG\Entity\Usuario\Usuario', $id);

        $this->em->remove($this->usuario);
        $this->em->flush();

        return true;
    }

    public function fetch($id)
    {
        $repository = $this->em->getRepository('AG\Entity\Usuario\Usuario');

        $this->usuario = $repository->find($id);

        //return $this->getData($this->usuario);
    }

    private function getData(Usuario $usuario)
    {
//        $arrayusuario['id'] = $usuario->getId();
//        $arrayusuario['nome'] = $usuario->getNome();
//        $arrayusuario['descricao'] = $usuario->getDescricao();
//        $arrayusuario['valor'] = $usuario->getValor();
//        if($usuario->getCategoria()){
//            $arrayusuario['categoria']['id'] = $usuario->getCategoria()->getId();
//            $arrayusuario['categoria']['nome'] = $usuario->getCategoria()->getNome();
//        } else {
//            $arrayusuario['categoria']['id'] = null;
//            $arrayusuario['categoria']['nome'] = null;
//        }
//        if(count($usuario->getTags()) > 0){
//            foreach($usuario->getTags() as $key => $tag){
//                $arrayusuario['tags'][$key]['id'] = $tag->getId();
//                $arrayusuario['tags'][$key]['nome'] = $tag->getNome();
//            }
//        } else {
//            $arrayusuario['tags'] = null;
//        }
//        $arrayusuario['path'] = $usuario->getPath();
//
//        return $arrayusuario;
    }

    public function fetchAll()
    {
        $repository = $this->em->getRepository('AG\Entity\Usuario\Usuario');

        return $this->toArray($repository->findAll());
    }

    public function toArray(array $usuarios)
    {
//        $arrayusuarios = array();
//        foreach($usuarios as $key => $usuario)
//        {
//            $arrayusuarios[$key]['id'] = $usuario->getId();
//            $arrayusuarios[$key]['nome'] = $usuario->getNome();
//            $arrayusuarios[$key]['descricao'] = $usuario->getDescricao();
//            $arrayusuarios[$key]['valor'] = $usuario->getValor();
//            if($usuario->getCategoria()){
//                $arrayusuarios[$key]['categoria']['id'] = $usuario->getCategoria()->getId();
//                $arrayusuarios[$key]['categoria']['nome'] = $usuario->getCategoria()->getNome();
//            } else {
//                $arrayusuarios[$key]['categoria'] = null;
//            }
//
//            if(count($usuario->getTags()) > 0)
//            {
//                foreach($usuario->getTags() as $k => $tag){
//                    $arrayusuarios[$key]['tags'][$k]['id'] = $tag->getId();
//                    $arrayusuarios[$key]['tags'][$k]['nome'] = $tag->getNome();
//                }
//            } else {
//                $arrayusuarios[$key]['tags'] = null;
//            }
//            $arrayusuarios['path'] = $usuario->getPath();
//        }
//
//        return $arrayusuarios;
    }

    public function buscarUsuario($nome)
    {
        $repository = $this->em->getRepository('AG\Entity\Usuario\Usuario');
        return $repository->getBuscarUsuarios($nome);
    }

    public function fetchPagination($offset, $limit)
    {
        $repository = $this->em->getRepository('AG\Entity\Usuario\Usuario');
        return $repository->fetchPagination($offset, $limit);
    }
}