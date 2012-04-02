<?php

namespace Snowcap\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FileType extends AbstractType
{
    public function getName()
    {
        return 'admin_snowcap_file';
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('file', 'file')
            ->add('tags')
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Snowcap\AdminBundle\Entity\File');
    }


}