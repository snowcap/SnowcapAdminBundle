<?php
namespace Snowcap\AdminBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Util\PropertyPath;
use Symfony\Component\Form\FormFactory;

use Snowcap\AdminBundle\Datalist\DatalistInterface;
use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;
use Snowcap\AdminBundle\Datalist\ViewContext;
use Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface;


class DatalistExtension extends \Twig_Extension implements ContainerAwareInterface
{
    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    private $formFactory;

    /**
     * @param \Symfony\Component\Form\FormFactory $formFactory
     */
    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param \Twig_Environment $environment
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'datalist_widget' => new \Twig_Function_Method($this, 'renderDatalistWidget', array('is_safe' => array('html'))),
            'datalist_header' => new \Twig_Function_Method($this, 'renderDatalistHeader', array('is_safe' => array('html'))),
            'datalist_field' => new \Twig_Function_Method($this, 'renderDatalistField', array('is_safe' => array('html'))),
            'datalist_search' => new \Twig_Function_Method($this, 'renderDatalistSearch', array('is_safe' => array('html'))),
            'datalist_filters' => new \Twig_Function_Method($this, 'renderDatalistFilters', array('is_safe' => array('html'))),
            'datalist_filter' => new \Twig_Function_Method($this, 'renderDatalistFilter', array('is_safe' => array('html')))
        );
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\DatalistInterface $datalist
     * @return string
     */
    public function renderDatalistWidget(DatalistInterface $datalist)
    {
        $blockName = 'datalist';

        $viewContext = new ViewContext();
        $datalist->getType()->buildViewContext($viewContext, $datalist, $datalist->getOptions());

        return $this->renderBlock($datalist->getOption('layout'), $blockName, $viewContext->all());
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface $field
     * @return string
     */
    public function renderDatalistHeader(DatalistFieldInterface $field)
    {
        $blockName = 'datalist_header'; //TODO: dynamic

        return $this->renderBlock($field->getDatalist()->getOption('layout'), $blockName, array(
            'label' => $field->getOption('label'),
        ));
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface $field
     * @param mixed $row
     * @return string
     */
    public function renderDatalistField(DatalistFieldInterface $field, $row)
    {
        $blockName = 'datalist_field_' . $field->getType()->getName();

        $propertyPath = new PropertyPath($field->getPropertyPath());
        $value = $propertyPath->getValue($row);

        $viewContext = new ViewContext();
        $field->getType()->buildViewContext($viewContext, $field, $value, $field->getOptions());

        return $this->renderBlock($field->getDatalist()->getOption('layout'), $blockName, $viewContext->all());
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\DatalistInterface $datalist
     * @return string
     */
    public function renderDatalistSearch(DatalistInterface $datalist)
    {
        $blockName = 'datalist_search';

        $form = $this->formFactory->createNamedBuilder('', 'form', null, array(
                'csrf_protection' => false
            ))
            ->add('search', 'search')
            ->getForm();
        $form->bind(array('search' => $this->container->get('request')->get('search', null)));

        return $this->renderblock($datalist->getOption('layout'), $blockName, array(
            'form' => $form->createView(),
            'placeholder' => $datalist->getOption('search_placeholder'),
            'submit' => $datalist->getOption('search_submit'),
        ));
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\DatalistInterface $datalist
     * @return string
     */
    public function renderDatalistFilters(DatalistInterface $datalist)
    {
        $blockName = 'datalist_filters';

        return $this->renderblock($datalist->getOption('layout'), $blockName, array(
            'filters' => $datalist->getFilters(),
            'submit' => $datalist->getOption('filter_submit'),
        ));
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\DatalistInterface $datalist
     * @return string
     */
    public function renderDatalistFilter(DatalistFilterInterface $filter)
    {
        $blockName = 'datalist_filter_' . $filter->getType()->getName();
        $childForm = $filter->getDatalist()->getFilterForm()->get($filter->getName());

        return $this->renderblock($filter->getDatalist()->getOption('layout'), $blockName, array(
            'form' => $childForm->createView(),
        ));
    }

    /**
     * @param string $layout
     * @param string $blockName
     * @param array $context
     * @return string
     * @throws \Exception
     */
    private function renderblock($layout, $blockName, array $context = array())
    {
        $templateName = 'datalist_' . $layout . '_layout.html.twig';
        $loader = $this->environment->getLoader();
        $loader->addPath(__DIR__ . '/../../Resources/views/Datalist');
        $template = $this->environment->loadTemplate($templateName);

        if (!$template->hasBlock($blockName)) {
            throw new \Exception(sprintf('The block "%s" could not be loaded ', $blockName));
        }

        return $template->renderBlock($blockName, $context);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'snowcap_admin_datalist';
    }
}
