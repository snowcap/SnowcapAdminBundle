<?php
namespace Snowcap\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Router;
/**
 * Markdown field type class
 * 
 */
class WysiwygType extends AbstractType
{

    /** @var Router */
    private $router;

    public function __construct(Router $router) {
        $this->router = $router;
    }

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
            'wysiwyg_config' => '/bundles/snowcapadmin/js/ckeditor_config.js',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options) {
        $builder->setAttribute('wysiwyg_config', $options['wysiwyg_config']);
        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form)
    {
        $view->set('wysiwyg_config', $form->getAttribute('wysiwyg_config'));
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