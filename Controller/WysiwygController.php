<?php

namespace Snowcap\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Provides controller to manage wysiwyg related content
 *
 */
class WysiwygController extends Controller
{

    /**
     * @return array
     *
     * @Template()
     */
    public function browserAction()
    {
        return array();
    }

    /**
     * @return array
     *
     * @Template()
     */
    public function uploadAction()
    {
        return array();
    }
}