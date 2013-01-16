<?php

namespace Snowcap\AdminBundle\Datalist\Filter;

use Snowcap\AdminBundle\Datalist\Filter\Expression\ExpressionInterface;
use Snowcap\AdminBundle\Datalist\Filter\Expression\CombinedExpression;

class DatalistFilterExpressionBuilder {
    /**
     * @var Expression\CombinedExpression
     */
    private $expression;

    public function __construct()
    {
        $this->expression = new CombinedExpression(CombinedExpression::OPERATOR_AND);
    }

    /**
     * @param Expression\ExpressionInterface $expression
     */
    public function add(ExpressionInterface $expression)
    {
        $this->expression->addExpression($expression);
    }

    /**
     * @return ExpressionInterface
     */
    public function getExpression()
    {
        return $this->expression;
    }
}