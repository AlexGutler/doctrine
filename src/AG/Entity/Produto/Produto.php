<?php
namespace AG\Entity\Produto;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AG\Entity\Produto\ProdutoRepository")
 * @ORM\Table(name="produtos")
 */
class Produto
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nome;

    /**
     * @ORM\Column(type="text")
     */
    private $descricao;

    /**
     * @ORM\Column(type="float", scale=2)
     */
    private $valor;

    /**
     * @ORM\ManyToOne(targetEntity="AG\Entity\Categoria\Categoria")
     * @ORM\JoinColumn(name="categoria_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $categoria;

    /**
     * @ORM\ManyToMany(targetEntity="AG\Entity\Tag\Tag")
     * @ORM\JoinTable(name="produtos_tags",
     *      joinColumns={@ORM\JoinColumn(name="produto_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id", onDelete="CASCADE")}
     *     )
     */
    private $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * @param mixed $tags
     */
    public function addTag($tags)
    {
        $this->tags->add($tags);
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return mixed
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * @param mixed $categoria
     * @return mixed
     */
    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return mixed
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param mixed $nome
     * @return mixed
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param mixed $descricao
     * @return mixed
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * @param mixed $valor
     * @return mixed
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'id-> '.$this->getId().' | nome-> '.$this->getNome().
        ' | descricao-> '.$this->getDescricao().' | valor-> '.$this->getValor().
        ' | categoria_id->'.$this->getCategoria()->getId();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        if($this->getTags()){
            $tags = array();
            foreach ($this->getTags() as $tag) {
                $tags[] = $tag->getNome();
            }
        } else {
            $tags = null;
        }

        if ($this->getCategoria()) {
            $categoria = $this->getCategoria()->toArray();
        } else {
            $categoria = null;
        }

        return [
            'id' => $this->getId(),
            'nome' => $this->getNome(),
            'descricao' => $this->getDescricao(),
            'valor' => $this->getValor(),
            'tags' => $tags,
            'categoria' => $categoria
        ];
    }
}