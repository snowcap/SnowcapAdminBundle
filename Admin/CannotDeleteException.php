<?php
namespace Snowcap\AdminBundle\Admin;

use Snowcap\AdminBundle\Exception;

class CannotDeleteException extends Exception {
    const HAS_CHILDREN = 10;
}