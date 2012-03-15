<?php
namespace Snowcap\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Util\PropertyPath;


use Snowcap\AdminBundle\Admin\ContentAdmin;

/**
 * This controller provides basic CRUD capabilities for content models
 *
 */
class InlineContentController extends Controller
{
    public function selectOrCreateAction($code)
    {
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $return = array(
            'html' => $this->renderView('SnowcapAdminBundle:InlineContent:select_or_create.html.twig', array(
                'admin' => $admin,
            ))
        );
        return new Response(json_encode($return), 200, array('content-type' => 'text/json'));
    }

    /**
     * Create a new content entity
     *
     * @param string $code
     * @param string $property
     * @return mixed
     */
    public function createAction($code)
    {
        $extensions = $this->get('snowcap_admin.twig');
        $twig = $this->get('twig');
        $extensions->initRuntime($twig);

        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $entity = $admin->getBlankEntity();
        $request = $this->get('request');
        $form = $admin->getForm($entity);
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $admin->saveEntity($entity);

                $return = array(
                    'entity_id' => $entity->getId(),
                    'preview' => $extensions->renderPreview($entity, $admin),
                );
                return new Response(json_encode($return), 201, array('content-type' => 'text/json'));
            }
        }

        return $this->render('SnowcapAdminBundle:InlineContent:create.html.twig', array(
            'admin' => $admin,
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    public function selectAction($code)
    {
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $list = $admin->getList();

        return $this->render('SnowcapAdminBundle:InlineContent:select.html.twig', array(
            'admin' => $admin,
            'list' => $list,
        ));

    }

}