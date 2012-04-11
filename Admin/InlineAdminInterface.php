<?php

namespace Snowcap\AdminBundle\Admin;

interface InlineAdminInterface {

    /**
     * Return results for the provided autocomplete string
     *
     * @abstract
     * @param string $input
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function filterAutocomplete($input);

    /**
     * @abstract
     *
     * @return array
     */
    public function getPreview();
}