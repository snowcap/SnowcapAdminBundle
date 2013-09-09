<?php

namespace Snowcap\AdminBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;

use Snowcap\AdminBundle\Admin\ContentAdmin;

class EntityToIdTransformer implements DataTransformerInterface
{
    /**
     * @var ContentAdmin
     */
    private $admin;

    /**
     * @var bool
     */
    private $multiple;

    /**
     * @param string $field the field to filter on
     * @param string $operator the filter operator
     */
    public function __construct(ContentAdmin $admin, $multiple)
    {
        $this->admin = $admin;
        $this->multiple = $multiple;
    }

    /**
     * @param array $value
     * @return array
     */
    public function transform($value)
    {
        if ($this->multiple) {
            if (null === $value) {
                return array();
            }

            $transformedValue = array();
            foreach ($value as $entity) {
                $transformedValue []= $this->transformSingle($entity);
            }

            return $transformedValue;
        } else {
            return $this->transformSingle($value);
        }
    }

    /**
     * @param mixed $value
     * @return string
     */
    private function transformSingle($value)
    {
        if (null === $value) {
            return "";
        }

        return $value->getId();
    }

    /**
     * @param array $value
     * @return array
     */
    public function reverseTransform($value)
    {
        if(null === $value) {
            return null;
        }
        if($this->multiple) {
            $reverseTransformedValue = new ArrayCollection();
            foreach($value as $id) {
                $reverseTransformedValue[]= $this->singleReverseTransform($id);
            }

            return $reverseTransformedValue;
        }
        else {
            return $this->singleReverseTransform($value);
        }
    }

    /**
     * @param mixed $value
     * @return object
     */
    private function singleReverseTransform($value)
    {
        return $this->admin->findEntity($value);
    }
}