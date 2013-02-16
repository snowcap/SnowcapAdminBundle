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
     * @var string
     */
    private $defaultTranslationDomain;
    /**
     * @var array
     */
    private $admins = array();

    /**
     * @param string $alias
     * @param \Snowcap\AdminBundle\Admin\AdminInterface $admin
     */
    public function registerAdmin($alias, AdminInterface $admin, array $options)
    {
        $admin->setAlias($alias);
        $admin->setOptions($options);
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
     * @param string $defaultTranslationDomain
     */
    public function setDefaultTranslationDomain($defaultTranslationDomain)
    {
        $this->defaultTranslationDomain = $defaultTranslationDomain;
    }

    /**
     * @return string
     */
    public function getDefaultTranslationDomain()
    {
        return $this->defaultTranslationDomain;
    }
}