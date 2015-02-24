<?php
namespace AG\Entity\Usuario;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\table("usuarios")
 * @ORM\Entity(repositoryClass="AG\Entity\Usuario\UsuarioRepository")
 */
class Usuario
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $username;
    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $email;
    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $password;
    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $roles = array('ROLE_USER');
    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createAt;
    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $salt;

    public function __construct(){
        $this->createAt = new \DateTime();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function encryptPass()
    {
        $this->setPassword(password_hash($this->getPassword(), PASSWORD_DEFAULT));
    }

    /**
     * @ORM\PrePersist
     */
    public function generateSalt()
    {
        $this->setSalt();
        // aqui eu poderia enviar um código de validação por email...
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
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }

    /**
     * @param mixed $createAt
     */
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param mixed $salt
     */
    public function setSalt()
    {
        $this->salt = strtolower(sha1($this->getPassword() . date('Y-m-d H:i:s')));
        return $this;
    }
}