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
     * @Template()
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
        $vars = array(
            'admin' => $admin,
            'list' => $list,
        );
        if ($searchForm !== null) {
            $searchForm->bindRequest($this->get('request'));
            $searchData = $searchForm->getData();
            $list->filterData(array_filter($searchData));
            $vars['searchForm'] = $searchForm->createView();
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
            'form_template' => $this->getTemplate('form', $code),
        );
        if ($admin->isTranslatable()) {
            $templateParams['content_locale'] = $locale;
        }
        return $templateParams;
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
            $translationEntity = $admin->findTranslationEntity($entity, $locale);
            $translationForm = $admin->getTranslationForm($translationEntity);
            $forms->add($translationForm);
        }
        $form = $admin->getForm($entity);
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
            'form_template' => $this->getTemplate('form', $code),
        );
        if ($admin->isTranslatable()) {
            $templateParams['content_locale'] = $locale;
        }
        return $templateParams;
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

    protected function getTemplate($templateName, $code)
    {
        $bundle = $this->get('snowcap_admin')->getBundle();
        // TODO check if available in bundle, otherwise use the default one
        // $template = $bundle . ':Content:' . ucfirst($code) . '/'. $templateName . '.html.twig';
        $template = "SnowcapAdminBundle:Content:form.html.twig";
        return $template;
    }
}