<?php
namespace Snowcap\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Snowcap\AdminBundle\AdminManager;
use Snowcap\AdminBundle\Routing\Helper\ContentRoutingHelper;

/**
 * Slug field type class
 *
 */
class InlineEntityType extends AbstractType
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
     */
    public function __construct(AdminManager $adminManager, ContentRoutingHelper $routingHelper)
    {
        $this->adminManager = $adminManager;
        $this->routingHelper = $routingHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'inline_admin' => null,
            'empty_value' => '---',
            'preview' => null,
            'property' => 'id',
            'allow_add' => false,
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'allow_add' => true,
                'add_label' => 'Add'
            ))
            ->setRequired(array('admin'));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['allow_add'] = $options['allow_add'];
        if($options['allow_add']) {
            $view->vars['add_url'] = $this->routingHelper->generateUrl($this->adminManager->getAdmin($options['admin']), 'create');
            $view->vars['add_label'] = $options['add_label'];
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'snowcap_admin_inline_entity';
    }

    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'entity';
    }
}