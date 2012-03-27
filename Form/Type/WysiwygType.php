<?php
namespace Snowcap\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * Markdown field type class
 * 
 */
class WysiwygType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'wysiwyg';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options) {
        return array(
            'style_file' => 'bundles/snowcapadmin/vendor/ckeditor/plugin/styles/styles/ckeditor_styles.js',
            'css_file' => 'bundles/snowcapadmin/vendor/ckeditor/contents.css',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options) {
        $builder->setAttribute('style_file', $options['style_file']);
        $builder->setAttribute('css_file', $options['css_file']);
        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form)
    {
        $view->set('style_file', $form->getAttribute('style_file'));
        $view->set('css_file', $form->getAttribute('css_file'));
        parent::buildView($view, $form);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'textarea';
    }
}