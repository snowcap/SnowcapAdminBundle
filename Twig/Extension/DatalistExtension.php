<?php
namespace Snowcap\AdminBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormFactory;

use Snowcap\AdminBundle\Datalist\DatalistInterface;
use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;
use Snowcap\AdminBundle\Datalist\ViewContext;
use Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface;
use Snowcap\AdminBundle\Datalist\Action\DatalistActionInterface;
use Snowcap\AdminBundle\Twig\TokenParser\DatalistThemeTokenParser;


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
     * @var string
     */
    private $defaultTheme = 'SnowcapAdminBundle:Datalist:datalist_grid_layout.html.twig';

    /**
     * @var array
     */
    private $themes;

    /**
     * @param \Symfony\Component\Form\FormFactory $formFactory
     */
    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
        $this->themes = new \SplObjectStorage();
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
            'datalist_field' => new \Twig_Function_Method($this, 'renderDatalistField', array('is_safe' => array('html'))),
            'datalist_search' => new \Twig_Function_Method($this, 'renderDatalistSearch', array('is_safe' => array('html'))),
            'datalist_filters' => new \Twig_Function_Method($this, 'renderDatalistFilters', array('is_safe' => array('html'))),
            'datalist_filter' => new \Twig_Function_Method($this, 'renderDatalistFilter', array('is_safe' => array('html'))),
            'datalist_action' => new \Twig_Function_Method($this, 'renderDatalistAction', array('is_safe' => array('html')))
        );
    }

    /**
     * @return array
     */
    public function getTokenParsers()
    {
        return array(new DatalistThemeTokenParser());
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\DatalistInterface $datalist
     * @return string
     */
    public function renderDatalistWidget(DatalistInterface $datalist)
    {
        $blockNames = array($datalist->getType()->getBlockName(), '_' . $datalist->getName() . '_datalist');

        $viewContext = new ViewContext();
        $datalist->getType()->buildViewContext($viewContext, $datalist, $datalist->getOptions());

        return $this->renderBlock($datalist, $blockNames, $viewContext->all());
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface $field
     * @param mixed $row
     * @return string
     */
    public function renderDatalistField(DatalistFieldInterface $field, $row)
    {
        $blockNames = array(
            'datalist_field_' . $field->getType()->getBlockName(),
            '_' . $field->getDatalist()->getName() . '_' . $field->getName() . '_field',
        );

        $accessor = PropertyAccess::getPropertyAccessor();
        try {
            $value = $accessor->getValue($row, $field->getPropertyPath());
        } catch (InvalidPropertyException $e) {
            if (is_object($row) && !$field->getDatalist()->hasOption('data_class')) {
                $message = sprintf('Missing "data_class" option');
            } else {
                $message = sprintf('unknown property "%s"', $field->getPropertyPath());
            }
            throw new \UnexpectedValueException($message);
        } catch (UnexpectedTypeException $e) {
            $value = null;
        }

        $viewContext = new ViewContext();
        $field->getType()->buildViewContext($viewContext, $field, $value, $field->getOptions());

        return $this->renderBlock($field->getDatalist(), $blockNames, $viewContext->all());
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\DatalistInterface $datalist
     * @return string
     */
    public function renderDatalistSearch(DatalistInterface $datalist)
    {
        $blockNames = array(
            'datalist_search',
            '_' . $datalist->getName() . '_search',
        );

        return $this->renderblock($datalist, $blockNames, array(
            'form' => $datalist->getSearchForm()->createView(),
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
        $blockNames = array(
            'datalist_filters',
            '_' . $datalist->getName() . '_filters'
        );

        return $this->renderblock($datalist, $blockNames, array(
            'filters' => $datalist->getFilters(),
            'submit' => $datalist->getOption('filter_submit'),
            'reset' => $datalist->getOption('filter_reset'),
            'url' => $this->container->get('request')->getPathInfo()
        ));
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\DatalistInterface $datalist
     * @return string
     */
    public function renderDatalistFilter(DatalistFilterInterface $filter)
    {
        $blockNames = array(
            'datalist_filter_' . $filter->getType()->getBlockName(),
            '_' . $filter->getDatalist()->getName() . '_' . $filter->getName() . '_filter'
        );
        $childForm = $filter->getDatalist()->getFilterForm()->get($filter->getName());

        return $this->renderblock($filter->getDatalist(), $blockNames, array(
            'form' => $childForm->createView(),
        ));
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\Action\DatalistActionInterface $action
     * @param mixed $item
     * @return string
     */
    public function renderDatalistAction(DatalistActionInterface $action, $item)
    {
        $blockNames = array(
            'datalist_action_' . $action->getType()->getName(),
            '_' . $action->getDatalist()->getName() . '_' . $action->getName() . '_action'
        );

        $viewContext = new ViewContext();
        $action->getType()->buildViewContext($viewContext, $action, $item, $action->getOptions());

        return $this->renderblock(
            $action->getDatalist(),
            $blockNames,
            $viewContext->all()
        );
    }

    /**
     * @param string $layout
     * @param string $blockName
     * @param array $context
     * @return string
     * @throws \Exception
     */
    private function renderblock(DatalistInterface $datalist, array $blockNames, array $context = array())
    {
        $templateNames = $this->getTemplateNames($datalist);
        foreach($templateNames as $templateName) {
            $template = $this->environment->loadTemplate($templateName);
            do {
                foreach($blockNames as $blockName) {
                    if ($template->hasBlock($blockName)) {
                        return $template->renderBlock($blockName, $context);
                    }
                }
            }
            while(($template = $template->getParent($context)) !== false);
        }

        throw new \Exception(sprintf('No block found (tried to find %s)', implode(',', $blockNames)));
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\DatalistInterface $datalist
     * @return array
     */
    private function getTemplateNames(DatalistInterface $datalist)
    {
        if(isset($this->themes[$datalist])){
            return $this->themes[$datalist];
        }

        return array($this->defaultTheme);
    }

    /**
     * @param string $theme
     */
    public function setTheme(DatalistInterface $datalist, $ressources)
    {
        $this->themes[$datalist] = $ressources;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'snowcap_admin_datalist';
    }
}
