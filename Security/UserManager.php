<?php

namespace Snowcap\AdminBundle\Security;

use Doctrine\ORM\EntityManager;
use Snowcap\AdminBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class UserManager
 * @package Snowcap\AdminBundle\Security
 */
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
     * @param EncoderFactoryInterface $encoderFactory
     * @param EntityManager $em
     */
    public function __construct($userClass, EncoderFactoryInterface $encoderFactory, EntityManager $em)
    {
        $parentClassName = 'Snowcap\AdminBundle\Entity\User';
        if (null === $userClass) {
            throw new \InvalidArgumentException('Please provide a valid user_class in your snowcap_admin.security config.');
        } elseif (!class_exists($userClass) || !in_array($parentClassName, class_parents($userClass))) {
            throw new \InvalidArgumentException(sprintf('Your user class does not exist or does not extend %s', $parentClassName));
        }
        $this->userClass = $userClass;
        $this->encoderFactory = $encoderFactory;
        $this->em = $em;
    }

    /**
     * @param string $userName
     * @param string $email
     * @param string $password
     * @param array $roles
     * @param array $extraFields
     * @return \Snowcap\AdminBundle\Entity\User
     */
    public function createUser($userName, $email, $password, array $roles, array $extraFields = array())
    {
        $user = new $this->userClass;
        $encoder = $this->encoderFactory->getEncoder($user);
        $encodedPassword = $encoder->encodePassword($password, $user->getSalt());

        $user
            ->setUsername($userName)
            ->setEmail($email)
            ->setPassword($encodedPassword)
            ->setRoles($roles);

        foreach ($extraFields as $extraFieldName => $extraFieldValue) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $propertyAccessor->setValue($user, $extraFieldName, $extraFieldValue);
        }

        $this->em->persist($user);
        $this->em->flush($user);
    }
}