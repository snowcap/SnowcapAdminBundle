<?php

namespace Snowcap\AdminBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Snowcap\AdminBundle\Logger\Logger;
use Snowcap\AdminBundle\Event\AdminEvents;
use Snowcap\AdminBundle\Event\ContentAdminEvent;

class LoggerListener implements EventSubscriberInterface{
    /**
     * @var \Snowcap\AdminBundle\Logger\Logger
     */
    private $logger;

    /**
     * @param \Snowcap\AdminBundle\Logger\Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            AdminEvents::CONTENT_CREATE => 'onContentAction',
            AdminEvents::CONTENT_UPDATE => 'onContentAction',
            AdminEvents::CONTENT_DELETE => 'onContentAction',
        );
    }

    public function onContentAction(ContentAdminEvent $event)
    {
        $entity = $event->getEntity();
        $admin = $event->getAdmin();

        $this->logger->log('content', $this->getAction($event->getName()), $admin->getEntityName($entity), $admin->getAlias(), $entity->getId());
    }

    /**
     * @param $eventName
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getAction($eventName)
    {
        switch($eventName) {
            case AdminEvents::CONTENT_CREATE:
                return 'content_create';
            break;
            case AdminEvents::CONTENT_UPDATE:
                return 'content_update';
            break;
            case AdminEvents::CONTENT_DELETE:
                return 'content_delete';
            break;
            default:
                throw new \InvalidArgumentException(sprintf('Cannot process event "%s"', $eventName));
                break;
        }
    }
}