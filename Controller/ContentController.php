<?php

namespace Snowcap\AdminBundle\Controller;

use Snowcap\AdminBundle\Admin\AdminInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;

use Snowcap\CoreBundle\Util\String;
use Snowcap\AdminBundle\Admin\ContentAdmin;
use Snowcap\AdminBundle\Datalist\Datasource\DoctrineORMDatasource;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContext;

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
        $this->secure($admin, 'ADMIN_CONTENT_LIST');

        $datalist = $admin->getDatalist();
        $datasource = new DoctrineORMDatasource($admin->getQueryBuilder());
        $datalist->setDatasource($datasource);
        $datalist->bind($request);

        return $this->render('SnowcapAdminBundle:' . String::camelize($admin->getAlias()) . ':index.html.twig', array(
            'admin' => $admin,
            'datalist' => $datalist
        ));
    }

    /**
     * Display the detail screen
     *
     */
    public function viewAction(Request $request, ContentAdmin $admin)
    {
        $entity = $admin->findEntity($request->attributes->get('id'));
        $this->secure($admin, 'ADMIN_CONTENT_VIEW', $entity);

        return $this->render('SnowcapAdminBundle:' . String::camelize($admin->getAlias()) . ':view.html.twig', array(
            'admin' => $admin,
            'entity' => $entity
        ));
    }

    /**
     * Create a new content entity
     *
     */
    public function createAction(Request $request, ContentAdmin $admin)
    {
        $entity = $admin->buildEntity();
        $this->secure($admin, 'ADMIN_CONTENT_CREATE', $entity);

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
                $this->get('logger')->addError($e->getMessage());
            }
        }

        return $this->render('SnowcapAdminBundle:' . String::camelize($admin->getAlias()) . ':create.html.twig', array(
            'admin' => $admin,
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Create a new content entity through ajax modal
     *
     */
    public function modalCreateAction(Request $request, ContentAdmin $admin) {
        $entity = $admin->buildEntity();
        $this->secure($admin, 'ADMIN_CONTENT_CREATE', $entity);

        $form = $admin->getForm();
        $form->setData($entity);

        $status = 200;

        if ('POST' === $request->getMethod()) {
            try {
                $this->save($admin, $form, $entity);
                $result = array(
                    'entity_id' => $entity->getId(),
                    'entity_name' => $admin->getEntityName($entity)
                );

                return new JsonResponse(array('result' => $result), 201);
            }
            catch(\Exception $e) {
                //TODO: handle invalid ?
            }
        }

        $responseData = array(
            'content' =>   $this->renderView('SnowcapAdminBundle:' . String::camelize($admin->getAlias()) . ':modalCreate.html.twig', array(
                'admin' => $admin,
                'entity' => $entity,
                'form' => $form->createView(),
            ))
        );

        return new JsonResponse($responseData, $status);
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
        $this->secure($admin, 'ADMIN_CONTENT_UPDATE', $entity);

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
                $this->get('logger')->addError($e->getMessage());
            }
        }

        return $this->render('SnowcapAdminBundle:' . String::camelize($admin->getAlias()) . ':update.html.twig', array(
            'admin' => $admin,
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Update an existing content entity
     *
     */
    public function modalUpdateAction(Request $request, ContentAdmin $admin)
    {
        $entity = $admin->findEntity($request->attributes->get('id'));
        if ($entity === null) {
            return $this->renderError('error.content.notfound', 404);
        }
        $this->secure($admin, 'ADMIN_CONTENT_UPDATE', $entity);

        $form = $admin->getForm();
        $form->setData($entity);

        $status = 200;

        if ('POST' === $request->getMethod()) {
            try {
                $this->save($admin, $form, $entity);

                $result = array(
                    'entity_id' => $entity->getId(),
                    'entity_name' => $admin->getEntityName($entity),
                );

                return new JsonResponse(array(
                    'result' => $result,
                    'flashes' => $this->buildModalFlash('success', 'content.update.flash.success')
                ), 201);
            }
            catch(\Exception $e) {
                $status = 400;
            }
        }

        $responseData = array(
            'content' => $this->renderView('SnowcapAdminBundle:' . String::camelize($admin->getAlias()) . ':modalUpdate.html.twig', array(
                'admin' => $admin,
                'entity' => $entity,
                'form' => $form->createView(),
            ))
        );

        return new JsonResponse($responseData, $status);
    }

    /**
     * Delete a content entity
     *
     */
    public function deleteAction(Request $request, ContentAdmin $admin)
    {
        $entity = $admin->findEntity($request->attributes->get('id'));
        $this->secure($admin, 'ADMIN_CONTENT_DELETE', $entity);

        if($request->isXmlHttpRequest()) {
            return $this->modalDelete($request, $admin, $entity);
        }
        else {
            return $this->delete($request, $admin, $entity);
        }
    }

    /**
     * Handle AJAX delete (modal)
     *
     * @param Request $request
     * @param ContentAdmin $admin
     * @param $entity
     * @return JsonResponse
     */
    public function modalDelete(Request $request, ContentAdmin $admin, $entity)
    {
        if($request->isMethod('post')) {
            $admin->deleteEntity($entity);
            $this->setFlash('success', 'content.delete.flash.success');
            $result = array(
                'entity_id' => $entity->getId(),
                'entity_name' => $admin->getEntityName($entity)
            );
            $redirectUrl = $request->headers->get('referer');

            return new JsonResponse(array('result' => $result, 'redirect_url' => $redirectUrl), 301);
        }

        $content = $this->renderView(
            'SnowcapAdminBundle:' . String::camelize($admin->getAlias()) . ':modalDelete.html.twig',
            array(
                'admin' => $admin,
                'entity' => $entity,
            )
        );

        return new JsonResponse(array('content' => $content));
    }

    /**
     * Handle standard delete (no modal)
     *
     * @param Request $request
     * @param ContentAdmin $admin
     * @param $entity
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function delete(Request $request, ContentAdmin $admin, $entity)
    {
        if($request->isMethod('post')) {
            $admin->deleteEntity($entity);
            $this->setFlash('success', 'content.delete.flash.success');

            return $this->redirect($this->getRoutingHelper()->generateUrl($admin, 'index'));
        }

        return $this->render(
            'SnowcapAdminBundle:' . String::camelize($admin->getAlias()) . ':modalDelete.html.twig',
            array(
                'admin' => $admin,
                'entity' => $entity,
            )
        );
    }

    /**
     * Render a json array of entity values and text (to be used in autocomplete widgets)
     *
     */
    public function autocompleteListAction(ContentAdmin $admin, $where, $id_property, $property, $query) {
        $qb = $admin->getQueryBuilder();
        $results = $qb
            ->andWhere(base64_decode($where))
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();

        $flattenedResults = array();
        $accessor = PropertyAccess::getPropertyAccessor();
        foreach($results as $result) {
            $id = $accessor->getValue($result, $id_property);
            $value = $accessor->getValue($result, $property);
            $flattenedResults[] = array($id, $value);
        }

        return new JsonResponse(array('result' => $flattenedResults));
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
        } else {
            throw new \Exception('could not save');
        }
    }

    /**
     * @param AdminInterface $admin
     * @param mixed $attributes
     * @param object $object
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    protected function secure(AdminInterface $admin, $attributes, $object = null)
    {
        if(!is_array($attributes)) {
            $attributes = array($attributes);
        }
        $suffixedAttributes = array_map(function($attribute) use($admin) {
            return $attribute . '__' . strtoupper($admin->getAlias());
        }, $attributes);
        if(!$this->getSecurityContext()->isGranted($suffixedAttributes, $object)) {
            throw new AccessDeniedException();
        }
    }

    /**
     * @return \Snowcap\AdminBundle\Routing\Helper\ContentRoutingHelper
     */
    protected function getRoutingHelper()
    {
        return $this->get('snowcap_admin.routing_helper_content');
    }

    /**
     * @return SecurityContext
     */
    protected function getSecurityContext()
    {
        return $this->get('security.context');
    }
}