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
    private $sections;

    /**
     * @param array $sections
     */
    public function __construct($sections)
    {
        foreach ($sections as $sectionCode => $sectionParams) {
            $this->validateSection($sectionCode, $sectionParams);
            $adminClassName = $sectionParams['admin_class'];
            $adminInstance = new $adminClassName($sectionCode, $sectionParams, $this);
            $this->sections[$sectionCode] = $adminInstance;
        }
    }

    /**
     * Get the section admin instance for the provided code
     *
     * @param string $code
     * @return \Snowcap\AdminBundle\Admin\Base
     */
    public function getAdmin($code)
    {
        if (!array_key_exists($code, $this->sections)) {
            throw new Exception(sprintf('The admin section %s  has not been registered with the admin bundle', $code), Exception::SECTION_UNKNOWN);
        }
        return $this->sections[$code];
    }

    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param strong $sectionName
     * @param array $sectionParams
     */
    protected function validateSection($sectionName, $sectionParams)
    {
        if (!is_array($sectionParams)) {
            throw new Exception(sprintf('The parameters of the admin section "%s" have to be defined as an array, %s given', $sectionName, gettype($sectionParams)), Exception::SECTION_INVALID);
        }
        if (!array_key_exists('admin_class', $sectionParams)) {
            throw new Exception(sprintf('The admin section "%s" lacks a "admin_class" parameter.', $sectionName), Exception::SECTION_INVALID);
        }
        if (!class_exists($sectionParams['admin_class'])) {
            throw new Exception(sprintf('The admin section "%s" has an invalid "admin_class" parameter (%s).', $sectionName, $sectionParams['admin_class']), Exception::SECTION_INVALID);
        }
        $expectedParent = 'Snowcap\\AdminBundle\\Admin\\Base';
        if (!in_array($expectedParent, class_parents($sectionParams['admin_class']))) { //TODO: replace with instanceof ?
            throw new Exception(sprintf('The admin section class %s for the admin section "%s" must extend the class %s.', $sectionParams['admin_class'], $sectionName, $expectedParent), Exception::SECTION_INVALID);
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
}