<?php
namespace Snowcap\AdminBundle\Logger;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Doctrine\ORM\EntityManager;

use Snowcap\AdminBundle\Entity\Log;

class Logger
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var string
     */
    protected $entityClassName;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Symfony\Component\Security\Core\SecurityContextInterface $securityContext
     * @param string $entityClassName The entity where logs will be saved into
     */
    public function __construct(EntityManager $em, SecurityContextInterface $securityContext = null, $entityClassName)
    {
        $this->em = $em;
        $this->securityContext = $securityContext;
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
        $token = $this->securityContext->getToken();

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
        $log->setCreatedAt( new \datetime('now'));
        $log->setAction($action);

        $token = $this->securityContext->getToken();
        if (null !== $token && $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
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
}
