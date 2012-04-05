<?php
namespace Snowcap\AdminBundle\Datalist;

use Symfony\Component\DependencyInjection\Reference;

use Snowcap\AdminBundle\Datalist\View\DatalistViewInterface;
use Snowcap\AdminBundle\Exception;
use Snowcap\AdminBundle\Datalist\ContentDatalist;

class DatalistFactory {

    /**
     * @var array
     */
    protected $views = array();

    /**
     * Build a new datalist
     *
     * @param string $view
     * @param string $name
     * @return ContentDatalist
     * @throws \Snowcap\AdminBundle\Exception
     */
    public function create($view, $name) {
        if(!isset($this->views[$view])){
            throw new Exception(sprintf('The view "%s" has not been registered for the datalist factory service', $view));
        }

        return new ContentDatalist($name, $this->views[$view]);
    }

    /**
     * Register a view within the service
     *
     * @param string $alias
     * @param \Symfony\Component\DependencyInjection\Reference $reference
     */
    public function addView($alias, Reference $reference) {
        $this->views[$alias] = $reference;
    }
}