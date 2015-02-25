<?php
namespace AG\Service\Usuario;

use AG\Entity\Usuario\Usuario,
    AG\Utils\Validator\Usuario\UsuarioValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class UsuarioService
{
    private $usuarioValidator;
    private $em;
    private $usuario;

    public function __construct(Usuario $usuario, EntityManager $em, UsuarioValidator $usuarioValidator)
    {
        $this->usuario = $usuario;
        $this->em = $em;
        $this->usuarioValidator = $usuarioValidator;
    }

    public function insert(Request $request)
    {
        $this->usuario->setUsername($request->get('username'))
                      ->setEmail($request->get('email'))
                      ->setPassword($request->get('password'))
                      ->setRoles($request->get('role'));
        $isValid = $this->usuarioValidator->validate($this->usuario);
        if(true !== $isValid) {
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
            ->setPassword($request->get('password'))
            ->setRoles($request->get('role'));
        
        $isValid = $this->usuarioValidator->validate($this->usuario);
        if(true !== $isValid) {
            return $isValid;
        }

        $this->em->persist($this->usuario);
        $this->em->flush();

        return $this->usuario;
    }

    public function login(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');
        $repository = $this->em->getRepository('AG\Entity\Usuario\Usuario');

        $this->usuario = $repository->findOneByUsername($username);

        if ($this->usuario){
            if (password_verify($password, $this->usuario->getPassword())) {
                return $this->getData($this->usuario);
            }
        } else {
            return false;
        }
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

        return $this->getData($this->usuario);
    }

    private function getData(Usuario $usuario)
    {
        $arrayUsuario = array();

        $arrayUsuario['id'] = $usuario->getId();
        $arrayUsuario['username'] = $usuario->getUsername();
        $arrayUsuario['password'] = $usuario->getPassword();
        $arrayUsuario['email'] = $usuario->getEmail();
        $arrayUsuario['roles'] = $usuario->getRoles();

        return $arrayUsuario;
    }

    public function fetchAll()
    {
        $repository = $this->em->getRepository('AG\Entity\Usuario\Usuario');

        return $this->toArray($repository->findAll());
    }

    public function toArray(array $usuarios)
    {
        $arrayUsuarios = array();
        foreach($usuarios as $key => $usuario)
        {
            $arrayUsuarios[$key]['id'] = $usuario->getId();
            $arrayUsuarios[$key]['username'] = $usuario->getUsername();
            $arrayUsuarios[$key]['password'] = $usuario->getPassword();
            $arrayUsuarios[$key]['email'] = $usuario->getEmail();
            $arrayUsuarios[$key]['roles'] = $usuario->getRoles();
        }
        return $arrayUsuarios;
    }

//    public function buscarUsuario($nome)
//    {
//        $repository = $this->em->getRepository('AG\Entity\Usuario\Usuario');
//        return $repository->getBuscarUsuarios($nome);
//    }
//
//    public function fetchPagination($offset, $limit)
//    {
//        $repository = $this->em->getRepository('AG\Entity\Usuario\Usuario');
//        return $repository->fetchPagination($offset, $limit);
//    }
}