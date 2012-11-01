<?php
namespace Snowcap\AdminBundle\Twig\Extension;

use Symfony\Component\Form\Util\PropertyPath;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

use Snowcap\AdminBundle\DataList\AbstractDatalist;
use Snowcap\AdminBundle\AdminManager;

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
     * @var \Snowcap\AdminBundle\AdminManager
     */
    private $adminManager;

    /**
     * @param \Snowcap\AdminBundle\AdminManager $adminManager
     */
    public function __construct(AdminManager $adminManager)
    {
        $this->adminManager = $adminManager;
    }

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
            'list_widget' => new \Twig_Function_Method($this, 'renderList', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'list_value' => new \Twig_Function_Method($this, 'listValue', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'preview_value' => new \Twig_Function_Method($this, 'previewValue', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'is_array'  => new \Twig_Function_Method($this, 'is_array', array()),
            'get_admin_for_entity_name' => new \Twig_Function_Method($this, 'getAdminForEntityName'),
        );
    }

    /**
     * Render a list widget
     *
     * @param \Snowcap\AdminBundle\DataList\AbstractDatalist $list
     * @return string
     * @throws \Exception
     */
    public function renderList(AbstractDatalist $list)
    {
        $loader = $this->environment->getLoader();
        $loader->addPath(__DIR__ . '/../../Resources/views/');
        $template = $this->environment->loadTemplate('list.html.twig');
        $blockName = 'list_' . $list->getView()->getName();
        if (!$template->hasBlock($blockName)) {
            throw new \Exception(sprintf('The block "%s" could not be loaded whe trying to display the "%s" grid', $blockName, $list->getName()));
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
    public function listValue($row, $path, $params = array())
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
     * Not obvious ? :)
     *
     * @param mixed $value
     * @return bool
     */
    public function is_array($value)
    {
        return is_array($value);
    }

    /**
     * @param mixed $data
     * @param string $path
     * @return mixed
     * @throws \Exception
     */
    private function getDataValue($data, $path)
    {
        $value = null;
        if (strpos($path, '%locale%') !== false) {
            if(!is_object($data) || !in_array('Snowcap\CoreBundle\Entity\TranslatableEntityInterface', class_implements($data))){
                throw new \Exception(sprintf('Localized paths such as %s may only be called on objects that implement the "TranslatableEntityInterface" interface', $path));
            }
            $workingLocale = 'en';
            $activeLocales = array('en');
            $mergedLocales = array_merge(array($workingLocale), array_diff($activeLocales, array($workingLocale)));
            while (!empty($mergedLocales)) {
                $testLocale = array_shift($mergedLocales);
                $propertyPath = new PropertyPath(str_replace('%locale%', $testLocale, $path));
                try {
                    $value = $propertyPath->getValue($data);
                    if($testLocale !== $workingLocale) {
                        $value = '<span class="untranslated">' . $value . ' (' . $testLocale . ')</span>';
                    }
                    break;
                }
                catch (UnexpectedTypeException $e) {
                    // do nothing
                }
            }
        }
        else {
            $propertyPath = new PropertyPath($path);
            try {
                $value = $propertyPath->getValue($data);
            }
            catch (UnexpectedTypeException $e) {
                // do nothing
            }
        }
        return $value;
    }

    public function getName()
    {
        return 'snowcap_admin';
    }

    public function getAdminForEntityName($namespace, $param = null)
    {
        $entity = new $namespace;
        $admin = $this->adminEnvironment->getAdminForEntity($entity);
        if($param === 'code') {
            return $admin->getCode();
        }
        elseif($param !== null) {
            return $admin->getParam($param);
        } else {
            return $admin;
        }
    }

}
