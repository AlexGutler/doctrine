<?php
namespace AG\Categoria\Validator;

use AG\Categoria\Entity\Categoria;
use AG\Validator\Validator;

class CategoriaValidator extends Validator
{
    private $categoria;
    private $erros = array();

    public function validate(Categoria $categoria)
    {
        $this->categoria = $categoria;
        $this->erros = array('nome' => null);

        if($this->isEmpty($this->categoria->getNome()))
        {
            $this->erros['nome'] = "{{ Nome }} Não pode estar vazio.";
        } elseif ($this->minStrLength($this->categoria->getNome(), 3)) {
            $this->erros['nome'] = "{{ Nome }} Não pode conter menos que 3 caracteres.";
        } elseif ($this->maxStrLength($this->categoria->getNome(), 255)) {
            $this->erros['nome'] = "{{ Nome }} Não pode conter mais que 255 caracteres.";
        }

        // verifica se algum erro foi encontrado
        foreach($this->erros as $erro)
        {
            if ($erro <> null)
            {
                return $this->erros;
            }
        }

        return true;
    }
}