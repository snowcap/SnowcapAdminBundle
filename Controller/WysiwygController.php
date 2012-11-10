<?php

namespace Snowcap\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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

        $builder = $this->get('form.factory')->createBuilder('search')->add('name', 'text');
        $searchForm = $builder->getForm();

        $file = new File();
        $uploadForm = $this->createForm(new FileType(), $file);
        $extraParameters = array();

        /** @var $list \Snowcap\AdminBundle\Datalist\ContentDatalist */
        $list = $this->get('snowcap_admin.datalist_factory')->create('thumbnail', 'browser');
        $list
            ->add('path', 'image')
            ->add('name', 'label')
            ->add('tags', 'description');

        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('f')->from('SnowcapAdminBundle:File', 'f');

        $list->setQueryBuilder($queryBuilder);


        if ('POST' === $request->getMethod()) {
            // Manage search post
            if ($request->get('search') !== null) {
                $searchForm->bindRequest($this->get('request'));
                $searchData = $searchForm->getData();
                $filters = array(
                    'name' => array(
                        'field' => 'f.name',
                        'operator' => 'LIKE',
                        'value' => $searchData['name'],
                    ),
                    'tags' => array(
                        'field' => 'f.tags',
                        'operator' => 'LIKE',
                        'value' => $searchData['name']
                    )
                );
                $list->filterData($filters, 'OR');
            }
            // Manage upload post
            if ($request->get('admin_snowcap_file') !== null) {
                $uploadForm->bindRequest($request);
                if ($uploadForm->isValid()) {
                    /** @var $em \Doctrine\ORM\EntityManager */
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($file);
                    $em->flush();

                    $extraParameters = array('url' => $file->getPath());
                }
            }
        }

        return array_merge(array('searchForm' => $searchForm->createView(), 'uploadForm' => $uploadForm->createView(), 'list' => $list, 'arguments' => $arguments), $extraParameters);
    }
}