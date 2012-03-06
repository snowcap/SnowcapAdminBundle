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
        return 'snowcap_admin_inline';
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
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['inline_admin'] === null) {
            throw new FormException('Inline types must be given a valid "inline_admin" option');
        }
        $builder->setAttribute('inline_admin', $options['inline_admin']);
        $builder->setAttribute('property', $options['property']);
        $builder->setAttribute('preview', $options['preview']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form)
    {
        $view->set('inline_admin', $form->getAttribute('inline_admin'));
        $view->set('property', $form->getAttribute('property'));
        $view->set('preview', $form->getAttribute('preview'));
        $formData = $form->getData();
        $view->set('data', $formData);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'entity';
    }
}