<?php

namespace Snowcap\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * Class SlugType
 *
 * @package Snowcap\AdminBundle\Form\Type
 */
class SlugType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'snowcap_admin_slug';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setRequired(array('target'))
            ->setAllowedTypes(array(
                'target' => 'string'
            ));
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['target'] = $options['target'];
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'text';
    }
}