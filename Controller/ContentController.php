<?php
namespace Snowcap\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Snowcap\AdminBundle\Admin\Content as ContentAdmin;
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
        $admin = $this->get('snowcap_admin')->getAdmin($code); /* @var \Snowcap\AdminBundle\Admin\ContentAdmin $admin */
        $grid = $admin->getContentGrid();

        return array(
            'admin' => $admin,
            'grid' => $grid,
        );
    }

    /**
     * Create a new content entity
     *
     * @Route("/content/{code}/create", name="content_create")
     * @Template("SnowcapAdminBundle:Content:create.html.twig")
     *
     * @param string $code
     * @return mixed
     */
    public function createAction($code)
    {
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $em = $this->get('doctrine')->getEntityManager();
        $entityName = $admin->getParam('entity_class');
        $entity = new $entityName();
        $request = $this->get('request');
        $form = $admin->getContentForm($entity);
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();
                return $this->redirect($this->generateUrl('content', array('code' => $code)));
            }
        }
        return array(
            'admin' => $admin,
            'entity' => $entity,
            'form_view' => $form->createView(),
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
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $this->findEntity($id, $admin);
        $request = $this->get('request');
        $form = $admin->getContentForm($entity);
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();
                return $this->redirect($this->generateUrl('content', array('code' => $code)));
            }
        }
        return array(
            'admin' => $admin,
            'entity' => $entity,
            'form_view' => $form->createView(),
        );
    }

    /**
     * Mass update an existing content entity
     *
     * @Route("/content/{code}/mass_update", name="content_mass_update")
     *
     * @param string $type
     * @return mixed
     */
    public function massUpdateAction($code)
    {
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $em = $this->getDoctrine()->getEntityManager();
        $grid = $admin->getContentGrid();
        $form = $grid->getOrderForm();
        $request = $this->get('request');
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                foreach($grid->getData() as $entity) {
                    foreach($form->get($grid->generateEntityKey($entity))->get($entity->getId())->getData() as $dataKey => $dataValue) {
                        call_user_func(array($entity, 'set' . ucfirst($dataKey)), $dataValue);
                    }
                    $em->persist($entity);
                }
                $em->flush();
                return $this->redirect($this->generateUrl('content', array('section' => $section)));
            }
        }
    }

    /**
     * Deletes a content entity.
     *
     * @Route("/content/{section}/delete/{id}", name="content_delete")
     *
     * @param string $type
     * @param int $id
     * @return mixed
     */
    public function deleteAction($section, $id)
    {
        $admin = $this->get('snowcap_admin')->getAdmin($section);
        $entity = $this->findEntity($id, $admin);
        $em = $this->get('doctrine')->getEntityManager();
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('content', array('section' => $section)));
    }

    /**
     * Find an entity managed by the provided admin class
     *
     * @throws \Symfony\Bundle\FrameworkBundle\Controller\NotFoundHttpException
     * @param int $id
     * @param \Snowcap\AdminBundle\Admin\Content $admin
     * @return \Object
     */
    private function findEntity($id, ContentAdmin $admin)
    {
        $entity = $this->get('doctrine')->getEntityManager()->getRepository($admin->getParam('entity_class'))->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find content entity.');
        }
        return $entity;
    }
}