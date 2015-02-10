<?php
namespace AG\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AG\Entity\User\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $roles = array('ROLE_USER');

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getSalt()
    {
        return null;
    }

    public function __toString()
    {
        return $this->getUsername();
    }

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'username' => $this->getUsername(),
            'salt' => $this->getSalt(),
            'roles' => $this->getRoles(),
            'password' => $this->getPassword()
        );
    }
}