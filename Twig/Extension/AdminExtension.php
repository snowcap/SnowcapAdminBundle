<?php
namespace Snowcap\AdminBundle\Twig\Extension;

use Symfony\Component\Form\Util\PropertyPath;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

use Snowcap\AdminBundle\DataList\AbstractDatalist;
use Snowcap\AdminBundle\Exception;
use Snowcap\AdminBundle\Environment;

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
     * @var Snowcap\AdminBundle\Environment
     */
    protected $adminEnvironment;

    /**
     * @param \Snowcap\AdminBundle\Environment $environment
     */
    public function __construct(Environment $environment)
    {
        $this->adminEnvironment = $environment;
    }

    /**
     * {@inheritdoc}
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
            'list_widget' => new \Twig_Function_Method($this, 'renderList', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'list_value' => new \Twig_Function_Method($this, 'listValue', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'preview_value' => new \Twig_Function_Method($this, 'previewValue', array('pre_escape' => 'html', 'is_safe' => array('html'))),
        );
    }

    /**
     * Render a list widget
     *
     * @param \Snowcap\AdminBundle\DataList\AbstractDatalist $list
     * @return string
     * @throws \Snowcap\AdminBundle\Exception
     */
    public function renderList(AbstractDatalist $list)
    {
        $loader = $this->environment->getLoader();
        /* @var \Symfony\Bundle\TwigBundle\Loader\FilesystemLoader $loader */
        $loader->addPath(__DIR__ . '/../../Resources/views/');
        $template = $this->environment->loadTemplate('list.html.twig');
        $blockName = 'list_' . $list->getView()->getName();
        if (!$template->hasBlock($blockName)) {
            throw new Exception(sprintf('The block "%s" could not be loaded whe trying to display the "%s" grid', $blockName, $list->getName()));
        }
        ob_start();
        $template->displayBlock($blockName, array(
            'list' => $list,
        ));
        $html = ob_get_clean();
        return $html;
    }

    /**
     * Get a value to use in list widgets
     *
     * @param mixed $row
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public function listValue($row, $path, $params)
    {
        return $this->getDataValue($row, $path);
    }

    /**
     * Get a value to use in preview blocks (inline admins)
     *
     * @param mixed $entity
     * @param string $path
     * @return mixed
     */
    public function previewValue($entity, $path)
    {
        return $this->getDataValue($entity, $path);
    }

    /**
     * Get a value by the specified path on the provided array or object
     * Note that the valeu can be plain - such as "image.title" or translated
     * for example "post.translations[%locale].title"
     *
     * @param mixed $data
     * @param string $path
     * @return mixed
     * @throws \Snowcap\AdminBundle\Exception
     */
    private function getDataValue($data, $path)
    {
        $value = null;
        if (strpos($path, '%locale%') !== false) {
            if(!is_object($data) || !in_array('Snowcap\CoreBundle\Entity\TranslatableEntityInterface', class_implements($data))){
                throw new Exception(sprintf('Localized paths such as %s may only be called on objects that implement the "TranslatableEntityInterface" interface', $path));
            }
            if($this->adminEnvironment->getWorkingLocale() !== null) {
                $locale = $this->adminEnvironment->getWorkingLocale();
            }
            else {
                $locale = $currentLocale = $this->adminEnvironment->getLocale();
            }
            $activeLocales = $this->adminEnvironment->getLocales();
            $mergedLocales = array_merge(array($locale), array_diff($activeLocales, array($locale)));
            while (!empty($mergedLocales)) {
                $testLocale = array_shift($mergedLocales);
                $propertyPath = new PropertyPath(str_replace('%locale%', $testLocale, $path));
                try {
                    $value = $propertyPath->getValue($data);
                    break;
                }
                catch (UnexpectedTypeException $e) {
                    // do nothing
                }
            }
        }
        else {
            $propertyPath = new PropertyPath($path);
            $value = $propertyPath->getValue($data);
        }
        if($value === null) {
            $value = $this->adminEnvironment->get('translator')->trans('data.emptyvalue', array(), 'SnowcapAdminBundle');
        }
        return $value;
    }

    public function getName()
    {
        return 'snowcap_admin';
    }


}
