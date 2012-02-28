<?php
namespace Snowcap\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

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
     * @return mixed
     */
    public function createAction($code)
    {
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $entity = $admin->getBlankEntity();
        $request = $this->get('request');
        $form = $admin->getForm($entity);
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $admin->saveEntity($entity);
                $return = array(
                    'inline_id' => $entity->getId(),
                );
                return new Response(json_encode($return), 201, array('content-type' => 'text/json'));
            }
        }

        $return = array(
            'html' => $this->renderView('SnowcapAdminBundle:Inline:create.html.twig', array(
                'admin' => $admin,
                'entity' => $entity,
                'form' => $form->createView(),
            ))
        );
        return new Response(json_encode($return), 200, array('content-type' => 'text/json'));
    }
}