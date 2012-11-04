<?php

namespace Snowcap\AdminBundle\Controller;

use Snowcap\AdminBundle\Admin\ContentAdmin;
use Snowcap\AdminBundle\Admin\CannotDeleteException;

/**
 * This controller provides basic CRUD capabilities for content models
 */
class ContentController extends BaseController
{
    /**
     * Display the index screen (listing)
     */
    public function indexAction(ContentAdmin $admin)
    {
        $list = $admin->getDatalist();
        $request = $this->getRequest();
        if (($page = $request->get('page')) !== null) {
            $list->setPage($page);
        }

        $templateParams = array(
            'admin' => $admin,
            'list' => $list,
            'reorder' => false // TODO: reimplement reorder
        );

        $contentForm = $this->createForm('form', array(), array(
            'virtual' => true,
            'csrf_protection' => false,
        ));
        $searchForm = $admin->getSearchForm();
        if($searchForm !== null) {
            $contentForm->add($searchForm);
        }
        $filterForm = $admin->getFilterForm();
        if($filterForm !== null) {
            $contentForm->add($filterForm);
        }

        if ($contentForm->hasChildren()) {
            $contentForm->bindRequest($this->get('request'));
            $searchData = $contentForm->getData();
            $list->filterData($searchData);
            $templateParams['contentForm'] = $contentForm->createView();
            $templateParams['form_theme_template'] = $this->getTemplate('SnowcapAdminBundle:Form:widgets.html.twig');
        }

        return $this->render($this->getTemplate("SnowcapAdminBundle:Content:index.html.twig", $admin->getAlias()), $templateParams);
    }

    /**
     * Create a new content entity
     */
    public function createAction(ContentAdmin $admin)
    {
        $request = $this->get('request');
        $entity = $admin->buildEntity();
        $forms = $this->createForm('form');
        $form = $admin->getForm($entity);
        $forms->add($form);
        if ('POST' === $request->getMethod()) {
            $forms->bind($request);
            if ($forms->isValid()) {
                $admin->saveEntity($entity);
                $admin->flush();
                // TODO: reactivate using event dispatcher
                //$this->get('snowcap_admin.logger')->logContent(Logger::ACTION_CREATE, $admin, $entity, $locale);
                $this->setFlash('success', 'content.create.flash.success');
                $saveMode = $this->getRequest()->get('saveMode');
                if ($saveMode === ContentAdmin::SAVEMODE_CONTINUE) {
                    $redirectUrl = $this->getRoutingHelper()->generateUrl($admin, 'update', array('id' => $entity->getId()));
                }
                else {
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
            'forms' => $forms->createView(),
            'form_template' => $this->getTemplate('SnowcapAdminBundle:Content:form.html.twig', $admin->getAlias()),
            'form_theme_template' => $this->getTemplate('SnowcapAdminBundle:Form:form_layout.html.twig'),
            'form_action' => $this->getRoutingHelper()->generateUrl($admin, 'create'),
        );

        return $this->render($this->getTemplate('SnowcapAdminBundle:Content:create.html.twig', $admin->getAlias()), $templateParams);
    }

    /**
     * Update an existing content entity
     */
    public function updateAction(ContentAdmin $admin, $id)
    {
        $request = $this->get('request');
        $entity = $admin->findEntity($id);

        if($entity === null) {
            return $this->renderError('error.content.notfound', 404);
        }

        $forms = $this->createForm('form');
        $form = $admin->getForm($entity);
        $forms->add($form);
        if ('POST' === $request->getMethod()) {
            $forms->bindRequest($request);
            if ($forms->isValid()) {
                $admin->saveEntity($entity);
                $admin->flush();
                // TODO: reactivate using event dispatcher
                //$this->get('snowcap_admin.logger')->logContent(Logger::ACTION_UPDATE, $admin, $entity, $locale);
                $this->setFlash('success', 'content.update.flash.success');
                $saveMode = $this->getRequest()->get('saveMode');
                if ($saveMode === ContentAdmin::SAVEMODE_CONTINUE) {
                    $redirectUrl = $this->getRoutingHelper()->generateUrl($admin, 'update', array('id' => $entity->getId()));
                }
                else {
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
            'forms' => $forms->createView(),
            'form_template' => $this->getTemplate('SnowcapAdminBundle:Content:form.html.twig', $admin->getAlias()),
            'form_theme_template' => $this->getTemplate('SnowcapAdminBundle:Form:form_layout.html.twig'),
            'form_action' => $this->getRoutingHelper()->generateUrl($admin, 'update', array('id' => $entity->getId())),
        );

        return $this->render($this->getTemplate('SnowcapAdminBundle:Content:update.html.twig', $admin->getAlias()), $templateParams);
    }

    /**
     * Delete a content entity
     */
    public function deleteAction(ContentAdmin $admin, $id)
    {
        try {
            $entity = $admin->findEntity($id);
            $admin->deleteEntity($entity);
            $admin->flush();
            // TODO: reactivate using event dispatcher
            // $this->get('snowcap_admin.logger')->logContent(Logger::ACTION_DELETE, $admin, $entity, $this->getRequest()->getLocale());
            $this->setFlash('success', 'content.delete.flash.success');
        }
        catch(CannotDeleteException $e) {
            switch($e->getCode()) {
                case CannotDeleteException::HAS_CHILDREN:
                    $this->setFlash('error', 'content.delete.flash.error_has_children');
                    break;
            }
        }

        return $this->redirect($this->getRoutingHelper()->generateUrl($admin, 'index'));
    }

    /**
     * @return \Snowcap\AdminBundle\Routing\Helper\ContentRoutingHelper
     */
    private function getRoutingHelper()
    {
        return $this->get('snowcap_admin.routing_helper_content');
    }
}