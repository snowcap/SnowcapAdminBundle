<?php
namespace Snowcap\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Snowcap\AdminBundle\Environment;
use Snowcap\AdminBundle\Admin\ContentAdmin;
use Snowcap\AdminBundle\Admin\ReorderableAdminInterface;
use Snowcap\AdminBundle\Admin\CannotDeleteException;

use Snowcap\AdminBundle\Logger\Logger;

/**
 * This controller provides basic CRUD capabilities for content models
 *
 */
class ContentController extends BaseController
{
    /**
     * Content homepage (listing)
     *
     * @param string $type
     * @return mixed
     */
    public function indexAction($code)
    {
        $locale = $this->getRequest()->getLocale();
        $this->get('snowcap_admin')->setWorkingLocale($locale);
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $list = $admin->getDatalist();
        $request = $this->getRequest();
        if (($page = $request->get('page')) !== null) {
            $list->setPage($page);
        }

        $templateParams = array(
            'admin' => $admin,
            'list' => $list,
            'reorder' => $admin instanceof ReorderableAdminInterface
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
        return $this->render($this->getTemplate("SnowcapAdminBundle:Content:index.html.twig", $code), $templateParams);
    }

    /**
     * Create a new content entity
     *
     * @param string $code
     * @return mixed
     */
    public function createAction($code, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->getRequest()->getLocale();
        }
        $this->get('snowcap_admin')->setWorkingLocale($locale);
        $request = $this->get('request');
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $entity = $admin->buildEntity();
        $forms = $this->createForm('form');
        $form = $admin->getForm($entity);
        $forms->add($form);
        if ($admin->isTranslatable()) {
            $translationEntity = $admin->buildTranslationEntity($entity, $locale);
            $translationForm = $admin->getTranslationForm($translationEntity);
            $forms->add($translationForm);
        }
        if ('POST' === $request->getMethod()) {
            $forms->bindRequest($request);
            if ($forms->isValid()) {
                $admin->saveEntity($entity);
                if ($admin->isTranslatable()) {
                    $entity->getTranslations()->set($translationEntity->getLocale(), $translationEntity);
                    $admin->saveTranslationEntity($entity, $translationEntity);
                }
                $admin->flush();
                $this->get('snowcap_admin.logger')->logContent(Logger::ACTION_CREATE, $admin, $entity, $locale);
                $this->setFlash('success', 'content.create.flash.success');
                $saveMode = $this->getRequest()->get('saveMode');
                if ($saveMode === ContentAdmin::SAVEMODE_CONTINUE) {
                    return $this->redirect($this->generateUrl('snowcap_admin_content_update', array('code' => $code, 'id' => $entity->getId(), 'locale' => $locale)));
                }
                else {
                    return $this->redirect($this->generateUrl('snowcap_admin_content_index', array('code' => $code)));
                }
            } else {
                $this->setFlash('error', 'content.create.flash.error');
            }
        }
        $templateParams = array(
            'admin' => $admin,
            'entity' => $entity,
            'forms' => $forms->createView(),
            'form_template' => $this->getTemplate('SnowcapAdminBundle:Content:form.html.twig', $code),
            'form_theme_template' => $this->getTemplate('SnowcapAdminBundle:Form:form_layout.html.twig')
        );
        if ($admin->isTranslatable()) {
            $templateParams['content_locale'] = $locale;
        }

        return $this->render($this->getTemplate('SnowcapAdminBundle:Content:create.html.twig', $code), $templateParams);
    }

    /**
     * Update an existing content entity
     *
     * @param string $code
     * @param int $id
     * @return mixed
     */
    public function updateAction($code, $id, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->getRequest()->getLocale();
        }
        $this->get('snowcap_admin')->setWorkingLocale($locale);
        $request = $this->get('request');
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $entity = $admin->findEntity($id);

        if($entity === null) {
            return $this->renderError('error.content.notfound', 404);
        }

        $forms = $this->createForm('form');
        $form = $admin->getForm($entity);
        $forms->add($form);
        if ($admin->isTranslatable()) {
            $translationEntity = $admin->findTranslationEntity($entity, $locale);
            $translationForm = $admin->getTranslationForm($translationEntity);
            $forms->add($translationForm);
        }
        if ('POST' === $request->getMethod()) {
            $forms->bindRequest($request);
            if ($forms->isValid()) {
                $admin->saveEntity($entity);
                if ($admin->isTranslatable()) {
                    $entity->getTranslations()->set($translationEntity->getLocale(), $translationEntity);
                    $admin->saveTranslationEntity($entity, $translationEntity);
                }

                $this->get('snowcap_admin.logger')->logContent(Logger::ACTION_UPDATE, $admin, $entity, $locale);

                $admin->flush();
                $this->setFlash('success', 'content.update.flash.success');
                $saveMode = $this->getRequest()->get('saveMode');
                if ($saveMode === ContentAdmin::SAVEMODE_CONTINUE) {
                    return $this->redirect($this->generateUrl('snowcap_admin_content_update', array('code' => $code, 'id' => $id, 'locale' => $locale)));
                }
                else {
                    return $this->redirect($this->generateUrl('snowcap_admin_content_index', array('code' => $code)));
                }
            } else {
                $this->setFlash('error', 'content.update.flash.error');
            }
        }
        $templateParams = array(
            'admin' => $admin,
            'entity' => $entity,
            'forms' => $forms->createView(),
            'form_template' => $this->getTemplate('SnowcapAdminBundle:Content:form.html.twig', $code),
            'form_theme_template' => $this->getTemplate('SnowcapAdminBundle:Form:form_layout.html.twig')
        );
        if ($admin->isTranslatable()) {
            $templateParams['content_locale'] = $locale;
        }

        return $this->render($this->getTemplate('SnowcapAdminBundle:Content:update.html.twig', $code), $templateParams);
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
        try {
            $entity = $admin->findEntity($id);
            $this->get('snowcap_admin.logger')->logContent(Logger::ACTION_DELETE, $admin, $entity, $this->getRequest()->getLocale());
            $admin->deleteEntity($entity);
            $admin->flush();
            $this->setFlash('success', 'content.delete.flash.success');
        }
        catch(CannotDeleteException $e) {
            switch($e->getCode()) {
                case CannotDeleteException::HAS_CHILDREN:
                    $this->setFlash('error', 'content.delete.flash.error_has_children');
                    break;
            }
        }

        return $this->redirect($this->generateUrl('snowcap_admin_content_index', array('code' => $code)));
    }
}