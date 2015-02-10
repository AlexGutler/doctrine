<?php
namespace AG\Entity\User;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserRepository extends EntityRepository implements UserProviderInterface
{
    private $passwordEncoder;

    public function createAdminUser($username, $password)
    {
        $user = new User();
        $user->setUsername($username);
        $user->setPassword($password);
        $user->setRoles('ROLE_ADMIN');

        $this->insert($user);
    }

    public function insert($user)
    {
        $this->encodePassword($user);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }

    public function setPasswordEncoder(PasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->findOneByUsername($username);
        if(!$user)
        {
            throw new UsernameNotFoundException(sprintf('Usuário "%s" não encontrado!', $username));
        }

        return $this->arrayToObject($user->toArray());
    }

    public function refreshUser(UserInterface $user)
    {
        if(!$user instanceof User)
        {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported', get_class($user)));
        }
    }

    public function supportsClass($class)
    {
        return $class === 'AG\Entity\User\User';
    }

    public function encodePassword(User $user)
    {
        if ($user->getPlainPassword())
        {
            $user->setPassword($this->passwordEncoder->encodePassword($user->getPlainPassword(), $user->getSalt()));
        }
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
        $roles = isset($userArr['roles']) ? explode(',', $userArr['roles']) : array();
        $createAt = isset($userArr['created_at']) ? \DateTime::createFromFormat(self::DATE_FORMAT, $userArr['created_at']) : null;

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

    public function objectToArray(User $user)
    {
        return array(
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'password' => $user->getPassword(),
            'roles' => $user->getRoles(),
            'create_at' => $user->getCreatedAt(),
        );
    }
}