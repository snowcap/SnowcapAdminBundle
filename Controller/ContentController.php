<?php

namespace Snowcap\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Util\PropertyPath;
use Symfony\Component\Form\Form;

use Snowcap\CoreBundle\Util\String;
use Snowcap\AdminBundle\Admin\ContentAdmin;
use Snowcap\AdminBundle\Datalist\Datasource\DoctrineORMDatasource;

/**
 * This controller provides basic CRUD capabilities for content models
 *
 */
class ContentController extends BaseController
{
    /**
     * Display the index screen (listing)
     *
     */
    public function indexAction(Request $request, ContentAdmin $admin)
    {
        $datalist = $admin->getDatalist();
        $datasource = new DoctrineORMDatasource($admin->getQueryBuilder());
        $datalist->setDatasource($datasource);
        $datalist->bind($request);

        $templateParams = array(
            'admin' => $admin,
            'datalist' => $datalist
        );

        return $this->render('SnowcapAdminBundle:' . String::camelize($admin->getAlias()) . ':index.html.twig', $templateParams);
    }

    /**
     * Display the detail screen
     *
     */
    public function viewAction(Request $request, ContentAdmin $admin)
    {
        $entity = $admin->findEntity($request->attributes->get('id'));

        return $this->render(
            $this->getTemplate('SnowcapAdminBundle:Content:view.html.twig', $admin->getAlias()),
            array('admin' => $admin, 'entity' => $entity)
        );
    }

    /**
     * Create a new content entity
     *
     */
    public function createAction(Request $request, ContentAdmin $admin)
    {
        $entity = $admin->buildEntity();
        $form = $admin->getForm();
        $form->setData($entity);

        if ($request->isMethod('POST')) {
            try {
                $this->save($admin, $form, $entity);
                $this->setFlash('success', 'content.create.flash.success');
                $redirectUrl = $this->getRequest()->get('saveMode') === ContentAdmin::SAVEMODE_CONTINUE ?
                    $this->getRoutingHelper()->generateUrl($admin, 'update', array('id' => $entity->getId())) :
                    $this->getRoutingHelper()->generateUrl($admin, 'index');

                return $this->redirect($redirectUrl);
            }
            catch(\Exception $e) {
                $this->setFlash('error', 'content.create.flash.error');
            }
        }
        $templateParams = array(
            'admin' => $admin,
            'entity' => $entity,
            'form' => $form->createView(),
        );

        return $this->render('SnowcapAdminBundle:' . String::camelize($admin->getAlias()) . ':create.html.twig', $templateParams);
    }

    /**
     * Update an existing content entity
     *
     */
    public function updateAction(Request $request, ContentAdmin $admin)
    {
        $entity = $admin->findEntity($request->attributes->get('id'));

        if ($entity === null) {
            return $this->renderError('error.content.notfound', 404);
        }

        $form = $admin->getForm();
        $form->setData($entity);

        if ($request->isMethod('POST')) {
            try {
                $this->save($admin, $form, $entity);
                $this->setFlash('success', 'content.update.flash.success');
                $redirectUrl = $this->getRequest()->get('saveMode') === ContentAdmin::SAVEMODE_CONTINUE ?
                    $this->getRoutingHelper()->generateUrl($admin, 'update', array('id' => $entity->getId())) :
                    $this->getRoutingHelper()->generateUrl($admin, 'index');

                return $this->redirect($redirectUrl);
            }
            catch(\Exception $e) {
                $this->setFlash('error', 'content.update.flash.error');
            }
        }
        $templateParams = array(
            'admin' => $admin,
            'entity' => $entity,
            'form' => $form->createView(),
        );

        return $this->render('SnowcapAdminBundle:' . String::camelize($admin->getAlias()) . ':update.html.twig', $templateParams);
    }

    /**
     * Delete a content entity
     *
     */
    public function deleteAction(Request $request, ContentAdmin $admin)
    {
        $entity = $admin->findEntity($request->attributes->get('id'));
        $admin->deleteEntity($entity);
        $admin->flush();
        // TODO: reactivate using event dispatcher
        // $this->get('snowcap_admin.logger')->logContent(Logger::ACTION_DELETE, $admin, $entity, $this->getRequest()->getLocale());
        $this->setFlash('success', 'content.delete.flash.success');

        return $this->redirect($this->getRoutingHelper()->generateUrl($admin, 'index'));
    }

    /**
     * Create a new content entity through ajax modal
     *
     */
    public function modalCreateAction(Request $request, ContentAdmin $admin) {
        $entity = $admin->buildEntity();
        $form = $admin->getForm();
        $form->setData($entity);

        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $admin->saveEntity($entity);
                $admin->flush();

                $json = array(
                    'result' => array($entity->getId(), $admin->getEntityName($entity))
                );

                return new Response(json_encode($json), 201);
            } else {

            }
        }
        $templateParams = array(
            'admin' => $admin,
            'entity' => $entity,
            'form' => $form->createView(),
            'form_template' => $this->getTemplate('SnowcapAdminBundle:Content:modalForm.html.twig', $admin->getAlias()),
            'form_theme_template' => $this->getTemplate('SnowcapAdminBundle:Form:form_layout.html.twig'),
        );

        return $this->render('SnowcapAdminBundle:' . String::camelize($admin->getAlias()) . ':modalCreate.html.twig', $templateParams);
    }

    /**
     * Render a json array of entity values and text (to be used in autocomplete widgets)
     *
     */
    public function autocompleteListAction(Request $request, ContentAdmin $admin, $query, $where, $property) {
        $qb = $admin->getQueryBuilder();
        $results = $qb
            ->andWhere($where)
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();

        $flattenedResults = array();
        $propertyPath = new PropertyPath($property);
        foreach($results as $result) {
            $flattenedResults[] = array($result->getId(), $propertyPath->getValue($result));
        }
        $json = array('result' => $flattenedResults);

        return new Response(json_encode($json));
    }

    /**
     * Save a content entity
     *
     * @param \Snowcap\AdminBundle\Admin\ContentAdmin $admin
     * @param \Symfony\Component\Form\Form $form
     * @param object $entity
     * @param string $successFlash
     * @param string $errorFlash
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function save(ContentAdmin $admin, Form $form, $entity) {
        $form->bind($this->getRequest());
        if ($form->isValid()) {
            $admin->saveEntity($entity);
            $admin->flush();
        } else {
            throw new \Exception('could not save');
        }
    }

    /**
     * @return \Snowcap\AdminBundle\Routing\Helper\ContentRoutingHelper
     */
    protected function getRoutingHelper()
    {
        return $this->get('snowcap_admin.routing_helper_content');
    }
}