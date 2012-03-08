<?php

namespace Snowcap\AdminBundle\Datalist\View;

use Snowcap\AdminBundle\Exception;

class ThumbnailView implements ListViewInterface
{

    /**
     * @var array
     */
    protected $image;

    /**
     * @var array
     */
    protected $label;

    /**
     * @var array
     */
    protected $description;

    public function add($path, $type = null, $options = array())
    {
        switch ($type) {
            case 'image':
                $this->image = array(
                    'path' => $path,
                    'options' => $options
                );
                break;
            case 'label':
                $this->label = array(
                    'path' => $path,
                    'options' => $options
                );
                break;
            case 'description':
                $this->description = array(
                    'path' => $path,
                    'options' => $options
                );
                break;
            default:
                throw new Exception(sprintf('Unknown type "%s" for grid %s', $type, $this->name));
                break;
        }
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getName()
    {
        return 'thumbnail';
    }

}