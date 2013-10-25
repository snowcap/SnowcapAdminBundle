<?php

namespace Snowcap\AdminBundle\Form\Type;

use Snowcap\AdminBundle\Form\EventListener\MultiUploadSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MultiUploadType extends AbstractType
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * Constructor
     *
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new MultiUploadSubscriber($this->rootDir, $options['dst_dir']));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['type'] = $options['type'];
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setRequired(array('dst_dir'))
            ->setAllowedTypes(array(
                'dst_dir' => array('string', 'callable'),
            ))
            ->setDefaults(array(
                'type' => 'snowcap_admin_multiupload_url',
            )
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'snowcap_admin_multiupload';
    }

    /**
     * Returns the name of the parent type
     *
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'collection';
    }
}
