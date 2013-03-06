<?php
namespace Snowcap\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;

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
        $compound = function (Options $options) {
            return $options['multiple'];
        };

        $resolver
            ->setDefaults(array(
                'allow_add' => false,
                'add_label' => 'Add',
                'multiple' => false,
                'compound' => $compound,

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
        $builder->addModelTransformer(new EntityToIdTransformer($admin, $options['multiple']));

        if($options['multiple']) {
            $prototype = $builder->create('__name__', 'hidden');
            $builder->setAttribute('prototype', $prototype->getForm());

            $resizeListener = new ResizeFormListener(
                'hidden',
                array(),
                true,
                true
            );

            $builder->addEventSubscriber($resizeListener);
        }
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
        // For multiple autocomplete, we need to store textual values indexed by value, along with the prototype
        if($options['multiple']) {
            $textValues = array();
            foreach($value as $entity) {
                $textValues[$entity->getId()]= $this->buildTextValue($entity, $options);
            }
            $view->vars['text_values'] = $textValues;

            $view->vars['prototype'] = $form->getConfig()->getAttribute('prototype')->createView($view);
        }
        // For single autocompletes, just store the only textual value
        else {
            $textValue = $this->buildTextValue($value, $options);
            $view->vars['text_value'] = $textValue;
        }

        // Set the other variables
        $view->vars['multiple'] = $options['multiple'];
        $view->vars['list_url'] = $this->generateListUrl($options);
        $view->vars['allow_add'] = $options['allow_add'];
        if($options['allow_add']) {
            $view->vars['add_url'] = $this->generateAddUrl($options);
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
        return 'form';
    }

    /**
     * @param mixed $value
     * @param array $options
     * @return string
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    private function buildTextValue($value, array $options)
    {
        if(null === $value) {
            $textValue = "";
        }
        elseif(isset($options['property'])) {
            $accessor = PropertyAccess::getPropertyAccessor();
            $textValue = $accessor->getValue($value, $options['property']);
        }
        elseif(method_exists($value, '__toString')) {
            $textValue = $value->__toString();
        }
        else {
            throw new MissingOptionsException('You must provide a "property" option (or your class must implement the "__toString" method');
        }

        return $textValue;
    }

    /**
     * @param array $options
     * @return string
     */
    private function generateListUrl(array $options)
    {
        return $this->routingHelper->generateUrl(
            $this->adminManager->getAdmin($options['admin']),
            'autocompleteList',
            array('query' => '__query__', 'where' => base64_encode($options['where']), 'property' => $options['property'])
        );
    }

    /**
     * @param array $options
     * @return string
     */
    private function generateAddUrl(array $options)
    {
        return $this->routingHelper->generateUrl($this->adminManager->getAdmin($options['admin']), 'modalCreate');
    }
}