<?php
namespace Snowcap\AdminBundle\Grid;

use Doctrine\ORM\QueryBuilder;

class Content extends Base {

    protected $queryBuilder;

    protected $formFactory;

    public function getType() {
        return 'content';
    }

    public function setQueryBuilder(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function setFormFactory($formFactory)
    {
        $this->formFactory = $formFactory;
    }

    protected function processQueryBuilder(){
        
    }

    public function getData()
    {
        if(!isset($this->data)) {
            $this->processQueryBuilder();
            $this->data = $this->queryBuilder->getQuery()->getResult();
        }
        return $this->data;
    }
}