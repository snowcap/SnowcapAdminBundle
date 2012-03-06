<?php
namespace Snowcap\AdminBundle\Twig\Extension;

use Symfony\Component\Form\Util\PropertyPath;

use Snowcap\AdminBundle\Grid\AbstractGrid;

/**
 * Created by JetBrains PhpStorm.
 * User: edwin
 * Date: 28/08/11
 * Time: 21:47
 * To change this template use File | Settings | File Templates.
 */
 
class AdminExtension extends \Twig_Extension {
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
            'grid_widget' => new \Twig_Function_Method($this, 'renderGrid', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'grid_cell' => new \Twig_Function_Method($this, 'renderCell', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'grid_header' => new \Twig_Function_Method($this, 'renderHeader', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'preview_widget' => new \Twig_Function_Method($this, 'renderPreview', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'form_row_type_classes' => new \Twig_Function_Method($this, 'getFormRowTypeClasses'),
        );
    }

    
    public function renderGrid(AbstractGrid $grid)
    {
        $loader = $this->environment->getLoader(); /* @var \Symfony\Bundle\TwigBundle\Loader\FilesystemLoader $loader */
        $loader->addPath(__DIR__ . '/../../Resources/views/');
        $template = $this->environment->loadTemplate('grid.html.twig');
        $gridClasses = array('grid', 'grid-' . $grid->getType());
        $gridClass = implode(' ', $gridClasses);
        $blockName = 'grid_widget_' . $grid->getType();
        ob_start();
        $template->displayBlock($blockName, array(
            'grid' => $grid,
            'grid_class' => $gridClass
        ));
        $html = ob_get_clean();
        return $html;
    }
    
    public function renderCell($entity, $path, $params)
    {
        $loader = $this->environment->getLoader(); /* @var \Symfony\Bundle\TwigBundle\Loader\FilesystemLoader $loader */
        $loader->addPath(__DIR__ . '/../../Resources/views/');
        $template = $this->environment->loadTemplate('grid.html.twig');

        $propertyPath = new PropertyPath($path);
        $output = $propertyPath->getValue($entity);

        ob_start();
        $template->displayBlock('cell', array('output' => $output));
        $html = ob_get_clean();

        return $html;
    }

    public function renderHeader($property, $params)
    {
        $loader = $this->environment->getLoader(); /* @var \Symfony\Bundle\TwigBundle\Loader\FilesystemLoader $loader */
        $loader->addPath(__DIR__ . '/../../Resources/views/');
        $template = $this->environment->loadTemplate('grid.html.twig');
        if(array_key_exists('label', $params)){
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

    public function getFormRowTypeClasses(array $types) {
        return implode(' ', array_map(function($type){
            return 'type_' . $type;
        }, $types));
    }

    public function renderPreview($entity,$admin,$property)
    {
       $loader = $this->environment->getLoader(); /* @var \Symfony\Bundle\TwigBundle\Loader\FilesystemLoader $loader */

        // looking if there's some specific template in the users bundle
        $finderBundles = new \Symfony\Component\Finder\Finder();
        $finderBundles->directories()->in(__DIR__ . '/../../../../../../src')->depth('< 3')->name('*Bundle');
        foreach($finderBundles as $bundle) {
            $finderTemplates = new \Symfony\Component\Finder\Finder();
            $finderTemplates->files()->in($bundle . '/Resources/views/')->name("preview_widgets.html.twig")->depth('< 2');
            if(count(iterator_count($finderTemplates)) > 0) {
                $loader = $this->environment->getLoader(); /* @var \Symfony\Bundle\TwigBundle\Loader\FilesystemLoader $loader */
                $loader->addPath($bundle . '/Resources/views/');
                $template = $this->environment->loadTemplate('preview_widgets.html.twig');
                break;
            }
        }

        ob_start();
        if(!$this->renderPreviewBlock($template,$admin->getPreviewBlockName(), array('entity' => $entity))) {
            $loader->setPaths(__DIR__ . '/../../Resources/views/');
            $template = $this->environment->loadTemplate('preview_widgets.html.twig');
            $this->renderPreviewBlock($template,$admin->getPreviewBlockName(), array('entity' => $entity, 'property' => $property));
        }
        $html = ob_get_clean();
        return $html;
    }

    private function renderPreviewBlock($template,$block,$options)
    {
        if($template === null) {
            return false;
        }

        if($template->hasBlock($block)) {
            $template->displayBlock($block, $options);
            return true;
        }

        if($template->hasBlock('default_preview')) {
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
