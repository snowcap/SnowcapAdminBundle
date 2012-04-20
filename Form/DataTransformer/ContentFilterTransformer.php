<?php

namespace Snowcap\AdminBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ContentFilterTransformer implements DataTransformerInterface {

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $operator;

    /**
     * @param string $field the field to filter on
     * @param string operator $operator the sql operator
     */
    public function __construct($field, $operator) {
        $this->field = $field;
        $this->operator = $operator;
    }

    /**
     * @param array $value
     * @return array
     */
    function transform($value)
    {
        return $value;
    }

    /**
     * @param array $value
     * @return array
     */
    function reverseTransform($value)
    {
        return array_merge($value, array(
            'field' => $this->field,
            'operator' => $this->operator,
        ));
    }

}