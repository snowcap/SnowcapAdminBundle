<?php

namespace Snowcap\AdminBundle\Security;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserManager
{
    /**
     * @var string
     */
    private $userClass;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param string $userClass
     */
    public function __construct($userClass, EncoderFactoryInterface $encoderFactory, EntityManager $em)
    {
        $this->userClass = $userClass;
        $this->encoderFactory = $encoderFactory;
        $this->em = $em;
    }

    /**
     * @param string $userName
     * @param string $email
     * @param string $password
     * @param array $roles
     * @return Snowcap\AdminBundle\Entity\User
     */
    public function createUser($userName, $email, $password, array $roles)
    {
        $user = new $this->userClass;
        $encoder = $this->encoderFactory->getEncoder($user);
        $encodedPassword = $encoder->encodePassword($password, $user->getSalt());

        $user
            ->setUsername($userName)
            ->setEmail($email)
            ->setPassword($encodedPassword)
            ->setRoles($roles);

        $this->em->persist($user);
        $this->em->flush($user);
    }
}