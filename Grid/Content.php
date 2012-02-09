<?php
namespace Snowcap\AdminBundle\Grid;

use Snowcap\AdminBundle\Exception;
use Doctrine\ORM\QueryBuilder,
    Symfony\Component\Form\FormFactory,
    Symfony\Component\Routing\Router;

class Content extends Base {
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
                    sprintf('The "%s" grid queryBuilder leads to an invalid query (probably due to lacking select or from clauses)', $this->code),
                    Exception::GRID_INVALIDQUERYBUILDER
                );
            }
        }
        return $this->data;
    }

    public function setFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    public function add($columnName, $type = 'field', $columnParams = array())
    {
        parent::add($columnName, $type, $columnParams);
    }
}