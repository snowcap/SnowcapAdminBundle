<?php

namespace Snowcap\AdminBundle\Controller;

use Snowcap\CoreBundle\Paginator\Paginator;

class LogController extends BaseController
{
    public function listAction()
    {
        $logsQuery = $this->getDoctrine()->getRepository('SnowcapAdminBundle:Log')
            ->createQueryBuilder('l')
            ->orderBy('l.createdAt','DESC')
            ->getQuery();

        $paginator = new Paginator($logsQuery, true);
        $paginator
            ->setPage($this->getRequest()->get('page'))
            ->setLimitPerPage(25);

        return $this->render('SnowcapAdminBundle:Log:list.html.twig', array(
            'paginator' => $paginator,
        ));
    }
}
