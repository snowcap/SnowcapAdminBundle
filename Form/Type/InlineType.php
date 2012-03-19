<?php
namespace Snowcap\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Exception\FormException;

use Snowcap\AdminBundle\Environment;

/**
 * Slug field type class
 *
 */
class InlineType extends AbstractType
{
    /**
     * @var \Snowcap\AdminBundle\Environment
     */
    private $environment;

    /**
     * @param \Snowcap\AdminBundle\Environment $environment
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

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
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form)
    {
        $view->set('inline_admin', $form->getAttribute('inline_admin'));
        $formData = $form->getData();
        $view->set('data', $formData);
        $view->set('environment', $this->environment);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'entity';
    }
}