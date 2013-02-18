<?php

namespace Snowcap\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class WidgetController extends BaseController
{
    /**
     * @Template
     */
    public function contentChangedAction()
    {
        return array();
    }
}