<?php
namespace Snowcap\AdminBundle;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Environment admin service
 *
 */
class Environment extends ContainerAware
{

    /**
     * @var array
     */
    private $admins = array();

    /**
     * @var string
     */
    private $bundle;

    /**
     * @var array
     */
    private $translationCatalogues;

    /**
     * @var string
     */
    private $workingLocale = null;

    /**
     * @param array $admins
     */
    public function __construct($admins)
    {
        foreach ($admins as $adminCode => $adminParams) {
            $this->validateAdmin($adminCode, $adminParams);
            $adminClassName = $adminParams['admin_class'];
            $adminInstance = new $adminClassName($adminCode, $adminParams, $this);
            $this->admins[$adminCode] = $adminInstance;
        }
    }

    /**
     * Get the admin instance for the provided code
     *
     * @param string $code
     * @return \Snowcap\AdminBundle\Admin\AbstractAdmin
     */
    public function getAdmin($code)
    {
        if (!array_key_exists($code, $this->admins)) {
            throw new Exception(sprintf('The admin section %s  has not been registered with the admin bundle', $code), Exception::ADMIN_UNKNOWN);
        }
        return $this->admins[$code];
    }

    public function getAdmins()
    {
        return $this->admins;
    }

    public function getAdminForEntity($entity)
    {
        $class = get_class($entity);
        foreach($this->admins as $adminCode => $admin) {
            if($admin instanceof \Snowcap\AdminBundle\Admin\ContentAdmin && $class === $admin->getParam('entity_class')) {
                return $admin;
            }
        }
        return null;
    }

    /**
     * @param string $adminName
     * @param array $adminParams
     */
    protected function validateAdmin($adminName, array $adminParams)
    {
        if (!is_array($adminParams)) {
            throw new Exception(sprintf('The parameters of the admin section "%s" have to be defined as an array, %s given', $adminName, gettype($adminParams)), Exception::ADMIN_INVALID);
        }
        if (!array_key_exists('admin_class', $adminParams)) {
            throw new Exception(sprintf('The admin section "%s" lacks a "admin_class" parameter.', $adminName), Exception::ADMIN_INVALID);
        }
        if (!class_exists($adminParams['admin_class'])) {
            throw new Exception(sprintf('The admin section "%s" has an invalid "admin_class" parameter (%s).', $adminName, $adminParams['admin_class']), Exception::ADMIN_INVALID);
        }
        $expectedParent = 'Snowcap\\AdminBundle\\Admin\\AbstractAdmin';
        if (!in_array($expectedParent, class_parents($adminParams['admin_class']))) { //TODO: replace with instanceof ?
            throw new Exception(sprintf('The admin section class %s for the admin section "%s" must extend the class %s.', $adminParams['admin_class'], $adminName, $expectedParent), Exception::ADMIN_INVALID);
        }
    }

    /**
     * Gets a service by id.
     *
     * @param  string $id The service id
     *
     * @return object The service
     */
    public function get($id)
    {
        return $this->container->get($id);
    }

    public function setBundle($bundle)
    {
        $this->bundle = $bundle;
    }

    public function getBundle()
    {
        return $this->bundle;
    }

    /**
     * @return array
     */
    public function getLocales()
    {
        return $this->container->getParameter('locales');
    }

    public function getLocale()
    {
        return $this->get('request')->getLocale();
    }

    /**
     * @param string $workingLocale
     */
    public function setWorkingLocale($workingLocale)
    {
        $this->workingLocale = $workingLocale;
    }

    /**
     * @return string
     */
    public function getWorkingLocale()
    {
        return $this->workingLocale;
    }

    /**
     * Sets a list of translation catalogues to use in admin
     * Format sounds like namespace\bundle\catalogname
     *
     * @param array $translationCatalogues
     */
    public function setTranslationCatalogues($translationCatalogues)
    {
        $this->translationCatalogues = $translationCatalogues;
    }

    /**
     * @return array
     */
    public function getTranslationCatalogues() {
        return $this->translationCatalogues;
    }

    /**
     * @return bool
     */
    public function hasTranslationCatalogues() {
        return (count($this->translationCatalogues) > 0);
    }
}