<?php

namespace Snowcap\AdminBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\HttpFoundation\File\File;

class MultiUploadSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var string
     */
    private $dstDir;

    /**
     * @param $rootDir
     * @param $dstDir
     */
    public function __construct($rootDir, $dstDir)
    {
        $this->rootDir = $rootDir;
        $this->dstDir = $dstDir;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'move',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function move(FormEvent $event)
    {
        $data = $event->getData();

        if (is_callable($this->dstDir)) {
            $dstDir = call_user_func($this->dstDir, $event->getForm()->getParent()->getData());
        }

        if (!empty($data)) {
            foreach ($data as $key => $path) {
                $filename = $this->rootDir . '/../web/' . $path;

                if (!file_exists($filename)) {
                    $file = new File($filename);
                    $file->move($this->rootDir . '/../web/' . $dstDir);
                    // modify the form data with the new path
                    $data[$key] = rtrim($dstDir, '/') . '/' . basename($path);
                }
            }

            $event->setData($data);
        }
    }
}
