<?php

namespace Snowcap\AdminBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class CollectionTypeExtension
 * @package Snowcap\AdminBundle\Form\Extension
 */
class CollectionTypeExtension extends AbstractTypeExtension
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return string
     */
    public function getExtendedType()
    {
        return 'collection';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'confirm_delete' => false,
                'confirm_delete_url' => $this->router->generate('snowcap_admin_widget_delete_item')
            ));
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['confirm_delete']) {
            $view->vars['confirm_delete_url'] = $options['confirm_delete_url'];
        };
        $view->vars['confirm_delete'] = $options['confirm_delete'];
    }
}