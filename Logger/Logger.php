<?php
namespace Snowcap\AdminBundle\Logger;

use Symfony\Bundle\DoctrineBundle\Registry;
use Snowcap\AdminBundle\Entity\Log;

use Symfony\Component\Security\Core\SecurityContextInterface;

class Logger
{
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';

    /** @var \Symfony\Bundle\DoctrineBundle\Registry */
    protected $doctrine;

    /** @var null|\Symfony\Component\Security\Core\SecurityContextInterface */
    protected $securityContext;

    public function __construct(Registry $doctrine, SecurityContextInterface $securityContext = null)
    {
        $this->doctrine = $doctrine;
        $this->securityContext = $securityContext;
    }

    /**
     * @param string $type
     * @param string $action
     * @return \Snowcap\AdminBundle\Entity\Log
     */
    public function initLog($type, $action)
    {
        $log = new Log();
        $log->setType($type);
        $log->setCreatedAt( new \datetime('now'));
        $log->setAction($action);

        $token = $this->securityContext->getToken();
        if (null !== $token && $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $log->setUsername($token);
        } else {
            $log->setUsername('admin');
        }

        return $log;
    }

    /**
     * @param string $action
     * @param \Snowcap\AdminBundle\Admin\AbstractAdmin $admin
     * @param object $entity
     * @param string $locale
     */
    public function logContent($action, $admin, $entity, $locale)
    {
        $em = $this->doctrine->getEntityManager();
        $uow = $em->getUnitOfWork();
        $uow->computeChangeSets();

        $changeset = $uow->getEntityChangeSet($entity);
        if($admin->isTranslatable()) {
            $translationEntity = $admin->findTranslationEntity($entity, $locale);
            $changesetLocale = $uow->getEntityChangeSet($translationEntity);
            $changeset = array_merge_recursive($changeset, $changesetLocale);
        }

        $log = $this->initLog(Log::TYPE_CONTENT, $action);
        $log->setParams( array(
            'entity'    => get_class($entity),
            'entityId'  => $entity->getId(),
        ));
        $log->setDescription($admin->toString($entity));
        $log->setDiff($changeset);

        $em->persist($log);
        $em->flush();
    }

    public function logCatalogTranslation($catalogue, $locale, $diff)
    {
        $em = $this->doctrine->getEntityManager();

        $log = $this->initLog(Log::TYPE_CATALOG_TRANSLATION, 'update');
        $log->setParams( array(
            'catalogue' => $catalogue,
            'locale'    => $locale,
        ));
        $log->setDescription($catalogue . ' (' . $locale . ')');
        $log->setDiff($diff);

        $em->persist($log);
        $em->flush();
    }
}
