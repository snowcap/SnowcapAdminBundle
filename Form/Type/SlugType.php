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
class SlugType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'slug';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options) {
        return array('target' => 'title');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options) {
        parent::buildForm($builder, $options);
        $builder->setAttribute('target', $options['target']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form)
    {
        parent::buildView($view, $form);
        $view->set('target', $form->getAttribute('target'));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'text';
    }
}