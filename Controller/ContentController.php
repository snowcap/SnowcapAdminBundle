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
class ContentController extends Controller
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
        /* @var \Snowcap\AdminBundle\Admin\ContentAdmin $admin */
        $grid = $admin->getContentGrid();

        return array(
            'admin' => $admin,
            'grid' => $grid,
        );
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
        return $this->redirect($this->generateUrl('snowcap_admin_content_index', array('code' => $code)));
    }
}