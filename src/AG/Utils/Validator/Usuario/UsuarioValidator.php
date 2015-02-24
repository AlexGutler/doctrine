<?php
namespace AG\Utils\Validator\Usuario;

use AG\Entity\Usuario\Usuario;
use AG\Utils\Validator\Validator;

class UsuarioValidator extends Validator
{
    private $usuario;
    private $erros = array();

    public function validate(Usuario $usuario)
    {
//        $this->usuario = $usuario;
//        $this->erros = array('nome' => null, 'descricao' => null, 'valor' => null, 'file' => null);
//
//        if($this->isEmpty($this->produto->getNome()))
//        {
//            $this->erros['nome'] = "{{ Nome }} Não pode estar vazio.";
//        } elseif ($this->minStrLength($this->produto->getNome(), 3)) {
//            $this->erros['nome'] = "{{ Nome }} Não pode conter menos que 3 caracteres.";
//        } elseif ($this->maxStrLength($this->produto->getNome(), 255)) {
//            $this->erros['nome'] = "{{ Nome }} Não pode conter mais que 255 caracteres.";
//        }
//
//        if($this->isEmpty($this->produto->getDescricao()))
//        {
//            $this->erros['descricao'] = "{{ Descrição }} Não pode estar vazio.";
//        } elseif ($this->minStrLength($this->produto->getDescricao(), 20)) {
//            $this->erros['descricao'] = "{{ Descrição }} Não pode conter menos que 20 caracteres.";
//        }
//
//        if($this->isEmpty($this->produto->getValor()))
//        {
//            $this->erros['valor'] = "{{ Valor }} Não pode estar vazio.";
//        } elseif (!$this->isNumeric($this->produto->getValor())) {
//            $this->erros['valor'] = "{{ Valor }} Deve ser numérico.";
//        } elseif (!$this->isNaturalNumber($this->produto->getValor())) {
//            $this->erros['valor'] = "{{ Valor }} Não pode ser negativo.";
//        } elseif ($this->isZero($this->produto->getValor())){
//            $this->erros['valor'] = "{{ Valor }} Não pode ser zero.";
//        }
//
//        if($produto->getFile() <> null){
//            if(!in_array($produto->getFile()->getClientOriginalExtension(), self::getUploadAcceptedTypes()))
//            {
//                $this->erros['file'] = "{{ Imagem }} Não é um formato permitido.";
//            }
//        }
//
//        // verifica se algum erro foi encontrado
//        foreach($this->erros as $erro)
//        {
//            if ($erro <> null)
//            {
//                return $this->erros;
//            }
//        }

        return true;
    }

}