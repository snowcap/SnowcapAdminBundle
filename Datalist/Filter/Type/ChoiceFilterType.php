<?php

namespace Snowcap\AdminBundle\Datalist\Filter\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

use Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface;

class ChoiceFilterType extends AbstractFilterType
{
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setRequired(array('choices'));
    }

    public function buildForm(FormBuilderInterface $builder, DatalistFilterInterface $filter, array $options)
    {
        $builder->add($filter->getName(), 'choice', array(
            'choices' => $options['choices'],
            'label' => $options['label']
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'choice';
    }
}