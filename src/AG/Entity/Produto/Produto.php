<?php
namespace AG\Entity\Produto;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AG\Service\Produto\ProdutoService;

/**
 * @ORM\Entity(repositoryClass="AG\Entity\Produto\ProdutoRepository")
 * @ORM\Table(name="produtos")
 * @ORM\HasLifecycleCallbacks
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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;

    private $file;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function createPath()
    {
        $this->path = ProdutoService::uploadImage($this);
    }

    /**
     * @ORM\PreRemove
     */
    public function removePath()
    {
        return ProdutoService::removeImage($this);
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }


    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    public function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../public/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/imagens';
    }

    public function getUploadAcceptedTypes()
    {
        return array('jpg', 'jpeg', 'png');
    }

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
}