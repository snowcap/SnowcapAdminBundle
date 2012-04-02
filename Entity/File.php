<?php

namespace Snowcap\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Snowcap\CoreBundle\Doctrine\Mapping as SnowcapCore;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Snowcap\AdminBundle\Entity
 *
 * @ORM\Table(name="snowcap_admin_file")
 * @ORM\Entity
 */
class File {
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    protected $path;

    /**
     * @var string
     * @ORM\Column(name="tags", type="string", length=255, nullable=true)
     */
    protected $tags;

    /**
     * @var UploadedFile
     *
     * @Assert\File(maxSize="6000000")
     * @SnowcapCore\File(path="uploads/images", mappedBy="path")
     */
    public $file;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }
}