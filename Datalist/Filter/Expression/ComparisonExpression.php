<?php

namespace Snowcap\AdminBundle\Datalist\Filter\Expression;

use Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface;

class ComparisonExpression implements ExpressionInterface {
    const OPERATOR_EQ = 'eq';
    const OPERATOR_NEQ = 'neq';
    const OPERATOR_GT = 'gt';
    const OPERATOR_GTE = 'gte';
    const OPERATOR_LT = 'lt';
    const OPERATOR_LTE = 'lte';

    /**
     * @var \Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface
     */
    private $filter;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param \Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface $filter
     * @param string $operator
     * @param mixed $value
     */
    public function __construct(DatalistFilterInterface $filter, $operator, $value) {
        if(!in_array($operator, self::getValidOperators())) {
            throw new \InvalidArgumentException(sprintf('Unknown operator "%s"', $operator));
        };

        $this->filter = $filter;
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getPropertyPath()
    {
        return $this->filter->getPropertyPath();
    }

    /**
     * @return array
     */
    static private function getValidOperators(){
        return array(
            self::OPERATOR_EQ, self::OPERATOR_NEQ, self::OPERATOR_GT, self::OPERATOR_GTE,
            self::OPERATOR_LT, self::OPERATOR_LTE
        );
    }
}