<?php

namespace Snowcap\AdminBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class CollectionTypeExtension extends AbstractTypeExtension
{
    public function getExtendedType()
    {
        return 'collection';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'initial_data' => null,
            'tabbable' => false,
            'property' => 'id',
        );
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        if (isset($options['initial_data'])) {
            $prototype = $builder->getAttribute('prototype');
            $prototype->setData($options['initial_data']);
            $builder->setAttribute('prototype', $prototype);
        }
        if (isset($options['tabbable'])) {
            $builder->setAttribute('tabbable', $options['tabbable']);
        }
        if (isset($options['property'])) {
            $builder->setAttribute('property', $options['property']);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form)
    {
        $view->set('tabbable', $form->getAttribute('tabbable'));
        $view->set('property', $form->getAttribute('property'));
    }

}