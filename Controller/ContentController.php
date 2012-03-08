<?php
namespace Snowcap\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Snowcap\AdminBundle\Admin\ContentAdmin;
use Snowcap\AdminBundle\Form\ContentType;

/**
 * This controller provides basic CRUD capabilities for content models
 *
 */
class ContentController extends BaseController
{
    /**
     * Content homepage (listing)
     *
     * @Template()
     *
     * @param string $type
     * @return mixed
     */
    public function indexAction($code)
    {
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $list = $admin->getList();
        $searchForm = $admin->getSearchForm();
        $vars = array(
            'admin' => $admin,
            'list' => $list,
        );
        if($searchForm !== null) {
            $searchForm->bindRequest($this->get('request'));
            $searchData = $searchForm->getData();
            $list->filterData(array_filter($searchData));
            $vars['searchForm'] =  $searchForm->createView();
        }
        return $vars;
    }

    /**
     * Create a new content entity
     *
     * @Template("SnowcapAdminBundle:Content:create.html.twig")
     *
     * @param string $code
     * @return mixed
     */
    public function createAction($code)
    {
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $entity = $admin->getBlankEntity();
        $request = $this->get('request');
        $form = $admin->getForm($entity);
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $admin->saveEntity($entity);
                $this->setFlash('success', 'content.create.flash.success');
                return $this->redirect($this->generateUrl('snowcap_admin_content_index', array('code' => $code)));
            }
        }
        return array(
            'admin' => $admin,
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Update an existing content entity
     *
     * @Template("SnowcapAdminBundle:Content:update.html.twig")
     *
     * @param string $code
     * @param int $id
     * @return mixed
     */
    public function updateAction($code, $id)
    {
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $entity = $admin->findEntity($id);
        $request = $this->get('request');
        $form = $admin->getForm($entity);
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $admin->saveEntity($entity);
                $this->setFlash('success', 'content.update.flash.success');
                return $this->redirect($this->generateUrl('snowcap_admin_content_index', array('code' => $code)));
            }
        }
        return array(
            'admin' => $admin,
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Deletes a content entity.
     *
     * @param string $type
     * @param int $id
     * @return mixed
     */
    public function deleteAction($code, $id)
    {
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $admin->deleteEntity($id);
        $this->setFlash('success', 'content.delete.flash.success');
        return $this->redirect($this->generateUrl('snowcap_admin_content_index', array('code' => $code)));
    }
}