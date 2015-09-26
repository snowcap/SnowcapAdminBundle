<?php

namespace Snowcap\AdminBundle\Twig\Extension;

use Symfony\Component\Translation\TranslatorInterface;

use Snowcap\AdminBundle\AdminManager;
use Snowcap\AdminBundle\Admin\AdminInterface;
use Snowcap\AdminBundle\Admin\ContentAdmin;
use Snowcap\AdminBundle\Routing\Helper\ContentRoutingHelper;

/**
 * Global, general-purpose admin extension
 */
class AdminExtension extends \Twig_Extension
{
    /**
     * @var \Snowcap\AdminBundle\AdminManager
     */
    private $adminManager;

    /**
     * @var \Snowcap\AdminBundle\Routing\Helper\ContentRoutingHelper
     */
    private $contentRoutingHelper;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param \Snowcap\AdminBundle\AdminManager $adminManager
     */
    public function __construct(
        AdminManager $adminManager,
        ContentRoutingHelper $contentRoutingHelper,
        TranslatorInterface $translator
    ){
        $this->adminManager = $adminManager;
        $this->contentRoutingHelper = $contentRoutingHelper;
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('get_admin_for_entity_name', array($this, 'getAdminForEntityName')),
            new \Twig_SimpleFunction('admin', array($this, 'getAdminByCode')),
            new \Twig_SimpleFunction('admin_label', array($this, 'getAdminLabel')),
            new \Twig_SimpleFunction('admin_content_path', array($this, 'getAdminContentPath')),
            new \Twig_SimpleFunction('admin_translation_domain', array($this, 'getDefaultTranslationDomain')),
        );
    }

    /**
     * @param $code
     * @return \Snowcap\AdminBundle\Admin\AdminInterface
     */
    public function getAdminByCode($code)
    {
        return $this->adminManager->getAdmin($code);
    }

    /**
     * @param $namespace
     * @return \Snowcap\AdminBundle\Admin\ContentAdmin
     */
    public function getAdminForEntityName($namespace)
    {
        $entity = new $namespace;
        $admin = $this->adminManager->getAdminForEntity($entity);

        return $admin;
    }

    /**
     * @param \Snowcap\AdminBundle\Admin\ContentAdmin $admin
     * @param string $action
     * @param array $params
     * @return string
     */
    public function getAdminContentPath($admin, $action, array $params = array())
    {
        if(!$admin instanceof ContentAdmin) {
            $admin = $this->getAdminByCode($admin);
        }

        return $this->contentRoutingHelper->generateUrl($admin, $action, $params);
    }

    /**
     * @param \Snowcap\AdminBundle\Admin\ContentAdmin $admin
     * @param bool $plural
     * @return string
     */
    public function getAdminLabel(AdminInterface $admin, $plural = false)
    {
        $number = $plural ? 10 : 1;
        $label = $admin->getOption('label');

        return $this->translator->transChoice($label, $number, array(), $this->adminManager->getDefaultTranslationDomain());
    }

    /**
     * @return string
     */
    public function getDefaultTranslationDomain()
    {
        return $this->adminManager->getDefaultTranslationDomain();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'snowcap_admin';
    }
}
