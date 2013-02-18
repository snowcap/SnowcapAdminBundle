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
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Symfony\Component\Security\Core\SecurityContextInterface $securityContext
     */
    public function __construct(EntityManager $em, SecurityContextInterface $securityContext = null)
    {
        $this->em = $em;
        $this->securityContext = $securityContext;
    }

    /**
     * @param string $type
     * @param string $action
     * @param string $description
     * @param array $params
     * @param array $diff
     */
    public function log($type, $action, $description, array $params = null, array $diff = null)
    {
        $token = $this->securityContext->getToken();

        $log = new Log();
        $log
            ->setUsername(null !== $token ? $token->getUsername() : 'anonymous')
            ->setType($type)
            ->setAction($action)
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
        $log = new Log();
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
