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


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $admin = $this->adminManager->getAdmin($options['admin']);
        $builder->addModelTransformer(new EntityToIdTransformer($admin));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $value = $form->getData();

        if(isset($options['property'])) {
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
            array('query' => '__query__')
        );
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setRequired(array('admin'))
            ->setOptional(array('property'));
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'snowcap_admin_autocomplete';
    }
}