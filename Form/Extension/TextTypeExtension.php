<?php

namespace Snowcap\AdminBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

class TextTypeExtension extends AbstractTypeExtension {
    /**
     * @return string
     */
    public function getExtendedType()
    {
        return 'text';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setOptional(array('list_url'));
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if(isset($options['list_url'])) {
            $view->vars['text_autocomplete'] = true;
            $view->vars['list_url'] = $options['list_url'];
        }
        else {
            $view->vars['text_autocomplete'] = false;
        }
    }
}