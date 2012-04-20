<?php

namespace Snowcap\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
class WysiwygController extends Controller
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

        /** @var $list \Snowcap\AdminBundle\Datalist\ContentDatalist */
        $list = $this->get('snowcap_admin.datalist_factory')->create('thumbnail', 'browser');
        $list
            ->add('path', 'image')
            ->add('name', 'label')
            ->add('tags', 'description')
        ;

        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('f')->from('SnowcapAdminBundle:File', 'f');

        $list->setQueryBuilder($queryBuilder);
        //$list->paginate(2);

        if ('POST' === $request->getMethod() && $request->get('search') !== null) {
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

        return array('searchForm' => $searchForm->createView(), 'list' => $list, 'arguments' => $arguments);
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

        if ('POST' === $request->getMethod() && $request->get('admin_snowcap_file') !== null) {
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