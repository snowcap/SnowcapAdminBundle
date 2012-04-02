<?php

namespace Snowcap\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Finder\Finder;

use Snowcap\AdminBundle\Form\Type\FileType;
use Snowcap\AdminBundle\Entity\File;

/**
 * Provides controller to manage wysiwyg related content
 *
 */
class WysiwygController extends Controller
{
    /**
     * @return array
     *
     * @Template()
     */
    public function browserAction()
    {
        parse_str($this->getRequest()->getQueryString(), $arguments);
        $finder = new Finder();
        $finder->files()->in($this->get('kernel')->getRootDir() . '/../web/uploads');

        return array('images' => $finder, 'arguments' => $arguments);
    }

    /**
     * @return array
     *
     * @Template()
     */
    public function uploadAction()
    {
        $request = $this->get('request');

        parse_str($this->getRequest()->getQueryString(), $arguments);

        $file = new File();
        $form = $this->createForm(new FileType(), $file);

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                /** @var $em \Doctrine\ORM\EntityManager */
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($file);
                $em->flush();

                return array('form' => $form->createView(), 'url' => $file->getPath(), 'arguments' => $arguments);
            }
        }
        return array('form' => $form->createView(), 'arguments' => $arguments);


    }

}