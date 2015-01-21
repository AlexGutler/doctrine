<?php
namespace AG\Tag\Validator;

use AG\Tag\Entity\Tag;
use AG\Validator\Validator;

class TagValidator extends Validator
{
    private $tag;
    private $erros = array();

    public function validate(Tag $tag)
    {
        $this->tag = $tag;
        $this->erros = array('nome' => null);

        if($this->isEmpty($this->tag->getNome()))
        {
            $this->erros['nome'] = "{{ Nome }} Não pode estar vazio.";
        } elseif ($this->minStrLength($this->tag->getNome(), 3)) {
            $this->erros['nome'] = "{{ Nome }} Não pode conter menos que 3 caracteres.";
        } elseif ($this->maxStrLength($this->tag->getNome(), 255)) {
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