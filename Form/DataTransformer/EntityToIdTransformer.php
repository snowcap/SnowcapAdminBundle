<?php

namespace Snowcap\AdminBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

use Snowcap\AdminBundle\Admin\ContentAdmin;

class EntityToIdTransformer implements DataTransformerInterface {
    /**
     * @var ContentAdmin
     */
    private $admin;

    /**
     * @param string $field the field to filter on
     * @param string $operator the filter operator
     */
    public function __construct(ContentAdmin $admin) {
        $this->admin = $admin;
    }

    /**
     * @param array $value
     * @return array
     */
    function transform($value)
    {
        return $value->getId();
    }

    /**
     * @param array $value
     * @return array
     */
    function reverseTransform($value)
    {
        return $this->admin->findEntity($value);
    }
}