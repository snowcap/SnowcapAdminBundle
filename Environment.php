<?php
namespace Snowcap\AdminBundle;

use Symfony\Bundle\DoctrineBundle\Registry,
    Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormFactory;

use Snowcap\AdminBundle\Form\ContentType,
    Snowcap\AdminBundle\Grid\Factory as GridFactory;

/**
 * Environment admin service
 *
 */
class Environment {
    /**
     * Admin sections configuration
     *
     * @var array
     */
    private $sections = array();
    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    private $formFactory;
    /**
     * @var \Snowcap\AdminBundle\Grid\Factory
     */
    private $gridFactory;

    private $doctrine;
    /**
     * @param $sections
     * @param \Symfony\Component\Form\FormFactory $formFactory
     * @param Grid\Factory $gridFactory
     */
    public function __construct($sections, FormFactory $formFactory, GridFactory $gridFactory, Registry $doctrine)
    {
        $this->sections = $sections;
        $this->formFactory = $formFactory;
        $this->gridFactory = $gridFactory;
        $this->doctrine = $doctrine;
    }
    /**
     * Get the content admin instance for the provided type
     *
     * @throws \Exception
     * @param string $type
     * @return \Snowcap\AdminBundle\Admin\Content
     */
    public function getAdmin($section)
    {
        if(!array_key_exists($section, $this->sections)){
            throw new \Exception('Invalid section ' . $section);
        }
        $adminParams = $this->sections[$section];
        $adminParams['section'] = $section;
        $adminClassName = $adminParams['admin_class'];
        $adminInstance = new $adminClassName($adminParams, $this->formFactory, $this->gridFactory, $this->doctrine, $this);
        return $adminInstance;
    }
    /**
     * Build a param array for navigation purposes
     *
     * @return array
     */
    public function getNavigation() {
        $navigation = array();
        foreach ($this->sections as $section_name => $config) {
            $route = '';
            $routeParams = array();
            switch($config['type']){
                case 'custom':
                    $route = $config['default_route'];
                    break;
                case 'content':
                    $route = 'content';
                    $routeParams['section'] = $section_name;
                    break;
                default:
                    throw new \ErrorException('not implemented');
                    break;
            }
            $navigation[] = array(
                'route' => $route,
                'route_params' => $routeParams,
                'section_name'=> $config['label']
            );
        }
        return $navigation;
    }
}