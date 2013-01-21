<?php

namespace Snowcap\AdminBundle\Datalist\Datasource;

use Symfony\Component\Form\Util\PropertyPath;

use Snowcap\CoreBundle\Paginator\ArrayPaginator;
use Snowcap\AdminBundle\Datalist\Filter\Expression\ExpressionInterface;
use Snowcap\AdminBundle\Datalist\Filter\Expression\CombinedExpression;
use Snowcap\AdminBundle\Datalist\Filter\Expression\ComparisonExpression;

class ArrayDatasource extends AbstractDatasource
{
    /**
     * @var bool
     */
    private $initialized = false;

    /**
     * @var array
     */
    private $items = array();

    /**
     * @param array $items
     */
    public function __construct(array $items, array $options = array())
    {
        parent::__construct($options);

        $this->items = $items;
    }

    /**
     * @return \Snowcap\CoreBundle\Paginator\ArrayPaginator
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

    protected function initialize()
    {
        if ($this->initialized) {
            return;
        }

        $items = $this->items;
        $query = $this->searchQuery;
        // Handle search
        if (isset($this->searchQuery)) {
            if (!isset($this->options['search'])) {
                throw new \Exception('Missing "search" option');
            }

            $search = $this->options['search'];
            if (is_string($search)) {
                $search = array($search);
            }
            if (is_array($search)) {
                $items = array_filter(
                    $items,
                    function ($item) use ($search, $query) {
                        foreach ($search as $searchField) {
                            if (isset($item[$searchField]) && $item[$searchField] === $query) {
                                return true;
                            }
                        }

                        return false;
                    }

                );
            } else {
                throw new \InvalidArgumentException(sprintf(
                    'Unexpected type "%s" for "search" options',
                    gettype($search)
                ));
            }
        }

        // Handle filters
        if (isset($this->filterExpression)) {
            $filterCallback = $this->buildExpressionCallback($this->filterExpression);
            $items = array_filter($items, $filterCallback);
        }

        // Handle pagination
        if (isset($this->limitPerPage)) {
            $paginator = new ArrayPaginator($items);
            $paginator
                ->setLimitPerPage($this->limitPerPage)
                ->setRangeLimit($this->rangeLimit)
                ->setPage($this->page);

            $this->iterator = $paginator->getIterator();
            $this->paginator = $paginator;
        } else {
            $this->iterator = new \ArrayIterator($items);
            $this->paginator = null;
        }

        $this->initialized = true;
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\Filter\Expression\ExpressionInterface $expression
     * @return callable
     * @throws \InvalidArgumentException
     */
    private function buildExpressionCallback(ExpressionInterface $expression)
    {
        // If we have a combined expression ("AND" / "OR")
        if ($expression instanceof CombinedExpression) {
            $function = $this->buildCombinedExpressionCallback($expression);
        } elseif ($expression instanceof ComparisonExpression) {
            $function = $this->buildComparisonExpressionCallback($expression);
        }
        else {
            throw new \InvalidArgumentException(sprintf('Cannot handle expression of class "%s"', get_class($expression)));
        }

        return $function;
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\Filter\Expression\CombinedExpression $expression
     * @return callable
     * @throws \UnexpectedValueException
     */
    private function buildCombinedExpressionCallback(CombinedExpression $expression)
    {
        $tests = array();
        foreach ($expression->getExpressions() as $subExpression) {
            $tests [] = $this->buildExpressionCallback($subExpression);
        }
        $operator = $expression->getOperator();
        // If we have a "AND" expression, return a function testing that all sub-expressions succeed
        if (CombinedExpression::OPERATOR_AND === $operator) {
            $function = function ($item) use ($tests) {
                foreach ($tests as $test) {
                    if (!call_user_func($test, $item)) {
                        return false;
                    }
                }

                return true;
            };
        }
        // If we have a "OR" expression, return a function testing that at least one sub-expression succeeds
        elseif (CombinedExpression::OPERATOR_OR === $operator) {
            $function = function ($item) use ($tests) {
                foreach ($tests as $test) {
                    if (call_user_func($test, $item)) {
                        return true;
                    }
                }
                return false;
            };
        }
        else {
            throw new \UnexpectedValueException(sprintf('Unknown operator "%s"', $operator));
        }

        return $function;
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\Filter\Expression\ComparisonExpression $expression
     * @return callable
     * @throws \UnexpectedValueException
     */
    private function buildComparisonExpressionCallback(ComparisonExpression $expression)
    {
        $function = function($item) use($expression) {
            $propertyPath = new PropertyPath($expression->getPropertyPath());
            $value = $propertyPath->getValue($item);
            $comparisonValue = $expression->getValue();
            $operator = $expression->getOperator();

            switch($operator) {
                case ComparisonExpression::OPERATOR_EQ:
                    $result = $value === $comparisonValue;
                    break;
                case ComparisonExpression::OPERATOR_NEQ:
                    $result = $value !== $comparisonValue;
                    break;
                case ComparisonExpression::OPERATOR_GT:
                    $result = $value > $comparisonValue;
                    break;
                case ComparisonExpression::OPERATOR_GTE:
                    $result = $value >= $comparisonValue;
                    break;
                case ComparisonExpression::OPERATOR_LT:
                    $result = $value < $comparisonValue;
                    break;
                case ComparisonExpression::OPERATOR_LTE:
                    $result = $value <= $comparisonValue;
                    break;
                default:
                    throw new \UnexpectedValueException(sprintf('Unknown operator "%s"', $operator));
                    break;
            }

            return $result;
        };

        return $function;
    }
}