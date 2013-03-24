<?php

namespace Snowcap\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Snowcap\AdminBundle\Datalist\Datalist;
use Snowcap\AdminBundle\Datalist\Datasource\DoctrineORMDatasource;
use Symfony\Component\HttpFoundation\Request;

use Snowcap\AdminBundle\Form\Type\FileType;
use Snowcap\AdminBundle\Entity\File;

/**
 * Provides controller to manage wysiwyg related content
 *
 */
class WysiwygController extends BaseController
{
    /**
     * @return array
     *
     * @Template()
     */
    public function browserAction()
    {
        /** @var $request Request */
        $request = $this->get('request');

        parse_str($this->getRequest()->getQueryString(), $arguments);

        $file = new File();
        $uploadForm = $this->createForm(new FileType(), $file);
        $extraParameters = array();

        /** @var $datalistBuilder \Snowcap\AdminBundle\Datalist\DatalistBuilder */
        $datalistBuilder = $this
            ->get('snowcap_admin.datalist_factory')
            ->createBuilder(
                'datalist',
                array(
                    'translation_domain' => 'admin',
                    'data_class' => 'Snowcap\AdminBundle\Entity\File'
                )
            );
        /** @var $datalist Datalist */
        $datalist = $datalistBuilder
            ->addField('path', 'image')
            ->addField('name', 'text')
            ->addField('tags', 'text')
            ->addFilter('name', 'search', array('search_fields' => array('e.name', 'e.tags'), 'label' => 'search'))
            ->getDatalist();

        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('f')->from('SnowcapAdminBundle:File', 'f');

        $datasource = new DoctrineORMDatasource($queryBuilder);
        $datalist->setDatasource($datasource);
        $datalist->bind($request);

        if ('POST' === $request->getMethod()) {
            // Manage upload post
            if ($request->get('admin_snowcap_file') !== null) {
                $uploadForm->bind($request);
                if ($uploadForm->isValid()) {
                    $em->persist($file);
                    $em->flush();

                    $extraParameters = array('url' => $file->getPath());
                }
            }
        }

        return array_merge(array('uploadForm' => $uploadForm->createView(), 'datalist' => $datalist, 'arguments' => $arguments), $extraParameters);
    }
}