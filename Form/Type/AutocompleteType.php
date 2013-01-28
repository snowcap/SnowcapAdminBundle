<?php
namespace Snowcap\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Util\PropertyPath;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

use Snowcap\AdminBundle\Form\DataTransformer\EntityToIdTransformer;
use Snowcap\AdminBundle\AdminManager;
use Snowcap\AdminBundle\Routing\Helper\ContentRoutingHelper;

/**
 * Slug field type class
 *
 */
class AutocompleteType extends AbstractType
{
    /**
     * @var \Snowcap\AdminBundle\AdminManager
     */
    private $adminManager;

    /**
     * @var ContentRoutingHelper
     */
    private $routingHelper;

    /**
     * @param \Snowcap\AdminBundle\AdminManager $adminManager
     */
    public function __construct(AdminManager $adminManager, ContentRoutingHelper $routingHelper)
    {
        $this->adminManager = $adminManager;
        $this->routingHelper = $routingHelper;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'allow_add' => false,
                'add_label' => 'Add'
            ))
            ->setRequired(array('admin', 'where'))
            ->setOptional(array('property'));
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $admin = $this->adminManager->getAdmin($options['admin']);
        $builder->addModelTransformer(new EntityToIdTransformer($admin));
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $value = $form->getData();

        if(null === $value) {
            $textValue = "";
        }
        elseif(isset($options['property'])) {
            $propertyPath = new PropertyPath($options['property']);
            $textValue = $propertyPath->getValue($value);
        }
        elseif(method_exists($value, '__toString')) {
            $textValue = $value->__toString();
        }
        else {
            throw new MissingOptionsException('You must provide a "property" option (or your class must implement the "__toString" method');
        }

        $view->vars['text_value'] = $textValue;
        $view->vars['list_url'] = $this->routingHelper->generateUrl(
            $this->adminManager->getAdmin($options['admin']),
            'autocompleteList',
            array('query' => '__query__', 'where' => $options['where'], 'property' => $options['property'])
        );
        $view->vars['allow_add'] = $options['allow_add'];
        if($options['allow_add']) {
            $view->vars['add_url'] = $this->routingHelper->generateUrl($this->adminManager->getAdmin($options['admin']), 'modalCreate');
            $view->vars['add_label'] = $options['add_label'];
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'snowcap_admin_autocomplete';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'text';
    }
}