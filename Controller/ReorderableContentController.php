<?php
namespace Snowcap\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Snowcap\AdminBundle\Admin\ContentAdmin;

/**
 * This controller provides basic CRUD capabilities for content models
 *
 */
class ReorderableContentController extends BaseController
{
    /**
     * Create a new content entity
     *
     * @param string $code
     * @param string $property
     * @return mixed
     */
    public function reorderAction($code, $locale)
    {
        $locale = $this->getRequest()->getLocale();
        $this->get('snowcap_admin')->setWorkingLocale($locale);
        $admin = $this->get('snowcap_admin')->getAdmin($code);
        $entities = $admin->getQueryBuilder()->orderBy('e.left')->getQuery()->getResult();

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $reorderedTree = $request->get('treeData');
            $indexedEntities = array_combine(array_map(function($entity){return $entity->getId();}, $entities), $entities);

            $traverse = function($tree, $parent = null, $index = 1, $level = 1) use($indexedEntities, &$traverse) {
                $length = count($tree);
                foreach($tree as $offset => $item) {
                    $entity = $indexedEntities[$item['metadata']['id']];
                    $isLast = ($offset === $length - 1);
                    $entity->setLeft($index++);
                    $entity->setLevel($level);
                    if(isset($item['children'])) {
                        $index = $traverse($item['children'], $item, $index, $level + 1);
                    }
                    else {
                        $entity->setRight($index++);
                    }
                    if($parent !== null) {
                        $parentEntity =  $indexedEntities[$parent['metadata']['id']];
                        $entity->setParent($parentEntity);
                        if ($isLast) {
                            $parentEntity->setRight($index++);
                        }
                    }
                }
                return $index;
            };

            $traverse($reorderedTree);
            $this->getDoctrine()->getEntityManager()->flush();
        }

        $return = array(
            'html' => $this->renderView('SnowcapAdminBundle:ReorderableContent:reorder.html.twig', array(
                'admin' => $admin,
                'entities' => $entities,
            ))
        );

        return new Response(json_encode($return), 201, array('content-type' => 'text/json'));
    }

}