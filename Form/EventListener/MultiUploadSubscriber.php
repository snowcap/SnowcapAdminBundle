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
        } else {
            $dstDir = $this->dstDir;
        }

        if (!empty($data)) {
            foreach ($data as $key => $path) {
                // First check if the file is still in tmp directory
                $originalFilename = $this->rootDir . '/../web/' . trim($path, '/');
                $destinationFilename = $this->rootDir . '/../web/' . trim($dstDir, '/') . '/' . basename($path);
                $webPath = rtrim($dstDir, '/') . '/' . basename($path);
                if (file_exists($originalFilename)) { // if it does, then move it to the destination and update the data
                    $file = new File($originalFilename);
                    $file->move(dirname($destinationFilename));
                    // modify the form data with the new path
                    $data[$key] = $webPath;
                } else { // otherwise check if it is already on the destination
                    if (file_exists($destinationFilename)) { // if it does, simply modify the form data with the new path
                        // modify the form data with the new path
                        $data[$key] = $webPath;
                    } else {
                        // TODO :  check if we need to throw an exception here
                        unset($data[$key]);
                    }
                }
            }

            $event->setData($data);
        }
    }
}
