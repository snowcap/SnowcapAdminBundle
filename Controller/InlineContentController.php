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
        $entity = $admin->buildEntity();
        $request = $this->get('request');
        $form = $admin->getForm($entity);
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $admin->saveEntity($entity);
                $admin->flush();
                $return = array(
                    'html' => $this->renderView('SnowcapAdminBundle:InlineContent:preview.html.twig', array(
                        'admin' => $admin,
                        'entity' => $entity
                    ))
                );
                return new Response(json_encode($return), 201, array('content-type' => 'text/json'));
            }
        }

        $return = array(
            'html' => $this->renderView('SnowcapAdminBundle:InlineContent:create.html.twig', array(
                'admin' => $admin,
                'entity' => $entity,
                'form' => $form->createView(),
            ))
        );

        return new Response(json_encode($return), 201, array('content-type' => 'text/json'));
    }

    public function autocompleteAction($code, $input, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->getRequest()->getLocale();
        }
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        if($admin->isTranslatable()){
            $this->get('snowcap_admin')->setWorkingLocale($locale);
        }
        $return = array(
            'html' => $this->renderView('SnowcapAdminBundle:InlineContent:autocomplete.html.twig', array(
                'results' => $admin->filterAutocomplete($input),
                'admin' => $admin,
            ))
        );

        return new Response(json_encode($return), 201, array('content-type' => 'text/json'));
    }

}