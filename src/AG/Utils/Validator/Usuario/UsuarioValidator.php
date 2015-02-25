<?php
namespace AG\Utils\Validator\Usuario;

use AG\Entity\Usuario\Usuario;
use AG\Utils\Validator\Validator;

class UsuarioValidator extends Validator
{
    private $usuario;
    private $erros = array();
    private $roles = array('ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPERADMIN');

    public function validate(Usuario $usuario)
    {
        $this->usuario = $usuario;
        $this->erros = array('username' => null, 'password' => null, 'email' => null, 'role' => null);

        if($this->isEmpty($this->usuario->getUsername())) {
            $this->erros['username'] = "{{ Username }} Não pode estar vazio.";
        } elseif ($this->minStrLength($this->usuario->getUsername(), 3)) {
            $this->erros['username'] = "{{ Username }} Não pode conter menos que 3 caracteres.";
        } elseif ($this->maxStrLength($this->usuario->getUsername(), 255)) {
            $this->erros['username'] = "{{ Username }} Não pode conter mais que 255 caracteres.";
        }

        if($this->isEmpty($this->usuario->getPassword())) {
            $this->erros['password'] = "{{ Password }} Não pode estar vazio.";
        } elseif ($this->minStrLength($this->usuario->getPassword(), 6)) {
            $this->erros['password'] = "{{ Password }} Não pode conter menos que 6 caracteres.";
        } elseif ($this->maxStrLength($this->usuario->getPassword(), 255)) {
            $this->erros['password'] = "{{ Password }} Não pode conter mais que 255 caracteres.";
        }

        if(!$this->isMail($this->usuario->getEmail())) {
            $this->erros['email'] = "{{ Email }} Campo inválido.";
        }

        if(!in_array($usuario->getRoles(), $this->roles)){
            $this->erros['role'] = "{{ Role }} Nível de acesso inválido.";
        }

        // verifica se algum erro foi encontrado
        foreach($this->erros as $erro) {
            if ($erro <> null) {
                return $this->erros;
            }
        }

        return true;
    }

}