<?php

namespace Snowcap\AdminBundle\Form\Type;

use Snowcap\AdminBundle\AdminManager;
use Snowcap\AdminBundle\Routing\Helper\ContentRoutingHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EntityType
 * @package Snowcap\AdminBundle\Form\Type
 */
class EntityType extends AbstractType
{
    /**
     * @var \Snowcap\AdminBundle\AdminManager
     */
    private $adminManager;

    /**
     * @var ContentRoutingHelper
     */
    private $routingHelper;

    /**
     * @param \Snowcap\AdminBundle\AdminManager $adminManager
     * @param ContentRoutingHelper $routingHelper
     */
    public function __construct(AdminManager $adminManager, ContentRoutingHelper $routingHelper)
    {
        $this->adminManager = $adminManager;
        $this->routingHelper = $routingHelper;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'allow_add' => false,
                'add_label' => 'Add new'
            ))
            ->setRequired(array('admin'));
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['allow_add'] = $options['allow_add'];
        if($options['allow_add']) {
            $view->vars['add_url'] = $this->routingHelper->generateUrl($this->adminManager->getAdmin($options['admin']), 'modalCreate');
            $view->vars['add_label'] = $options['add_label'];
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'snowcap_admin_entity';
    }

    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'entity';
    }
}
