<?php
namespace Snowcap\AdminBundle\Twig\Extension;

use Symfony\Component\Form\Util\PropertyPath;

use Snowcap\AdminBundle\DataList\AbstractDatalist;
use Snowcap\AdminBundle\Exception;

/**
 * Created by JetBrains PhpStorm.
 * User: edwin
 * Date: 28/08/11
 * Time: 21:47
 * To change this template use File | Settings | File Templates.
 */

class AdminExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * {@inheritdoc}
     */


    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }


    public function getFunctions()
    {
        return array(
            'list_widget' => new \Twig_Function_Method($this, 'renderList', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'list_value' => new \Twig_Function_Method($this, 'listValue', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'grid_header' => new \Twig_Function_Method($this, 'renderHeader', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'preview_widget' => new \Twig_Function_Method($this, 'renderPreview', array('pre_escape' => 'html', 'is_safe' => array('html'))),
        );
    }


    public function renderList(AbstractDatalist $list)
    {
        $loader = $this->environment->getLoader();
        /* @var \Symfony\Bundle\TwigBundle\Loader\FilesystemLoader $loader */
        $loader->addPath(__DIR__ . '/../../Resources/views/');
        $template = $this->environment->loadTemplate('list.html.twig');
        $blockName = 'list_' . $list->getView()->getName();
        if(!$template->hasBlock($blockName)) {
            throw new Exception(sprintf('The block "%s" could not be loaded whe trying to display the "%s" grid', $blockName, $list->getName()));
        }
        ob_start();
        $template->displayBlock($blockName, array(
            'list' => $list,
        ));
        $html = ob_get_clean();
        return $html;
    }

    public function listValue($row, $path, $params)
    {
        $loader = $this->environment->getLoader();
        /* @var \Symfony\Bundle\TwigBundle\Loader\FilesystemLoader $loader */
        $loader->addPath(__DIR__ . '/../../Resources/views/');

        $propertyPath = new PropertyPath($path);
        $output = $propertyPath->getValue($row);

        return $output;
    }

    public function renderHeader($property, $params)
    {
        $loader = $this->environment->getLoader();
        /* @var \Symfony\Bundle\TwigBundle\Loader\FilesystemLoader $loader */
        $loader->addPath(__DIR__ . '/../../Resources/views/');
        $template = $this->environment->loadTemplate('grid.html.twig');
        if (array_key_exists('label', $params)) {
            $output = $params['label'];
        }
        else {
            $output = $property;
        }
        ob_start();
        $template->displayBlock('header', array('output' => $output));
        $html = ob_get_clean();
        return $html;
    }

    public function renderPreview($entity, $admin, $property)
    {
        $loader = $this->environment->getLoader();
        /* @var \Symfony\Bundle\TwigBundle\Loader\FilesystemLoader $loader */

        // looking if there's some specific template in the users bundle
        $finderBundles = new \Symfony\Component\Finder\Finder();
        $finderBundles->directories()->in(__DIR__ . '/../../../../../../src')->depth('< 3')->name('*Bundle');
        foreach ($finderBundles as $bundle) {
            $finderTemplates = new \Symfony\Component\Finder\Finder();
            $finderTemplates->files()->in($bundle . '/Resources/views/')->name("preview_widgets.html.twig")->depth('< 2');
            if (count(iterator_count($finderTemplates)) > 0) {
                $loader = $this->environment->getLoader();
                /* @var \Symfony\Bundle\TwigBundle\Loader\FilesystemLoader $loader */
                $loader->addPath($bundle . '/Resources/views/');
                $template = $this->environment->loadTemplate('preview_widgets.html.twig');
                break;
            }
        }

        ob_start();
        if (!$this->renderPreviewBlock($template, $admin->getPreviewBlockName(), array('entity' => $entity))) {
            $loader->setPaths(__DIR__ . '/../../Resources/views/');
            $template = $this->environment->loadTemplate('preview_widgets.html.twig');
            $this->renderPreviewBlock($template, $admin->getPreviewBlockName(), array('entity' => $entity, 'property' => $property));
        }
        $html = ob_get_clean();
        return $html;
    }

    private function renderPreviewBlock($template, $block, $options)
    {
        if ($template === null) {
            return false;
        }

        if ($template->hasBlock($block)) {
            $template->displayBlock($block, $options);
            return true;
        }

        if ($template->hasBlock('default_preview')) {
            $template->displayBlock('default_preview', $options);
            return true;
        }
        return false;
    }

    public function getName()
    {
        return 'snowcap_admin';
    }
}
