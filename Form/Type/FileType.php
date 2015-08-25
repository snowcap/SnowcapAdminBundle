<?php

namespace Snowcap\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FileType
 *
 * Used in the wysiwyg file browser
 *
 * @package Snowcap\AdminBundle\Form\Type
 */
class FileType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'admin_snowcap_file';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'file')
            ->add('name', 'text')
            ->add('tags')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Snowcap\AdminBundle\Entity\File'));
    }
}