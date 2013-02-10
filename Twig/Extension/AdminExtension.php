<?php

namespace Snowcap\AdminBundle\Twig\Extension;

use Symfony\Component\Translation\TranslatorInterface;

use Snowcap\AdminBundle\AdminManager;
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
            'is_array'  => new \Twig_Function_Method($this, 'is_array', array()),
            'get_admin_for_entity_name' => new \Twig_Function_Method($this, 'getAdminForEntityName'),
            'admin' => new \Twig_Function_Method($this, 'getAdminByCode'),
            'admin_label' => new \Twig_Function_Method($this, 'getAdminLabel'),
            'admin_content_path' => new \Twig_Function_Method($this, 'getAdminContentPath'),
        );
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
    public function getAdminContentPath(ContentAdmin $admin, $action, array $params = array())
    {
        return $this->contentRoutingHelper->generateUrl($admin, $action, $params);
    }

    /**
     * @param \Snowcap\AdminBundle\Admin\ContentAdmin $admin
     * @param bool $plural
     * @return string
     */
    public function getAdminLabel(ContentAdmin $admin, $plural = false)
    {
        $number = $plural ? 10 : 1;
        $label = $admin->getOption('label');

        return $this->translator->transChoice($label, $number, array(), 'SnowcapAdminBundle');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'snowcap_admin';
    }
}
