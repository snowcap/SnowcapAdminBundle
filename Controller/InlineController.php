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
class InlineController extends Controller
{
    /**
     * Create a new content entity
     *
     * @param string $code
     * @param string $property
     * @return mixed
     */
    public function createAction($code, $property)
    {
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $entity = $admin->getBlankEntity();
        $request = $this->get('request');
        $form = $admin->getForm($entity);
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $admin->saveEntity($entity);

                $propertyPath = new PropertyPath($property);
                $value = $propertyPath->getValue($entity);

                $return = array(
                    'entity_id' => $entity->getId(),
                    'entity_property' => $value,
                );
                return new Response(json_encode($return), 201, array('content-type' => 'text/json'));
            }
        }

        $return = array(
            'html' => $this->renderView('SnowcapAdminBundle:Inline:create.html.twig', array(
                'admin' => $admin,
                'entity' => $entity,
                'form' => $form->createView(),
                'property' => $property,
            ))
        );
        return new Response(json_encode($return), 200, array('content-type' => 'text/json'));
    }

    public function selectAction($code)
    {
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $grid = $admin->getContentGrid();

        $return = array(
            'html' => $this->renderView('SnowcapAdminBundle:Inline:select.html.twig', array(
                'admin' => $admin,
                'grid' => $grid,
            ))
        );
        return new Response(json_encode($return), 200, array('content-type' => 'text/json'));
    }

    public function previewAction($code, $id)
    {
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $preview = $admin->getPreview($id);

        $return = array(
            'html' => $this->renderView('SnowcapAdminBundle:Inline:preview.html.twig', array(
                'admin' => $admin,
                'preview' => $preview,
            ))
        );
        return new Response(json_encode($return), 200, array('content-type' => 'text/json'));
    }
}