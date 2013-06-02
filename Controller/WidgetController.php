<?php

namespace Snowcap\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

class WidgetController extends BaseController
{
    public function deleteItemAction()
    {
        $responseData = array(
            'content' => $this->renderView('SnowcapAdminBundle:Widget:deleteItem.html.twig')
        );

        return new JsonResponse($responseData);
    }

    /**
     * @Template
     */
    public function contentChangedAction()
    {
        return array();
    }
}