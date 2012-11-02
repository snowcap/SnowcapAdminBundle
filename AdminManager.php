<?php

namespace Snowcap\AdminBundle;

use Snowcap\AdminBundle\Admin\AdminInterface;

/**
 * Environment admin service
 *
 */
class AdminManager
{
    /**
     * @var array
     */
    private $admins = array();

    /**
     * @param string $alias
     * @param \Snowcap\AdminBundle\Admin\AdminInterface $admin
     */
    public function registerAdmin($alias, AdminInterface $admin)
    {
        $admin->setAlias($alias);
        $this->admins[$alias] = $admin;
    }

    /**
     * @param $alias
     * @return \Snowcap\AdminBundle\Admin\AdminInterface
     * @throws \InvalidArgumentException
     */
    public function getAdmin($alias)
    {
        if (!array_key_exists($alias, $this->admins)) {
            throw new \InvalidArgumentException(sprintf('The admin section %s  has not been registered with the admin bundle', $alias));
        }
        return $this->admins[$alias];
    }

    /**
     * @return array
     */
    public function getAdmins()
    {
        return $this->admins;
    }

    public function getAdminForEntity($entity) //TODO: remove ?
    {
        $class = get_class($entity);
        foreach($this->admins as $adminCode => $admin) {
            if($admin instanceof \Snowcap\AdminBundle\Admin\ContentAdmin && $class === $admin->getEntityClass()) {
                return $admin;
            }
        }
        return null;
    }

    /**
     * @param string $adminName
     * @param array $adminParams
     *
     * TODO: remove OR move ?
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
}