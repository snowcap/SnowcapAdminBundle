<?php

namespace Snowcap\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FileType
 *
 * Used in the wysiwyg file browser
 *
 * @package Snowcap\AdminBundle\Form\Type
 */
class FileType extends AbstractType
{
    public function getName()
    {
        return 'admin_snowcap_file';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'file')
            ->add('name', 'text')
            ->add('tags')
        ;

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Snowcap\AdminBundle\Entity\File'));
    }


}