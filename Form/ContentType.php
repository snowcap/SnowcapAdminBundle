<?php

namespace Snowcap\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Base Form type for admin content management
 * 
 */
class ContentType extends AbstractType
{
    protected $name = 'content';

    protected $fields = array();
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        foreach($this->fields as $fieldName => $fieldParams){
            $type = isset($fieldParams['type']) ? $fieldParams['type'] : null;
            $options = isset($fieldParams['options']) ? $fieldParams['options'] : array();
            $builder->add($fieldName, $type, $options);
        }
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'fields' => array(),
            'name' => null
        );
    }

	/**
     * {@inheritdoc}
     */
	public function getName()
	{
		return $this->name;
	}

    public function setName($name){
        $this->name = $name;
        return $this;
    }

    public function addField($fieldName, $fieldOptions = array()) {
        $this->fields[$fieldName] = $fieldOptions;
        return $this;
    }
}