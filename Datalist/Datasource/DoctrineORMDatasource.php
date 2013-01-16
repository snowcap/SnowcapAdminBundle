<?php

namespace Snowcap\AdminBundle\Datalist\Datasource;

use Doctrine\ORM\QueryBuilder;

use Snowcap\CoreBundle\Paginator\DoctrineORMPaginator;
use Snowcap\AdminBundle\Datalist\Filter\Expression\ExpressionInterface;
use Snowcap\AdminBundle\Datalist\Filter\Expression\CombinedExpression;
use Snowcap\AdminBundle\Datalist\Filter\Expression\ComparisonExpression;

class DoctrineORMDatasource extends AbstractDatasource
{
    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var bool
     */
    private $initialized = false;

    /**
     * @var \Traversable
     */
    private $iterator;

    /**
     * @var DoctrineORMPaginator
     */
    private $paginator;

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder, array $options = array())
    {
        parent::__construct($options);

        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return \Snowcap\CoreBundle\Paginator\DoctrineORMPaginator
     */
    public function getPaginator()
    {
        $this->initialize();

        return $this->paginator;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        $this->initialize();

        return $this->iterator;
    }

    /**
     * Load the collection
     *
     */
    private function initialize()
    {
        if($this->initialized) {
            return;
        }

        // Handle search
        if(isset($this->searchQuery)) {
            if(!isset($this->options['search'])) {
                throw new \Exception('Missing "search" option');
            }

            $search = $this->options['search'];
            if(is_string($search)) {
                $search = array($search);
            }
            if(is_array($search)) {
                foreach($search as $searchField) {
                    $this->queryBuilder->orWhere($this->queryBuilder->expr()->like($searchField, ':query'));
                }
                $this->queryBuilder->setParameter('query', '%'. $this->searchQuery . '%');
            }
            else {
                throw new \InvalidArgumentException(sprintf('Unexpected type "%s" for "search" options', gettype($search)));
            }
        }

        // Handle filters
        if(isset($this->filterExpression)) {
            $queryBuilderExpression = $this->buildQueryBuilderExpression($this->filterExpression);
            $this->queryBuilder->andWhere($queryBuilderExpression);
        }

        // Handle pagination
        if(isset($this->limitPerPage)) {
            $paginator = new DoctrineORMPaginator($this->queryBuilder->getQuery());
            $paginator
                ->setLimitPerPage($this->limitPerPage)
                ->setRangeLimit($this->rangeLimit)
                ->setPage($this->page);
            $this->iterator = $paginator->getIterator();
            $this->paginator = $paginator;
        }
        else {
            $items = $this->queryBuilder->getQuery()->getResult();
            $this->iterator = new \ArrayIterator($items);
            $this->paginator = null;
        }

        $this->initialized = true;
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\Filter\Expression\ExpressionInterface $expression
     * @return \Doctrine\ORM\Query\Expr\Andx|\Doctrine\ORM\Query\Expr\Comparison|\Doctrine\ORM\Query\Expr\Orx
     * @throws \InvalidArgumentException
     */
    private function buildQueryBuilderExpression(ExpressionInterface $expression)
    {
        // If we have a combined expression ("AND" / "OR")
        if ($expression instanceof CombinedExpression) {
            $queryBuilderExpression = $this->buildQueryBuilderCombinedExpression($expression);
        } elseif ($expression instanceof ComparisonExpression) {
            $queryBuilderExpression = $this->buildQueryBuilderComparisonExpression($expression);
        }
        else {
            throw new \InvalidArgumentException(sprintf('Cannot handle expression of class "%s"', get_class($expression)));
        }

        return $queryBuilderExpression;
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\Filter\Expression\CombinedExpression $expression
     * @return \Doctrine\ORM\Query\Expr\Andx|\Doctrine\ORM\Query\Expr\Orx
     * @throws \UnexpectedValueException
     */
    private function buildQueryBuilderCombinedExpression(CombinedExpression $expression) {
        $queryBuilderSubExpressions = array();
        foreach ($expression->getExpressions() as $subExpression) {
            $queryBuilderSubExpressions [] = $this->buildQueryBuilderExpression($subExpression);
        }
        $operator = $expression->getOperator();
        if(CombinedExpression::OPERATOR_AND === $operator) {
            $expr = $this->queryBuilder->expr()->andX();
        }
        elseif(CombinedExpression::OPERATOR_OR === $operator) {
            $expr = $this->queryBuilder->expr()->orX();
        }
        else {
            throw new \UnexpectedValueException(sprintf('Unknown operator "%s"', $operator));
        }
        $expr->addMultiple($queryBuilderSubExpressions);

        return $expr;
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\Filter\Expression\ComparisonExpression $expression
     * @return \Doctrine\ORM\Query\Expr\Comparison
     * @throws \UnexpectedValueException
     */
    private function buildQueryBuilderComparisonExpression(ComparisonExpression $expression) {
        $propertyPath = $expression->getPropertyPath();
        $placeholder=  ':' . str_replace('.', '_', $expression->getPropertyPath());
        $comparisonValue = $expression->getValue();
        $operator = $expression->getOperator();

        switch($operator) {
            case ComparisonExpression::OPERATOR_EQ:
                $expr = $this->queryBuilder->expr()->eq($propertyPath, $placeholder);
                break;
            case ComparisonExpression::OPERATOR_NEQ:
                $expr = $this->queryBuilder->expr()->neq($propertyPath, $placeholder);
                break;
            case ComparisonExpression::OPERATOR_GT:
                $expr = $this->queryBuilder->expr()->gt($propertyPath, $placeholder);
                break;
            case ComparisonExpression::OPERATOR_GTE:
                $expr = $this->queryBuilder->expr()->gte($propertyPath, $placeholder);
                break;
            case ComparisonExpression::OPERATOR_LT:
                $expr = $this->queryBuilder->expr()->lt($propertyPath, $placeholder);
                break;
            case ComparisonExpression::OPERATOR_LTE:
                $expr = $this->queryBuilder->expr()->lte($propertyPath, $placeholder);
                break;
            default:
                throw new \UnexpectedValueException(sprintf('Unknown operator "%s"', $operator));
                break;
        }

        $this->queryBuilder->setParameter($placeholder, $comparisonValue);

        return $expr;
    }
}