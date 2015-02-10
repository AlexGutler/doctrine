<?php
namespace AG\Entity\User;


use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use AG\Entity\User\User;

class UserProvider implements UserProviderInterface
{
    private $passwordEncoder;
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em, PasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->em->getRepository("AG\\Entity\\User\\User")->findOneBy(array("username" => $username));
        if(!$user)
        {
            throw new UsernameNotFoundException(sprintf('Usuário "%s" não encontrado!', $username));
        }
        $user->setRoles(explode(",", $user->getRoles()));
        return $user;

//        $user = $this->em->findByUsername($username);
//        if(!$user)
//        {
//            throw new UsernameNotFoundException(sprintf('Usuário "%s" não encontrado!', $username));
//        }
//        return $this->arrayToObject($user);
    }

    public function refreshUser(UserInterface $user)
    {
        if(!$user instanceof User)
        {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported', get_class($user)));
        }
        return $this->loadUserByUsername($user->getUsername());

//        if(!$user instanceof User)
//        {
//            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported', get_class($user)));
//        }
    }

    public function supportsClass($class)
    {
        return $class === 'AG\Entity\User\User';
    }

    public function arrayToObject($userArr, $user = null)
    {
        if(!$user)
        {
            $user = new User();

            $user->setId(isset($userArr['id']) ? $user['id'] : null);
        }

        $username = isset($userArr['username']) ? $user['username'] : null;
        $password = isset($userArr['password']) ? $user['password'] : null;
        $roles = isset($userArr['roles']) ? explode(',', $userArr['roles']) : array("ROLE_USER");
        $createAt = isset($userArr['created_at']) ? $userArr['created_at'] : null;

        if($username)
        {
            $user->setUsername($username);
        }

        if($password)
        {
            $user->setPassword($password);
        }

        if($roles)
        {
            $user->setRoles($roles);
        }

        if($createAt)
        {
            $user->setCreatedAt($createAt);
        }

        return $user;
    }
}