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
            'allow_add' => false,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['inline_admin'] === null) {
            throw new FormException('Inline types must be given a "inline_admin" option');
        }
        $inlineAdmin = $this->environment->getAdmin($options['inline_admin']);
        if(!in_array('Snowcap\AdminBundle\Admin\InlineAdminInterface', class_implements($inlineAdmin))){
            throw new FormException('Inline types must be given a valid "inline_admin" option - ie it must implement "Snowcap\AdminBundle\Admin\InlineAdminInterface"');
        }
        $builder->setAttribute('inline_admin', $this->environment->getAdmin($options['inline_admin']));
        $builder->setAttribute('allow_add', $options['allow_add']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form)
    {
        $view->set('inline_admin', $form->getAttribute('inline_admin'));
        $view->set('data', $form->getData());//TODO: check if necessary
        $view->set('allow_add', $form->getAttribute('allow_add'));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'entity';
    }
}