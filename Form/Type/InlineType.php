<?php
namespace Snowcap\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Exception\FormException;

/**
 * Slug field type class
 * 
 */
class InlineType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'inline';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options) {
        return array('create_url' => null);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options) {
        if($options['create_url'] === null) {
            throw new FormException('Inline types must be given a valid create_url option');
        }
        $builder->setAttribute('create_url', $options['create_url']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form)
    {
        $view->set('create_url', $form->getAttribute('create_url'));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'choice';
    }
}