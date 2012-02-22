<?php
namespace Snowcap\AdminBundle\Grid;

use Snowcap\AdminBundle\Exception;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Router;

class ContentGrid extends AbstractGrid {
    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $queryBuilder;
    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    protected $formFactory;
    /**
     * @var \Symfony\Component\Routing\Router
     */
    protected $router;

    public function getType() {
        return 'content';
    }

    public function setQueryBuilder(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function getData()
    {
        if(!isset($this->queryBuilder) || !$this->queryBuilder instanceof QueryBuilder) {
            throw new Exception(
                sprintf('The "%s" grid must have a valid queryBuilder property (see Snowcap\AdminBundle\Grid\Content::setQueryBuilder)', $this->code),
                Exception::GRID_NOQUERYBUILDER
            );
        }
        if(!isset($this->data)) {
            try {
                $this->data = $this->queryBuilder->getQuery()->getResult();
            }
            catch(\Doctrine\ORM\Query\QueryException $e) {
                throw new Exception(
                    sprintf('The "%s" grid queryBuilder leads to an invalid query (probably due to lacking select or from clauses). The returned error was: %s', $this->code, $e->getMessage()),
                    Exception::GRID_INVALIDQUERYBUILDER
                );
            }
        }
        return $this->data;
    }
}