<?php
namespace Snowcap\AdminBundle\Twig\Extension;

use Symfony\Component\Form\Util\PropertyPath;

use Snowcap\AdminBundle\Datalist\DatalistInterface;
use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;
use Snowcap\AdminBundle\Datalist\ViewContext;


class DatalistExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @param \Twig_Environment $environment
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'datalist_widget' => new \Twig_Function_Method($this, 'renderDatalistWidget', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'datalist_header' => new \Twig_Function_Method($this, 'renderDatalistHeader', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'datalist_field' => new \Twig_Function_Method($this, 'renderDatalistField', array('pre_escape' => 'html', 'is_safe' => array('html'))),
        );
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\DatalistInterface $datalist
     * @return string
     */
    public function renderDatalistWidget(DatalistInterface $datalist)
    {
        $blockName = 'datalist';

        return $this->renderBlock($datalist->getOption('layout'), $blockName, array(
            'datalist' => $datalist,
        ));
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
