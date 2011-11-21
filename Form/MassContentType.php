<?php
namespace Snowcap\AdminBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

class MassContentType extends AbstractType {


    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new MassContentListener($builder));
    }

    

    public function getName()
    {
        return 'masscontent';
    }
}