<?php

namespace Snowcap\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WysiwygType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'snowcap_admin_wysiwyg';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'wysiwyg_config' => '/bundles/snowcapadmin/js/ckeditor_config.js'
            ))
            ->setAllowedTypes(array(
                'wysiwyg_config' => 'string'
            ));
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['wysiwyg_config'] = $options['wysiwyg_config'];
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'textarea';
    }
}