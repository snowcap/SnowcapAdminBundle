<?php
namespace Snowcap\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Snowcap\AdminBundle\Environment;
use Snowcap\AdminBundle\Admin\ContentAdmin;

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
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $list = $admin->getDatalist();
        $request = $this->getRequest();
        if (($page = $request->get('page')) !== null) {
            $list->setPage($page);
        }
        $searchForm = $admin->getSearchForm();
        $templateParams = array(
            'admin' => $admin,
            'list' => $list,
        );
        if ($searchForm !== null) {
            $searchForm->bindRequest($this->get('request'));
            $searchData = $searchForm->getData();
            $list->filterData(array_filter($searchData));
            $templateParams['searchForm'] = $searchForm->createView();
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
        $request = $this->get('request');
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $entity = $admin->buildEntity();
        $forms = $this->createForm('form');
        $form = $admin->getForm($entity);
        $forms->add($form);
        if ($admin->isTranslatable()) {
            $this->get('snowcap_admin')->setWorkingLocale($locale);
            $translationEntity = $admin->buildTranslationEntity($entity, $locale);
            $translationForm = $admin->getTranslationForm($translationEntity);
            $forms->add($translationForm);
        }
        if ('POST' === $request->getMethod()) {
            $forms->bindRequest($request);
            if ($admin->isTranslatable()) {
                $admin->attachTranslation($entity, $translationEntity);
            }
            if ($forms->isValid()) {
                $admin->saveEntity($entity);
                $this->setFlash('success', 'content.create.flash.success');
                return $this->redirect($this->generateUrl('snowcap_admin_content_index', array('code' => $code)));
            }
        }
        $templateParams = array(
            'admin' => $admin,
            'entity' => $entity,
            'forms' => $forms->createView(),
            'form_template' => $this->getTemplate('SnowcapAdminBundle:Content:form.html.twig', $code),
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
        $request = $this->get('request');
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $entity = $admin->findEntity($id);
        $forms = $this->createForm('form');
        $form = $admin->getForm($entity);
        $forms->add($form);
        if ($admin->isTranslatable()) {
            $this->get('snowcap_admin')->setWorkingLocale($locale);
            $translationEntity = $admin->findTranslationEntity($entity, $locale);
            $translationForm = $admin->getTranslationForm($translationEntity);
            $forms->add($translationForm);
        }
        if ('POST' === $request->getMethod()) {
            $forms->bindRequest($request);
            if ($admin->isTranslatable()) {
                $admin->attachTranslation($entity, $translationEntity);
            }
            if ($forms->isValid()) {
                $admin->saveEntity($entity);
                $this->setFlash('success', 'content.update.flash.success');
                return $this->redirect($this->generateUrl('snowcap_admin_content_index', array('code' => $code)));
            }
        }
        $templateParams = array(
            'admin' => $admin,
            'entity' => $entity,
            'forms' => $forms->createView(),
            'form_template' => $this->getTemplate('SnowcapAdminBundle:Content:form.html.twig', $code),
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
        $admin->deleteEntity($id);
        $this->setFlash('success', 'content.delete.flash.success');
        return $this->redirect($this->generateUrl('snowcap_admin_content_index', array('code' => $code)));
    }
}