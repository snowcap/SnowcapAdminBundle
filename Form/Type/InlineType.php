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
    public function getDefaultOptions(array $options)
    {
        return array(
            'route' => null,
            'params' => null,
            'empty_value' => '---'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['route'] === null) {
            throw new FormException('Inline types must be given a valid route option');
        }
        if ($options['params'] === null) {
            throw new FormException('Inline types must be given a valid params option');
        }
        $builder->setAttribute('route', $options['route']);
        $builder->setAttribute('params', $options['params']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form)
    {
        $view->set('route', $form->getAttribute('route'));
        $view->set('params', $form->getAttribute('params'));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'entity';
    }
}