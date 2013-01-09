<?php
namespace Snowcap\AdminBundle\Twig\Extension;

use Symfony\Component\Form\Util\PropertyPath;

use Snowcap\AdminBundle\Datalist\DatalistInterface;
use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;

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
            'datalist_value' => new \Twig_Function_Method($this, 'listValue', array('pre_escape' => 'html', 'is_safe' => array('html'))),
        );
    }

    /**
     * Render a list widget
     *
     * @param DatalistInterface $list
     * @return string
     * @throws \Exception
     */
    public function renderDatalistWidget(DatalistInterface $datalist)
    {
        $blockName = 'datalist_' . $datalist->getOption('display_mode');

        return $this->renderBlock('list.html.twig', $blockName, array(
            'datalist' => $datalist,
        ));
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface $field
     * @return string
     */
    public function renderDatalistHeader(DatalistFieldInterface $field)
    {
        $blockName = 'datalist_' . $field->getDatalist()->getOption('display_mode') . '_header'; //TODO: dynamic

        return $this->renderBlock('list.html.twig', $blockName, array(
            'field' => $field,
        ));
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface $field
     * @param mixed $row
     * @return string
     */
    public function renderDatalistField(DatalistFieldInterface $field, $row)
    {
        $blockName = 'datalist_grid_field_' . $field->getType()->getName();

        $propertyPath = new PropertyPath($field->getPropertyPath());
        $value = $propertyPath->getValue($row);

        return $this->renderBlock('list.html.twig', $blockName, array(
            'value' => $value,
            'options' => $field->getOptions()
        ));
    }

    /**
     * @param string $templateName
     * @param string $blockName
     * @param array $context
     * @return string
     * @throws \Exception
     */
    private function renderblock($templateName, $blockName, array $context = array())
    {
        $loader = $this->environment->getLoader();
        $loader->addPath(__DIR__ . '/../../Resources/views/');
        $template = $this->environment->loadTemplate($templateName);

        if (!$template->hasBlock($blockName)) {
            throw new \Exception(sprintf('The block "%s" could not be loaded ', $blockName));
        }

        return $template->renderBlock($blockName, $context);
    }

    /**
     * Get a value to use in list widgets
     *
     * @param mixed $row
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public function listValue($row, $path, $params = array())
    {
        return $this->getDataValue($row, $path);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'snowcap_admin_datalist';
    }
}
