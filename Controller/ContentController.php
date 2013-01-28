<?php

namespace Snowcap\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Util\PropertyPath;

use Snowcap\AdminBundle\Admin\ContentAdmin;
use Snowcap\AdminBundle\Datalist\Datasource\DoctrineORMDatasource;

/**
 * This controller provides basic CRUD capabilities for content models
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
            'datalist' => $datalist,
            'reorder' => false, // TODO: reimplement reorder
            'form_theme_template' => $this->getTemplate('SnowcapAdminBundle:Form:form_layout.html.twig')
        );

        return $this->render(
            $this->getTemplate("SnowcapAdminBundle:Content:index.html.twig", $admin->getAlias()),
            $templateParams
        );
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
     */
    public function createAction(Request $request, ContentAdmin $admin)
    {
        $entity = $admin->buildEntity();
        $form = $admin->getForm();
        $form->setData($entity);

        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $admin->saveEntity($entity);
                $admin->flush();
                $this->setFlash('success', 'content.create.flash.success');
                $saveMode = $this->getRequest()->get('saveMode');
                if ($saveMode === ContentAdmin::SAVEMODE_CONTINUE) {
                    $redirectUrl = $this->getRoutingHelper()->generateUrl(
                        $admin,
                        'update',
                        array('id' => $entity->getId())
                    );
                } else {
                    $redirectUrl = $this->getRoutingHelper()->generateUrl($admin, 'index');
                }

                return $this->redirect($redirectUrl);
            } else {
                $this->setFlash('error', 'content.create.flash.error');
            }
        }
        $templateParams = array(
            'admin' => $admin,
            'entity' => $entity,
            'form' => $form->createView(),
            'form_template' => $this->getTemplate('SnowcapAdminBundle:Content:form.html.twig', $admin->getAlias()),
            'form_theme_template' => $this->getTemplate('SnowcapAdminBundle:Form:form_layout.html.twig'),
        );

        return $this->render(
            $this->getTemplate('SnowcapAdminBundle:Content:create.html.twig', $admin->getAlias()),
            $templateParams
        );
    }

    public function modalCreateAction(Request $request, ContentAdmin $admin) {
        $entity = $admin->buildEntity();
        $form = $admin->getForm();
        $form->setData($entity);

        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $admin->saveEntity($entity);
                $admin->flush();

                return new Response(json_encode(array('val' => $entity->getId(), 'html' => $admin->getEntityName($entity))), 201);
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

        return $this->render(
            $this->getTemplate('SnowcapAdminBundle:Content:modalCreate.html.twig', $admin->getAlias()),
            $templateParams
        );
    }

    /**
     * Render a json array of entity values and text (to be used in autocomplete widgets)
     *
     */
    public function autocompleteListAction(Request $request, ContentAdmin $admin, $query, $property) {
        $results = $admin->getQueryBuilder()
            ->andWhere('e.name LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();

        $flattenedResults = array();
        $propertyPath = new PropertyPath($property);
        foreach($results as $result) {
            $flattenedResults[] = array($result->getId(), $propertyPath->getValue($result));
        }

        return new Response(json_encode($flattenedResults));
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

        $form = $admin->getFormWithData($entity);

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $admin->saveEntity($entity);
                $admin->flush();
                // TODO: reactivate using event dispatcher
                //$this->get('snowcap_admin.logger')->logContent(Logger::ACTION_UPDATE, $admin, $entity, $locale);
                $this->setFlash('success', 'content.update.flash.success');
                $saveMode = $this->getRequest()->get('saveMode');
                if ($saveMode === ContentAdmin::SAVEMODE_CONTINUE) {
                    $redirectUrl = $this->getRoutingHelper()->generateUrl(
                        $admin,
                        'update',
                        array('id' => $entity->getId())
                    );
                } else {
                    $redirectUrl = $this->getRoutingHelper()->generateUrl($admin, 'index');
                }

                return $this->redirect($redirectUrl);
            } else {
                $this->setFlash('error', 'content.update.flash.error');
            }
        }
        $templateParams = array(
            'admin' => $admin,
            'entity' => $entity,
            'form' => $form->createView(),
            'form_template' => $this->getTemplate('SnowcapAdminBundle:Content:form.html.twig', $admin->getAlias()),
            'form_theme_template' => $this->getTemplate('SnowcapAdminBundle:Form:form_layout.html.twig'),
        );

        return $this->render(
            $this->getTemplate('SnowcapAdminBundle:Content:update.html.twig', $admin->getAlias()),
            $templateParams
        );
    }

    /**
     * Delete a content entity
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
     * @return \Snowcap\AdminBundle\Routing\Helper\ContentRoutingHelper
     */
    protected function getRoutingHelper()
    {
        return $this->get('snowcap_admin.routing_helper_content');
    }
}