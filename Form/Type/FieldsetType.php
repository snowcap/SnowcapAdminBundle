<?php
namespace Snowcap\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * Slug field type class
 * 
 */
class FieldsetType extends AbstractType
{
    public function getName()
    {
        return 'fieldset';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'virtual' => true,
            'legend' => null,
        );
    }



    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->setAttribute('legend', $options['legend']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form)
    {
        $view
            ->set('legend', $form->getAttribute('legend'));
    }


}