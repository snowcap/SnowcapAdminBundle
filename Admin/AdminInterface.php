<?php

namespace Snowcap\AdminBundle\Admin;

interface AdminInterface
{
    /**
     * Return the admin code
     *
     * @return string
     */
    public function getCode();

    /**
     * @return string
     */
    public function getDefaultUrl();
}