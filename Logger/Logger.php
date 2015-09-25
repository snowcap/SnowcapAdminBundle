<?php

namespace Snowcap\AdminBundle\Logger;

use Doctrine\ORM\EntityManager;
use Snowcap\AdminBundle\AdminManager;
use Snowcap\AdminBundle\Entity\Log;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class Logger
 * @package Snowcap\AdminBundle\Logger
 */
class Logger
{
    /**
     * @var \Snowcap\AdminBundle\AdminManager
     */
    protected $admin;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var string
     */
    protected $entityClassName;

    /**
     * @param AdminManager $admin
     * @param \Doctrine\ORM\EntityManager $em
     * @param TokenStorageInterface $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param string $entityClassName The entity where logs will be saved into
     */
    public function __construct(AdminManager $admin, EntityManager $em, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker, $entityClassName)
    {
        $this->admin = $admin;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->entityClassName = $entityClassName;
    }

    /**
     * @param string $type
     * @param string $action
     * @param string $description
     * @param string $admin
     * @param int $entityId
     * @param array $params
     * @param array $diff
     */
    public function log($type, $action, $description, $admin = null, $entityId = null, array $params = null, array $diff = null)
    {
        $token = $this->tokenStorage->getToken();

        /** @var $log Log */
        $log = new $this->entityClassName();
        $log
            ->setUsername(null !== $token ? $token->getUsername() : 'anonymous')
            ->setType($type)
            ->setAction($action)
            ->setAdmin($admin)
            ->setEntityId($entityId)
            ->setDescription($description)
            ->setCreatedAt(new \DateTime())
            ->setParams($params)
            ->setDiff($diff);

        $this->em->persist($log);
        $this->em->flush($log);
    }

    /**
     * @param string $type
     * @param string $action
     * @return \Snowcap\AdminBundle\Entity\Log
     */
    public function initLog($type, $action)
    {
        /** @var $log Log */
        $log = new $this->entityClassName();
        $log->setType($type);
        $log->setCreatedAt(new \DateTime('now'));
        $log->setAction($action);

        $token = $this->tokenStorage->getToken();
        if (null !== $token && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $log->setUsername($token->getUsername());
        } else {
            $log->setUsername('anonymous');
        }

        return $log;
    }

    public function logCatalogTranslation($catalogue, $locale, $diff)
    {
        $log = $this->initLog(Log::TYPE_CATALOG_TRANSLATION, 'update');
        $log->setParams( array(
            'catalogue' => $catalogue,
            'locale'    => $locale,
        ));
        $log->setDescription($catalogue . ' (' . $locale . ')');
        $log->setDiff($diff);

        $this->em->persist($log);
        $this->em->flush();
    }

    /**
     * Get logs corresponding to the provided criteria
     *
     * @param $entity
     * @param string|array $action
     * @return array
     */
    public function getLogsForEntity($entity, $action = null)
    {
        if (null !== $action && !is_array($action)) {
            $action = array($action);
        }

        $admin = $this->admin->getAdminForEntity($entity);

        $qb = $this->em->createQueryBuilder()
            ->select('l')
            ->from($this->entityClassName, 'l')
            ->where('l.admin = :admin')
            ->andWhere('l.entityId = :entity_id')
            ->setParameter('admin', $admin->getAlias())
            ->setParameter('entity_id', $entity->getId());

        if(null !== $action) {
            $qb
                ->andWhere($qb->expr()->in('l.action', ':action'))
                ->setParameter('action', $action);
        }

        return $qb->getQuery()->getResult();
    }
}